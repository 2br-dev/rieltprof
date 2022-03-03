<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Templates\Model\Orm;

use RS\Cache\Manager;
use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * Один блок в секции
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $page_id Страница
 * @property integer $section_id ID секции
 * @property integer $template_block_id id блока в теме без сетки
 * @property string $context Дополнительный идентификатор темы
 * @property string $module_controller Модуль
 * @property integer $public Публичный
 * @property integer $sortn 
 * @property string $params Параметры
 * --\--
 */
class SectionModule extends OrmObject
{
    protected static $table = 'section_modules';
    protected static $cache_blocks = [];
    
    function _init()
    {
        parent::_init();
        $this->getPropertyIterator()->append([
            'page_id' => new Type\Integer([
                'no_export' => true,
                'index' => true,
                'description' => t('Страница'),
            ]),
            'section_id' => new Type\Integer([
                'no_export' => true,
                'description' => t('ID секции'),
            ]),
            'template_block_id' => new Type\Bigint([
                'description' => t('id блока в теме без сетки'),
            ]),
            'context' => new Type\Varchar([
                'no_export' => true,
                'description' => t('Дополнительный идентификатор темы'),
                'hint' => t('Задается только если не пустой template_block_id'),
                'maxLength' => 50
            ]),
            'module_controller' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Модуль'),
            ]),
            'public' => new Type\Integer([
                'description' => t('Публичный'),
                'listenPost' => false,
                'maxLength' => 1,
                'default' => 1,
                'checkboxView' => [1,0]
            ]),
            'sortn' => new Type\Integer(),
            'params' => new Type\Text([
                'no_export' => true,
                'description' => t('Параметры'),
            ]),
        ]);

        $this->addIndex(['context']);
    }

    /**
     * Действия перед записью
     *
     * @param string $flag - insert или update
     * @return false|void|null
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            if ($this['sortn'] === null) {
                $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn)+1 as max')
                    ->from($this)
                    ->where(['section_id' => $this['section_id']])
                    ->exec()->getOneField('max', 0);
            }
        }
        if ($this['page_id'] === null && $this['section_id']) {
            $section = new Section($this['section_id']);
            $this['page_id'] = $section['page_id'];
        }
    }

    /**
     * Возвращает массив параметров модуля
     *
     * @param bool $grid Если true, то возвращаются параметры для шаблона, собранного по сетке.
     * @return array|mixed
     */
    function getParams($grid = true)
    {
        $params = unserialize($this['params']);
        if ($grid) {
            $default = [
                \RS\Controller\Block::BLOCK_ID_PARAM => $this['id'],
                'generate_by_grid' => true,
                'sortn' => $this['sortn']
            ];
        } else {
            $default = [];
        }
        return is_array($params) ? $default + $params : $default;
    }

    /**
     * Возвращает параметр модуля по его ключу
     *
     * @param string $key - ключ массива с данными
     * @param bool $grid Если true, то возвращаются параметры для шаблона, собранного по сетке.
     *
     * @return mixed
     */
    function getParam($key, $grid = true)
    {
        $params = $this->getParams($grid);

        return isset($params[$key]) ? $params[$key] : null;
    }

    /**
     * Утановка параметров модуля
     *
     * @param array $params - массив параметров
     */
    function setParams($params)
    {
        $this['params'] = serialize($params);
    }

    /**
     * Утановка параметра модуля по ключу
     *
     * @param string $key - ключ параметра для установки
     * @param mixed $value - данные для записи
     */
    function setParam($key, $value)
    {
        $params = $this->getParams();
        $params[$key] = $value;
        $this['params'] = serialize($params);
    }

    /**
     * Возвращает объект страницы, на которой находится модуль
     *
     * @return SectionPage
     */
    function getPage()
    {
        return new SectionPage($this['page_id']);
    }

    /**
     * Сохраняет блок в виде DOM элемента
     *
     * @param \DOMDocument $dom - объект DOM документа, с которым будет связан созданный элемент
     * @return \DOMElement
     */
    public function saveAsDomElement(\DOMDocument $dom)
    {
        static $exclude_block_params = ['_block_id', 'generate_by_grid', 'theme_context', 'params_loaded_from_db'];
        $exclude_properties = (empty($this['page_id'])) ? [] : ['template_block_id'];

        $dom_block = $dom->createElement('block');

        foreach($this as $key => $property) {
            if (empty($property->no_export) && $key != 'id' && !in_array($key, $exclude_properties)) {
                $dom_block->setAttribute($key, $this[$key]);
            }
        }

        foreach($this->getParams(false) as $key => $value) {
            if (in_array($key, $exclude_block_params)) continue;
            $param = $dom->createElement($key);
            $param->appendChild($dom->createCDATASection( base64_encode(serialize($value)) ));
            $dom_block->appendChild($param);
        }

        return $dom_block;
    }
    
    public static function getPageBlocks($page_id, $only_public = false)
    {
        if (!isset(self::$cache_blocks[ $page_id ])) {
            self::$cache_blocks[$page_id] = \RS\Orm\Request::make()
                ->from(new self())
                ->where(['page_id' => $page_id] + ($only_public ? ['public' => 1] : []))
                ->orderby('section_id, sortn')
                ->objects(null, 'section_id', true);
        }
        return self::$cache_blocks[ $page_id ];
    }

    /**
     * Перемещает элемент на новую позицию. 0 - первый элемент
     *
     * @param integer $new_position - новая позиция элемента в секции
     * @param integer|null $new_section_id - id секции куда будет ставлен блок
     * @return bool
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function moveToPosition($new_position, $new_section_id = null)
    {
        if ($this->noWriteRights()){
            return false;
        }

        if ($new_section_id) {
            $this->changeParent($new_section_id);
        }

        $downmove = \RS\Orm\Request::make()
            ->update($this)
            ->where([
                'section_id' => $this['section_id']
            ]);
        $upmove = clone $downmove;

        //Раздвинем позиции
        //Вниз
        $downmove->set('sortn = sortn - 1')
            ->where("sortn < '#new_pos'", ['new_pos' => $new_position])->exec();

        //Вверх
        $upmove->set('sortn = sortn + 1')
            ->where("sortn >= '#new_pos'", ['new_pos' => $new_position])->exec();

        //И занусем наш блок между позиций
        \RS\Orm\Request::make()
            ->update($this)
            ->set([
                'sortn' => $new_position
            ])
            ->where([
                'id' => $this['id']
            ])
            ->exec();

        //Обновим сортировочные индексы у данной секции, чтобы было 0,1,2,3,4
        $items = \RS\Orm\Request::make()
            ->from($this)
            ->orderby('sortn')
            ->where([
                'section_id' => $this['section_id']
            ])
            ->exec()->fetchAll();

        foreach ($items as $k=>$item){
            \RS\Orm\Request::make()
                ->update()
                ->from($this)
                ->set([
                    'sortn' => $k
                ])
                ->where([
                    'id' => $item['id']
                ])->exec();
        }

        
        //Сбросим кэш при перемещении блоков
        \RS\Cache\Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
        return true;
    }

    /**
     * Перемещает блок относительно другого
     *
     * @param integer $block_id - id блока относительно которого будет перемещение
     * @param string $move_type - тип перемещения (before|after|first|last)
     *
     * @return boolean
     * @throws \RS\Db\Exception
     */
    function moveToPositionRelativeOfBlock($block_id, $move_type = "after")
    {
        //Переместим на нужное место относительно модуля
        $relative_block = new \Templates\Model\Orm\SectionModule($block_id);
        $parent_id = $relative_block['section_id'];
        switch($move_type){
            case "first": //В начало
                return $this->moveToPosition(0, $parent_id);
                break;
            case "before": //Перед
                return $this->moveToPosition($relative_block['sortn'], $parent_id);
                break;
            case "after": //После
                return $this->moveToPosition($relative_block['sortn'] + 1, $parent_id);
                break;
            case "last": //Последним
            default:
                //Получим максимальную позицию и вставим
                $max = \RS\Orm\Request::make()
                        ->select('MAX(sortn) as num')
                        ->from($this)
                        ->where([
                            'section_id' => $parent_id,
                            'page_id' => $this['page_id'],
                        ])->exec()
                        ->getOneField('num', 0);
                return $this->moveToPosition($max, $parent_id);
                break;
        }
    }


    /**
     * Перемещяет элемент в последнюю позицию нового родителя.
     * Обновляет сортировочные индексы у предыдущего родителя
     *
     * @param integer $new_section_id - id секции куда будет перемещаться блок
     * @return bool
     * @throws \RS\Db\Exception
     */
    function changeParent($new_section_id)
    {
        if ($this['section_id'] == $new_section_id) {
            return false;
        }

        //Изменяем сортировочные индексы в старом контейнере
        \RS\Orm\Request::make()
            ->update($this)
            ->set('sortn = sortn - 1')
            ->where([
                'section_id' => $this['section_id']
            ])
            ->where("sortn > '#sortn'", ['sortn' => $this['sortn']])
            ->exec();


        //Получаем новый
        $max_new_sortn = \RS\Orm\Request::make()
            ->select('MAX(sortn)+1 as maxsortn')
            ->from($this)
            ->where([
                'section_id' => $new_section_id
            ])
            ->exec()->getOneField('maxsortn', 0);

        $section = new \Templates\Model\Orm\Section($new_section_id);

        //Изменяем родителя секции
        \RS\Orm\Request::make()
            ->update($this)
            ->set([
                'sortn' => $max_new_sortn,
                'page_id' => $section['page_id'], //Текущая страница
                'section_id' => $new_section_id
            ])
            ->where([
                'id' => $this['id'],
            ])
            ->exec();
        
        $this['page_id']    = $section['page_id'];
        $this['section_id'] = $new_section_id;
        $this['sortn']      = $max_new_sortn;
        
        return true;
    }

    /**
     * Возвращает информацию настроек блока
     *
     * @param null $key - ключ массива необходимой информации
     * @return array|mixed
     */
    function getBlockInfo($key = null)
    {
        if (class_exists($this['module_controller'])) {
            $result = call_user_func([$this['module_controller'], 'getInfo']);
        } else {
            $result = [
                'title' => t('Контроллер не найден'),
                'description' => ''
            ];
        }
        return $key !== null ? $result[$key] : $result;
    }
    
    /**
    * Возвращает объект блочного контроллера
    *
    * @return \RS\Controller\Block
    */
    function getControllerInstance()
    {
        if (class_exists($this['module_controller'])) {
            return new $this['module_controller']();
        }
        return new \Templates\Model\ModuleBlockStub();
    }

    /**
     * Удаляет блок и очищает кэш конструктора сайта
     *
     * @return bool
     */
    function delete()
    {
        if ($result = parent::delete()) {
            Manager::obj()->invalidateByTags(CACHE_TAG_BLOCK_PARAM);
        }
        return $result;
    }
    
}
