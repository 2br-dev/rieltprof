<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Application;

use RS\Application\Microdata\AbstractMicrodataType;
use RS\Application\Microdata\MicrodataSchemaOrgJsonLd;
use RS\Cache\Manager as CacheManager;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Helper\Tools as HelperTools;
use RS\Http\Request as HttpRequest;
use RS\Module\Item as ModuleItem;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use RS\Theme\Manager as ThemeManager;
use RS\View\Engine as ViewEngine;

/**
 * Singleton
 * Класс отвечает за параметры текущей страницы
 */
class Application
{
    const
        POSITION_HEADER = 'header',
        POSITION_FOOTER = 'footer',
        DEFAULT_ERROR_PAGE_TPL = '%THEME%/exception.tpl', //Путь к шаблону страницы ошибок
        DEFAULT_ADMIN_ERROR_PAGE_TPL = '%SYSTEM%/admin/exception.tpl';  //Путь к шаблону страницы ошибок в административной части

    //Виды режимов отладки
    const DEBUG_MODE_BLOCKS            = 'blocks'; //Блоки
    const DEBUG_MODE_SECTIONS_AND_ROWS = 'sectionsandrows'; //Секции и строки
    const DEBUG_MODE_CONTAINERS        = 'containers'; //Контейнеры

    const DEBUG_MODE_COOKIE = 'debug_mode'; //Кука для хранения вида режима отладки

    /** @var \RS\Application\Title $title */
    public $title;
    /** @var \RS\Application\Meta $meta */
    public $meta;
    /** @var Headers $headers */
    public $headers;
    /** @var BreadCrumbs $breadcrumbs Хлебные крошки для текущей страницы */
    public $breadcrumbs;
    /** @var \RS\Application\Block\Manager $blocks */
    public $blocks;
    /** @var AbstractMicrodataType */
    public $microdata;

    protected static $instance;

    protected
        $doctype = DOCTYPE, //Значение по умолчанию берем из \Setup
        $is_admin_zone,
        $body_class,
        $html_attr = [],
        $body_attr = [],
        $js_no_compress  = [],
        $css_no_compress = [],
        $js_default_position  = self::POSITION_HEADER,
        $css_default_position = self::POSITION_HEADER,
        $js = [
            self::POSITION_HEADER => [],
            self::POSITION_FOOTER => []
    ],
        $css = [
            self::POSITION_HEADER => [],
            self::POSITION_FOOTER => []
    ],
        $jscode  = [],
        $js_vars = [],
        $base_js,
        $base_css,
        $theme,
        $any_head_data = '',
        $alternative_cache = 0,
        $head_attributes = [];

    protected $debug_mode  = self::DEBUG_MODE_BLOCKS; //Текущий вид режима отладки, по умолчанию блоки
    protected $debug_modes = [ //Массив видов режима отладки
        self::DEBUG_MODE_BLOCKS,
        self::DEBUG_MODE_SECTIONS_AND_ROWS,
        self::DEBUG_MODE_CONTAINERS
    ];

    public static function getInstance()
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    protected function __construct()
    {
        $this->title       = new Title();
        $this->meta        = new Meta();
        $this->headers     = new Headers();
        $this->breadcrumbs = new BreadCrumbs();
        $this->blocks      = new Block\Manager();
        $this->microdata   = new MicrodataSchemaOrgJsonLd();

        $this->initThemePath();
        $this->is_admin_zone = RouterManager::obj()->isAdminZone();
        $this->js_default_position = (\Setup::$JS_POSITION_FOOTER && !$this->is_admin_zone) ? self::POSITION_FOOTER : self::POSITION_HEADER;
        $this->css_default_position = (\Setup::$CSS_POSITION_FOOTER && !$this->is_admin_zone) ? self::POSITION_FOOTER : self::POSITION_HEADER;

        if (!$this->is_admin_zone && \RS\Debug\Mode::isEnabled()){ //Если включена отладка, то добавим нужный класс
            $this->setBodyClass('debug-mode-'.$this->getDebugMode(), true);
        }

        $this->addJsVar([
            'folder' => (string)\Setup::$FOLDER
        ]);
    }

    /**
     * Устанавливает тип вида режима отладки для публичной части (Блоки, Строки, Секции, Контейнеры)
     *
     * @param string $mode - нужный нам режим вида правки
     */
    function setDebugMode($mode)
    {
        if (!in_array($mode, $this->debug_modes)){
            $this->debug_mode = self::DEBUG_MODE_BLOCKS;
        }else{
            $this->debug_mode = $mode;
        }
        //Поставим в куку режим
        $cookie_expire = time() + 60 * 60 * 24 * 30;
        setcookie(self::DEBUG_MODE_COOKIE, $this->debug_mode, $cookie_expire, \Setup::$FOLDER."/" ); //30 дней
    }

    /**
     * Возвращает текущий тип вида отладки для публичной части
     */
    function getDebugMode()
    {
        if (isset($_COOKIE[self::DEBUG_MODE_COOKIE])){
            if (!in_array($_COOKIE[self::DEBUG_MODE_COOKIE], $this->debug_modes)){
                $this->setDebugMode(self::DEBUG_MODE_BLOCKS);
            }else{
                return $_COOKIE[self::DEBUG_MODE_COOKIE];
            }
        }

        return $this->debug_mode;
    }

    /**
     * Инициализирует переменные, зависящие от текущей темы оформления
     * @return Application
     */
    function initThemePath()
    {
        $this->theme = ThemeManager::getCurrentTheme('theme');
        $this->base_css = \Setup::$SM_RELATIVE_TEMPLATE_PATH . '/' . trim($this->theme . \Setup::$RES_CSS_FOLDER, '/') . '/';
        $this->base_js = \Setup::$SM_RELATIVE_TEMPLATE_PATH . '/' . trim($this->theme . \Setup::$RES_JS_FOLDER, '/') . '/';

        return $this;
    }

    /**
     * Добавляет переменную для использования в JavaScript
     *
     * @param integer | string | array $key
     * @param mixed $value
     * @return Application
     */
    function addJsVar($key, $value = null)
    {
        if (is_array($key)) {
            $this->js_vars = $key + $this->js_vars;
        } else {
            $this->js_vars[$key] = $value;
        }
        return $this;
    }

    /**
     * Возвращает переменную для JavaScript по ключу. Если не указан, то
     *
     * @param string $key - ключ массива переменных
     * @return bool|mixed
     */
    function getJSVar($key = null)
    {
        if ($key){
            return isset($this->js_vars[$key]) ? $this->js_vars[$key] : false;
        }
        return $this->js_vars;
    }

    /**
     * Возвращает массив переменных, подготовленных для JavaScript
     *
     * @return string данные в json формате
     */
    function getJsonJsVars()
    {
        return json_encode($this->js_vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Устанавливает позицию добавления скриптов по-умолчанию - в Footer
     *
     * @param bool $bool - если true, то скипты по умолчанию добавляются в Footer, если иного не указано в конструкции добавления скрипта
     * @return void
     */
    function setJsDefaultFooterPosition($bool)
    {
        $this->js_default_position = $bool ? self::POSITION_FOOTER : self::POSITION_HEADER;
    }

    /**
     * Устанавливает позицию добавления стилей по-умолчанию - в Footer
     *
     * @param bool $bool - если true, то стили по умолчанию добавляются в Footer, если иного не указано в конструкции добавления скрипта
     * @return void
     */
    function setCssDefaultFooterPosition($bool)
    {
        $this->css_default_position = $bool ? self::POSITION_FOOTER : self::POSITION_HEADER;
    }

    /**
     * Подключить JavaScript к странице
     *
     * @param string $jspath путь к js - файлу
     * @param string $name внутренне имя
     * @param string $basepath базовый путь к папке BP_ROOT, BP_THEME, BP_COMMON
     * @param bool $no_compress - Не сжимать файл, даже если \Setup::$COMPRESS_JS установлено в true
     * @param array $params - массив дополнительных параметров (header, footer, unshift и т.д.)
     * @return Application
     */
    function addJs($jspath, $name = null, $basepath = null, $no_compress = false, $params = [])
    {
        if (!isset($basepath)) $basepath = BP_THEME;
        $jspath = $this->preparePath($jspath, $basepath, 'js');

        if (!isset($name)) $name = $jspath;
        if ($no_compress) $this->js_no_compress[] = $name;
        switch ($basepath) {
            case BP_COMMON:
                $jspath = \Setup::$JS_PATH . '/' . $jspath;
                break;
            case BP_THEME:
                $jspath = $this->base_js . $jspath;
                break;
            //default BP_ROOT
        }

        $file = [$name => [
            'file' => $jspath,
            'params' => $params
        ]];

        $position = $this->js_default_position;
        if (!empty($params['header'])) $position = self::POSITION_HEADER;
        if (!empty($params['footer'])) $position = self::POSITION_FOOTER;

        //Удаляем скрипт из противоположной позиции
        $not_position = $position == self::POSITION_FOOTER ? self::POSITION_HEADER : self::POSITION_FOOTER;
        unset($this->js[$not_position][$name]);

        //Добавляем скрипт в заданную позицию
        if (isset($params['unshift'])) {
            $this->js[$position] = $file + $this->js[$position];
        } else {
            $this->js[$position][$name] = $file[$name];
        }

        return $this;
    }

    /**
     * Устанавливает базовый путь к папке со скриптам
     *
     * @param string $folder
     * @return void
     */
    function setBaseJs($folder)
    {
        $this->base_js = $folder . '/';
    }

    /**
     * Возвращает список JavaScript файлов, которые необходимо подключить
     *
     * @param string $position - в какую позицию добавлять JS
     * @param boolean $absolute - генерировать абсолютный путь?
     *
     * @return array
     * @throws \RS\Event\Exception
     */
    function getJs($position = self::POSITION_HEADER, $absolute = false)
    {
        if (!isset($this->js[$position])) return [];

        $js_array = $this->js[$position];
        $js_array = $this->findAlternativeFile($js_array, 'js');

        $compress_type = $this->is_admin_zone ? \Setup::$COMPRESS_ADMIN_JS : \Setup::$COMPRESS_JS;

        if ((!$this->is_admin_zone || \Setup::$COMPRESS_ADMIN_ENABLE) && $compress_type) {
            $compressJs = new Compress\Js($js_array);
            $js_array = $compressJs->getCompressed($this->js_no_compress, $compress_type);
        }

        if ($absolute) { //Если нужно получить абсолютный адрес
            $current_site = SiteManager::getSite();
            foreach ($js_array as &$js_path) {
                if (!preg_match('/(\/\/)/i', $js_path['file'])) { //Проверин не указан ли абсолютный адрес
                    $js_path['file'] = $current_site->getAbsoluteUrl($js_path['file']);
                }
            }
        }

        $result = EventManager::fire('getjs', [
            'js_array' => $js_array
        ]);

        $result = $result->getResult();

        return $result['js_array'];
    }

    /**
     * Добавить на страницу JavaScript код
     *
     * @param mixed $name внутреннее название кода
     * @param mixed $jscode JavaScript код
     * @param string $position - позизия для которой нужно получить код JS
     * @return self
     */
    function addJsCode($name, $jscode, $position = 'footer')
    {
        $this->jscode[$position][$name] = $jscode;
        return $this;
    }

    /**
     * Возвращает JavaScript код, который нужно добавить в HEAD секцию HTML
     *
     * @param string $position - позизия для которой нужно получить код JS
     * @return string
     */
    function getJsCode($position = 'footer')
    {
        if (!empty($this->jscode[$position])){
            return implode("\n", $this->jscode[$position]);
        }
        return "";
    }

    /**
     * Устанавливает путь к CSS по-умолчанию
     *
     * @param string $folder
     * @return void
     */
    function setBaseCss($folder)
    {
        $this->base_css = $folder . '/';
    }

    /**
     * Подключить CSS к странице
     *
     * @param string $csspath путь к css - файлу
     * @param string $name - внутренне имя
     * @param string $basepath - базовый путь к папке BP_ROOT, BP_THEME, BP_COMMON
     * @param bool $no_compress - Не сжимать файл, даже если \Setup::$COMPRESS_CSS установлено в true
     * @param array $params - Дополнительные параметры
     * @return Application
     */
    function addCss($csspath, $name = null, $basepath = null, $no_compress = false, $params = [])
    {
        if (!isset($basepath)) $basepath = BP_THEME;

        $csspath = $this->preparePath($csspath, $basepath, 'css');
        if (!isset($name)){
            $name = $csspath;
        }
        if ($no_compress){ //Если сжимать не нужно, то дообавим в нужный массив
            $this->css_no_compress[] = $name;
        }

        switch ($basepath) {
            case BP_COMMON:
                $csspath = \Setup::$CSS_PATH . '/' . $csspath;
                break;
            case BP_THEME:
                $csspath = $this->base_css . $csspath;
                break;
            //default BP_ROOT
        }

        $file = [
            $name => [
                'file' => $csspath,
                'params' => $params,
            ]
        ];


        $position = $this->css_default_position;
        if (!empty($params['header'])) $position = self::POSITION_HEADER;
        if (!empty($params['footer'])) $position = self::POSITION_FOOTER;

        //Удаляем скрипт из противоположной позиции
        $not_position = $position == self::POSITION_FOOTER ? self::POSITION_HEADER : self::POSITION_FOOTER;
        unset($this->css[$not_position][$name]);

        //Добавляем скрипт в заданную позицию
        if (isset($params['unshift'])) {
            $this->css[$position] = $file + $this->css[$position];
        } else {
            $this->css[$position][$name] = $file[$name];
        }

        return $this;
    }

    /**
     * Заменяет конструкцию %ИМЯ_МОДУЛЯ% на путь к папке CSS и JS соответствующего модуля.
     * basepath принудительно устанавливается в root
     *
     * @param string $filepath - ссылка на CSS или JS файл
     * @param bool $basepath - базовый путь к папке
     * @param string $type - js или CSS
     * @return string
     */
    function preparePath($filepath, &$basepath, $type)
    {
        if (preg_match('/^%(.*?)%(.+)$/u', $filepath, $match)) {
            $folders = ModuleItem::getResourceFolders($match[1]);
            $basepath = BP_ROOT;
            $filepath = rtrim($folders['mod_' . $type], '/') . $match[2];
        }

        return $filepath;
    }

    /**
     * Проверяем, если файл есть в папке с темой оформления, то используем его
     * Тем самым даем возможность перегрузить JS, CSS файлы модулей локальными в теме оформления
     * А также даем возможность перегрузить CSS, JS файлы в самой теме оформления
     *
     * @param array $file_array - массив подключаемых файлов
     * @param string $type - тип файла
     * @param bool $cache - кэш ключен
     * @return mixed
     */
    public function findAlternativeFile($file_array, $type, $cache = true)
    {
        if ($this->is_admin_zone){
            return $file_array; //Перегрузка CSS,JS работает только для клиентской части
        }

        if ($cache) {
            return CacheManager::obj()
                ->request([$this, __FUNCTION__], $file_array, $type, false);

        } else {
            $prefix = \Setup::$FOLDER . \Setup::$MODULE_FOLDER;
            $section = "[^\/]+";
            $view_folder = \Setup::$MODULE_TPL_FOLDER;
            $common_res = $type == 'css' ? \Setup::$CSS_PATH : \Setup::$RES_JS_FOLDER;
            $theme_base = $type == 'css' ? $this->base_css : $this->base_js;

            $module_pattern = "#^($prefix)/($section)($view_folder)/($type)/(.+)$#"; // /modules/affiliate/view/css/affiliates.css
            $resource_pattern = "#^($common_res)/(.+)$#"; // /resource/css/flatadmin/iconic-font/css/material-design-iconic-font.min.css
            $theme_pattern = "#^($theme_base)(.+)$#"; // /templates/default/resource/css/style.css


            foreach ($file_array as &$file) {
                $filepath = strtok($file['file'], '?');
                $query = strtok('?');
                if (preg_match($theme_pattern, $filepath, $match)) {
                    //Если файл из папки /templates/{ТЕМА}/resource/(js|css)/{ПУТЬ К ФАЙЛУ}.{РАСШИРЕНИЕ}
                    //То ищем приоритетную альтернативу в /templates/{ТЕМА}/resource/{js|css}/{ПУТЬ К ФАЙЛУ}.my.{РАСШИРЕНИЕ}
                    $alternative_path = $theme_base . $this->getMyCustomFilename($match[2]);
                } elseif (preg_match($resource_pattern, $filepath, $match)) {
                    //Если файл из папки /resource/(js|css)/{ПУТЬ К ФАЙЛУ}.{РАСШИРЕНИЕ}
                    //То ищем приоритетную альтернативу в /templates/{ТЕМА}/resource/{js|css}/{ПУТЬ К ФАЙЛУ}.my.{РАСШИРЕНИЕ}
                    $alternative_path = $theme_base . $this->getMyCustomFilename($match[2]);
                } elseif (preg_match($module_pattern, $filepath, $match)) {
                    //Если файл из папки /modules/{МОДУЛЬ}/view/(js|css)/{ПУТЬ К ФАЙЛУ}.{РАСШИРЕНИЕ}
                    //То ищем приоритетную альтернативу в /templates/{ТЕМА}/moduleview/{МОДУЛЬ}/{js|css}/{ПУТЬ К ФАЙЛУ}.my.{РАСШИРЕНИЕ}
                    $alternative_path = \Setup::$SM_RELATIVE_TEMPLATE_PATH . "/{$this->theme}/moduleview/{$match[2]}/{$type}/" . $this->getMyCustomFilename($match[5]);
                } else {
                    $alternative_path = null;
                }

                if ($alternative_path !== null && is_file(\Setup::$ROOT . $alternative_path)) {
                    $file['file'] = $alternative_path.($query ? '?'.$query : '');
                }
            }

            return $file_array;
        }
    }

    /**
     * Добавляет к расширению префикс .my
     *
     * @param string $filename Имя файла
     * @return string
     */
    private function getMyCustomFilename($filename)
    {
        return preg_replace('/.([^.]+?)$/', '.my.\1', $filename);
    }


    /**
     * Удаляет js файл по ключу, если он задан, или удаляет все вскрипты
     *
     * @param string|null $name - ключ js скриппта
     * @return Application
     */
    function removeJs($name = null)
    {
        if ($name === null) {
            $this->js = [];
        } else {
            unset($this->js[self::POSITION_HEADER][$name]);
            unset($this->js[self::POSITION_FOOTER][$name]);
        }
        return $this;
    }

    /**
     * Удаляет css файл по ключу, если он задан, или удаляет все вскрипты
     *
     * @param mixed $name
     * @return Application
     */
    function removeCss($name = null)
    {
        if ($name === null) {
            $this->css = [];
        } else {
            unset($this->css[$name]);
        }
        return $this;
    }

    /**
     * Возвращает массив CSS файлов, которые необходимо подключить на странице
     * Если включена опция \Setup::$COMPRESS_CSS, то CSS файлы будут объеденены и оптимизированы
     *
     * @param boolean $absolute - генерировать абсолютный путь?
     *
     * @return array
     * @throws \RS\Event\Exception
     */
    function getCss($position = self::POSITION_HEADER, $absolute = false)
    {
        if (!isset($this->css[$position])) return [];

        $css_array = $this->css[$position];
        $css_array = $this->findAlternativeFile($css_array, 'css');

        $compress_type = $this->is_admin_zone ? \Setup::$COMPRESS_ADMIN_CSS : \Setup::$COMPRESS_CSS;

        if ((!$this->is_admin_zone || \Setup::$COMPRESS_ADMIN_ENABLE) && $compress_type) {
            $compressCss = new Compress\Css($css_array);
            $css_array = $compressCss->getCompressed($this->css_no_compress, $compress_type);
        }

        if ($absolute) { //Если нужно получить абсолютный адрес
            $current_site = SiteManager::getSite();
            foreach ($css_array as &$css_path) {
                if (!preg_match('/(\/\/)/i', $css_path['file'])) { //Проверин не указан ли абсолютный адрес
                    $css_path['file'] = $current_site->getAbsoluteUrl($css_path['file']);
                }
            }
        }

        $result = EventManager::fire('getcss', [
            'css_array' => $css_array
        ]);

        $result = $result->getResult();

        return $result['css_array'];
    }

    /**
     * Задает DOCTYPE, который должен быть установлен
     *
     * @param string $doctype
     * @return void
     */
    function setDoctype($doctype)
    {
        $this->doctype = $doctype;
    }

    /**
     * Возвращает DOCTYPE, который должен быть установлен
     *
     * @return string
     */
    function getDoctype()
    {
        return $this->doctype;
    }

    /**
     * Возвращает текстовую информацию, установленную с помощью addAnyHeadData
     */
    function getAnyHeadData()
    {
        return $this->any_head_data;
    }

    /**
     * Устанавливает произвольный текст в секцию HEAD
     *
     * @param string $data
     * @param bool $append
     * @return Application
     */
    function setAnyHeadData($data, $append = true)
    {
        $this->any_head_data = (($append) ? $this->any_head_data : '') . $data;
        return $this;
    }

    /**
     * Добавляет атрибут к секции head
     *
     * @param string|array $key атрибут
     * @param string $value значение атрибута
     * @return Application
     */
    function addHeadAttribute($key, $value = null)
    {
        if (is_array($key)) {
            $this->head_attributes = $key;
        } else {
            $this->head_attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * Возвращает атрибуты для тега head
     *
     * @param bool $inline - Если true, возвращает строку, иначе массив
     * @return string|array
     */
    function getHeadAttributes($inline = false)
    {
        if ($inline) {
            $str = '';
            foreach ($this->head_attributes as $key => $value) {
                $str .= $key . '="' . HelperTools::toEntityString($value) . '"';
            }
            return $str;
        }
        return $this->head_attributes;
    }

    /**
     * Возвращает HTML код, который приостанавливает выполнение JavaScript кода до полной загрузки скриптов
     *
     * @return string|null
     * @throws \SmartyException
     */
    function autoloadScripsAjaxBefore()
    {
        if (HttpRequest::commonInstance()->isAjax()) {
            $tpl = new ViewEngine();
            $tpl->assign('app', $this);
            return $tpl->fetch('%system%/ajaxscriptload_before.tpl');
        }
        return null;
    }

    /**
     * Возвращает HTML код ajax-загрузки JS и CSS файлов
     *
     * @return string|null
     * @throws \SmartyException
     */
    function autoloadScripsAjaxAfter()
    {
        if (HttpRequest::commonInstance()->isAjax()) {
            $tpl = new ViewEngine();
            $tpl->assign('app', $this);
            return $tpl->fetch('%system%/ajaxscriptload_after.tpl');
        }
        return null;
    }

    /**
     * Устанавливает класс для тега body
     *
     * @param mixed $class
     * @param bool $append - Если true, то класс будет добавлен, иначе заменен
     * @return void
     */
    function setBodyClass($class, $append = false)
    {
        if ($append) {
            $this->body_class .= ' ' . $class;
        } else {
            $this->body_class = $class;
        }
    }

    /**
     * Возвращает класс для тега body
     * @return string
     */
    function getBodyClass()
    {
        return $this->body_class;
    }

    /**
     * Устанавливает атрибуты, которые следует добавить к body.
     * Класс следует добавлять через метод setBodyClass
     *
     * @param string|array $key - название атрибута. В array можно передать ассоциативный массив с названиями и значениями атрибутов
     * @param $value - значение атрибута, ели $key строка
     * @param bool $append Если true, то атрибут будет добавлен к остальным
     * @return void
     */
    function setBodyAttr($key, $value = null, $append = true)
    {
        if (!$append)
            $this->body_attr = [];

        if (is_array($key)) {
            foreach ($key as $real_key => $value) {
                if ($value === null) {
                    unset($this->body_attr[$real_key]);
                } else {
                    $this->body_attr[$real_key] = $value;
                }
            }
        } else {
            if ($value === null) {
                unset($this->body_attr[$key]);
            } else {
                $this->body_attr[$key] = $value;
            }
        }
    }

    /**
     * Возвращает значение атрибута для body
     *
     * @param string $key название атрибута
     * @return string|array
     */
    function getBodyAttr($key = null)
    {
        return $key === null ? $this->body_attr : $this->body_attr[$key];
    }

    /**
     * Возвращает строку атрибутов, которые необходимо установить тегу body
     *
     * @return string
     */
    function getBodyAttrLine()
    {
        $line = '';
        foreach ($this->body_attr as $key => $value) {
            $line .= "$key=\"{$value}\" ";
        }
        return $line;
    }

    /**
     * Устанавливает атрибуты, которые следует добавить к тегу html.
     *
     * @param string|array $key - название атрибута. В array можно передать ассоциативный массив с названиями и значениями атрибутов
     * @param $value - значение атрибута, ели $key строка
     * @param bool $append Если true, то атрибут будет добавлен к остальным
     * @return void
     */
    function setHtmlAttr($key, $value = null, $append = true)
    {
        if (!$append)
            $this->html_attr = [];

        if (is_array($key)) {
            foreach ($key as $real_key => $value) {
                if ($value === null) {
                    unset($this->html_attr[$real_key]);
                } else {
                    $this->html_attr[$real_key] = $value;
                }
            }
        } else {
            if ($value === null) {
                unset($this->html_attr[$key]);
            } else {
                $this->html_attr[$key] = $value;
            }
        }
    }

    /**
     * Возвращает значение атрибута для html
     *
     * @param string $key название атрибута
     * @return string|array
     */
    function getHtmlAttr($key = null)
    {
        return $key === null ? $this->html_attr : $this->html_attr[$key];
    }

    /**
     * Возвращает строку атрибутов, которые необходимо установить тегу html
     *
     * @return string
     */
    function getHtmlAttrLine()
    {
        $line = '';
        foreach ($this->html_attr as $key => $value) {
            $line .= "$key=\"{$value}\"";
        }
        return $line;
    }

    /**
     * Отображает страницу с ошибкой. Прекращает выполение скрипта
     *
     * @param integer $status_code
     * @param string | null $comment
     * @param string | null $http_code_phrase
     * @return void
     */
    function showException($status_code, $comment = null, $http_code_phrase = null)
    {
        $this->cleanOutput();
        $this->headers
            ->cleanHeaders()
            ->setStatusCode($status_code, $http_code_phrase)
            ->sendHeaders();

        $template = $this->is_admin_zone ? self::DEFAULT_ADMIN_ERROR_PAGE_TPL : self::DEFAULT_ERROR_PAGE_TPL;

        $tpl = new ViewEngine();
        $tpl->assign([
            'error' => [
                'code' => $status_code,
                'comment' => $comment
            ],
            'site' => SiteManager::getSite(),
            'site_config' => ConfigLoader::getSiteConfig()
        ]);
        $tpl->display($template);
        exit;
    }

    /**
     * Очищает буфер вывода
     * @return void
     */
    function cleanOutput()
    {
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_clean(); //Очищаем все что было подготовлено на вывод
        }
        header('Content-Encoding: identity'); //Отменяем сообщение о том, что вывод сжимается gzip'ом (посланное ob_start("ob_gzhandler"))
    }

    /**
     * Перенаправляет браузер пользователя на другую страницу. Прекращает выполнение скрипта
     *
     * @param string $url Если url не задан, то перенаправляет на корневую страницу сайта
     * @param int $status - номер статуса редиректа с которым выполнять редирект
     */
    public function redirect($url = null, $status = 302)
    {
        if (!$url) {
            if ($site = SiteManager::getSite()) {
                $url = $site->getRootUrl();
            } else {
                $url = '/';
            }
        }
        $this->headers
            ->addHeader('location', $url)
            ->setStatusCode($status)
            ->sendHeaders();
        exit;
    }
}
