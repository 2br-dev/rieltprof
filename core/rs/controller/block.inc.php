<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller;

use \RS\Debug\Mode as DebugMode;
use RS\Performance\Timing;

/**
 * Этот класс должен быть родителем клиентского контроллера модуля.
 */
abstract class Block extends AbstractClient
{
    const BLOCK_ID_PARAM = '_block_id'; //Параметр в котором передается идентификатор блока

    const BLOCK_PATH_PARAM = 'tplpath';   //Реальный путь к шаблону блока
    const BLOCK_NUM_PARAM = 'num';       //Порядковый номер вставленого блока в шаблоне
    const BLOCK_LOADED_FROM_DB_PARAM = 'params_loaded_from_db'; // Идентификатор того что в блоке, добавленном в шаблоне, параметры загружены из БД (содержит ID объекта SectionModule, хранящего параметры блока)
    const BLOCK_INSERT_CONTEXT = 'theme_context'; //Показывает контекст, в котором был вызван {moduleinsert}

    protected static $controller_title = '';       //Краткое название контроллера
    protected static $controller_description = ''; //Описание контроллера

    /**
     * Каждый блок должен назначить себе уникальную переменую для определения действия,
     * иначе будет выполняться только actionIndex
     *
     * @var string
     */
    protected $action_var = null;

    /**
     * Параметры, которые должны быть сохранены в кэше для блоков данного класса.
     * Параметры сохраняются, чтобы при обращении к блок-контроллеру напрямую в переменной $this->param были установленные раннее значения.
     * Для корректной загрузки параметров в дальнейшем у каждого блока на странице появляется _block_id. Его нужно указывать при обращении
     * напрямую к блок-контроллеру
     *
     * @var array
     */
    protected $store_params;

    /**
     * Ключ, по которому хранятся значения параметров для блока в кэше
     *
     * @var string
     */
    protected $store_key;

    /**
     * Block constructor.
     * @param array $param
     */
    function __construct($param = [])
    {
        $param = $this->appendParamsFromDb($param);
        parent::__construct($param);

        if (isset($this->param['_rendering_mode'])) {
            $block_id_hash = $this->getBlockId();
            //Отключаем возврат данных в json, если блок вставлен в шаблоне
            $this->result->checkAjaxOutput(false);
        } else {
            if (isset($param[self::BLOCK_ID_PARAM])) {
                $block_id_hash = $param[self::BLOCK_ID_PARAM];
            } else {
                $block_id_hash = $this->url->request(self::BLOCK_ID_PARAM, TYPE_STRING);
            }
        }

        $cache = \RS\Cache\Manager::obj()->tags([CACHE_TAG_BLOCK_PARAM]);
        $this->store_key = $cache->prepareClass('block_cache_' . get_class($this) . $block_id_hash . $cache->getTagsKey());

        $this->view->assign('_block_id', $block_id_hash);
        if (!isset($this->param['_rendering_mode']) && $block_id_hash) {
            $this->loadStoredParams();
            $this->view->assign('param', $this->getParam());
        }
    }

    /**
     * Добавляет необходимые параметры из БД для блоков, добавленных не по сетке в клиентской части сайта
     *
     * @param array $param - Параметры по умолчанию
     * @return array
     */
    protected function appendParamsFromDb($param)
    {

        if (isset($param['generate_by_template']) && $param['generate_by_template'] && empty($param['skip_load_params_from_db'])) {
            //В клиентской части пытаемся загрузить параметры для блока из базы
            // данных для шаблонов не по сетке
            $param = array_merge($param, self::getClientDbParams($param));
        }

        return $param;
    }

    /**
     * Возвращает из базы данных значения сохраненных параметров для блока,
     * добавленного не по сетке в клиентской части сайта
     * В param должны присутствовать ключи: generate_by_template, theme_context
     *
     * @param array $param - параметры по умолчанию для блока
     * @return array
     */
    protected static function getClientDbParams($param)
    {
        static $cache_section_module = null;
        $template_block_id = $param[Block::BLOCK_ID_PARAM];

        if ($cache_section_module === null) {
            /** @var \Templates\Model\Orm\SectionModule[] $section_modules */
            $cache_section_module = [];

            try {
                //Получаем текущий контекст
                $cache_section_module = (new \RS\Orm\Request())
                    ->from(new \Templates\Model\Orm\SectionModule())
                    ->where([
                        'context' => $param[Block::BLOCK_INSERT_CONTEXT]
                    ])
                    ->objects(null, 'template_block_id');

            } catch (\RS\Db\Exception $e) {} //Необходимо для корректного обновления, когда core обновлен, а модуль templates - еще нет
        }

        if (isset($cache_section_module[$template_block_id])) {
            $section_module = $cache_section_module[$template_block_id];
            $db_param = $section_module->getParams(false);
            $db_param[Block::BLOCK_LOADED_FROM_DB_PARAM] = $section_module['id'];
            return $db_param;
        }

        return [];
    }

    /**
     * Загружает параметры из кэша
     */
    protected function loadStoredParams()
    {
        if (\RS\Cache\Manager::obj()->exists($this->store_key)) {
            $loaded_params = \RS\Cache\Manager::obj()->read($this->store_key);
            $this->param += $loaded_params;
        } else {
            $this->e404();
        }
    }

    /**
     * Возвращает информацию о текущем контроллере.
     *
     * @param string $key - параметр информации, который нужно вернуть
     * @return array
     */
    public static function getInfo($key = null)
    {
        $info = [
            'title' => t(static::$controller_title),
            'description' => t(static::$controller_description)
        ];
        return $key ? $info[$key] : $info;
    }

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return \RS\Orm\ControllerParamObject | false
     */
    public function getParamObject()
    {
        return false;
    }

    /**
     * Выполняет action(действие) текущего контроллера, возвращает результат действия
     * Также помещает в кэш установленые настройки данного блока
     *
     * @param boolean $returnAsIs - возвращать как есть. Если true, то метод будет возвращать точно то,
     * что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed
     * @throws Exception
     * @throws ExceptionPageNotFound
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    public function exec($returnAsIs = false)
    {
        $config = $this->getModuleConfig();
        if (!$config->isActive()) {
            if (DebugMode::isEnabled()) {
                if ($config['deactivated']) {
                    return t('<!-- Модуль "%0" деактивирован, блок "%1" скрыт -->', [
                        $config['name'],
                        $this->getInfo('title'),
                    ]);
                }
                return t('<!-- Модуль "%0" отключён, блок "%1" скрыт -->', [
                    $config['name'],
                    $this->getInfo('title'),
                ]);
            }
            return '';
        }

        if (!$this->getParam(self::BLOCK_ID_PARAM))  throw new \RS\Controller\Exception(t('Не задан параметр _block_id'));
        // Создаем группу инструментов отладки, если это необходимо
        if (\RS\Debug\Mode::isEnabled() && !$this->router->isAdminZone()) {

            if ($this->getParam('generate_by_template')) { // Если блок вставлен в шаблон с помощью moduleinsert
                //Генерируем кнопку
                $this->debug_group->addTool('block_options', new \RS\Debug\Tool\BlockOptions($this->getBlockId(),
                    [
                        'attr' => [
                            'href' => $this->router->getAdminUrl('editTemplateModule', [
                                '_block_id' => $this->getBlockId(),
                                'block' => $this->getUrlName(),
                            ],
                            'templates-blockctrl'),
                        ]
                    ]

                ));
            }
        }
        //Если идет режим рендеринга страницы, то сохраняем параметры.
        //Это необходимо, чтобы далее можно было напрямую обращаться к данному контроллеру, оперируя параметрами рендеринга.

        $cache = \RS\Cache\Manager::obj();

        if ($this->getParam('_rendering_mode') && !$cache->exists($this->store_key)) {
            $this->updateParamCache();
        }

        return parent::exec($returnAsIs);
    }

    /**
     * Обновляет сохраненные в кэше параметры текущего блока
     * @return void
     */
    public function updateParamCache()
    {
        $cache = \RS\Cache\Manager::obj();
        $additional_store_params = [
            self::BLOCK_ID_PARAM,
            self::BLOCK_PATH_PARAM,
            self::BLOCK_NUM_PARAM,
            self::BLOCK_LOADED_FROM_DB_PARAM,
            self::BLOCK_INSERT_CONTEXT
        ];

        $for_store = array_intersect_key($this->getParam(), array_flip(array_merge($this->getStoreParams(), $additional_store_params)));
        $cache->expire(0)->write($this->store_key, $for_store);
    }

    /**
     * Возвращает ключи параметров, которые необходимо сохранять в кэше
     *
     * @return array
     */
    public function getStoreParams()
    {
        if ($this->store_params === null) {
            $object_params = $this->getParamObject();
            $this->store_params = $object_params ? array_values($object_params->getPropertyIterator()->getKeys()) : [];
        }
        return $this->store_params;
    }

    /**
     * Возвращает id блока, который можно использовать в URL для обращения к данному блоку.
     * По данному id будут загружены все параметры($this->param) для блока
     *
     * @return integer
     */
    public function getBlockId()
    {
        return isset($this->block_id_cache) ? $this->block_id_cache : $this->block_id_cache = $this->getParam(self::BLOCK_ID_PARAM);
    }

    /**
     * Возвращает значение параметра из get только если запрос идет конкретно к текущему контроллеру.
     *
     * @return mixed
     */
    public function myGet($key, $type, $default = null)
    {
        if ($this->url->get(\RS\Router\RouteAbstract::CONTROLLER_PARAM, TYPE_STRING) == $this->getUrlName()) {
            return $this->url->get($key, $type, $default);
        }
        return $default;
    }

    /**
     * Возвращает input[type="hidden"] с id блочного контроллера, чтобы отметить, что данный пост идет по его инициативе.
     *
     * @return string
     */
    public function myBlockIdInput()
    {
        return '<input type="hidden" name="' . self::BLOCK_ID_PARAM . '" value="' . $this->getBlockId() . '">';
    }

    /**
     * Возвращает true, если инициатором POST запроса выступил данный контроллер
     *
     * @return bool
     */
    public function isMyPost()
    {
        return $this->url->isPost() && $this->url->post(self::BLOCK_ID_PARAM, TYPE_INTEGER) == $this->getBlockId();
    }

    /**
     * Возвращает URL для настройки блока (в случае если используется сборка по сетке)
     *
     * @return string
     */
    public function getSettingUrl($absolute = false)
    {
        if ($this->getParam('generate_by_template')) {
            $url = $this->router->getAdminUrl('editTemplateModule', ['_block_id' => $this->getBlockId(), 'block' => $this->getUrlName()], 'templates-blockctrl');
        } else {
            $url = $this->router->getAdminUrl('editModule', ['id' => $this->getParam('_block_id')], 'templates-blockctrl', $absolute);
        }
        return $url;
    }

    /**
     * Возвращает список параметров, которые не изменяются при редактировании через "режим отладки"
     *
     * @return string[]
     */
    public static function getNotReplaceableParams(): array
    {
        return array_merge(self::getAlwaysNotReplaceableParams(), self::getSelfNotReplaceableParams());
    }

    /**
     * Возвращает список параметров, которые не изменяются при редактировании через "режим отладки"
     *
     * @return string[]
     */
    protected static function getSelfNotReplaceableParams(): array
    {
        return [];
    }

    /**
     * Возвращает список параметров, которые не изменяются при редактировании через "режим отладки"
     *
     * @return string[]
     */
    protected static function getAlwaysNotReplaceableParams(): array
    {
        return [
            self::BLOCK_ID_PARAM,
            self::BLOCK_LOADED_FROM_DB_PARAM,
            self::BLOCK_INSERT_CONTEXT
        ];
    }
}