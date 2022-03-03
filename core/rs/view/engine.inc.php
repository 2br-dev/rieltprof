<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\View;

/**
* Шаблонизатор
*/
class Engine extends \Smarty
{
    public
        $hooks,
        $called_hooks = [],
        $config,
        $cms_config,
        $theme,
        $theme_settings,
        $site;
        
    function __construct()
    {
        parent::__construct();
        
        $this->setTemplateDir(\Setup::$SM_TEMPLATE_PATH);
        $this->setCompileDir(\Setup::$SM_COMPILE_PATH);
        $this->setCacheDir(\Setup::$SM_CACHE_PATH);
        $this->addPluginsDir(\Setup::$PATH.'/core/smarty/rsplugins/');
        $this->registerClass('ModuleManager', '\RS\Module\Manager');
        $this->registerClass('ConfigLoader', '\RS\Config\Loader');
        
        $this->compile_check = \Setup::$SM_COMPILE_CHECK;
        $this->error_reporting = E_ALL ^ E_NOTICE;
        $this->_dir_perms = \Setup::$CREATE_DIR_RIGHTS;
        
        //Устанавливаем свой обработчик путей к шаблонам
        $this->default_resource_type = 'rs';
        $this->config = \RS\Config\Loader::getSiteConfig();
        $this->cms_config = \RS\Config\Loader::getSystemConfig();
        $this->theme = \RS\Theme\Manager::getCurrentTheme();
        $this->site = \RS\Site\Manager::getSite();
        $this->theme_settings = $this->getThemeSettings();
        $this->hooks = new \RS\View\Hooks($this, $this->site['id'] ?? 0);
        
        $this->assign([
            'Setup' => \Setup::varsAsArray(),
            'app' => \RS\Application\Application::getInstance(),
            'router' => \RS\Router\Manager::obj(),
            'url' => \RS\Http\Request::commonInstance(),
            'current_user' => \RS\Application\Auth::getCurrentUser(),
            'CONFIG' => $this->config,
            'CMS_CONFIG' => $this->cms_config,
            'LANG' => \RS\Language\Core::getCurrentLang(),
            'SITE' => $this->site,
            'THEME_ROOT' => \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->theme['theme'],
            'THEME_CSS' => \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->theme['theme'].\Setup::$RES_CSS_FOLDER,
            'THEME_JS' => \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->theme['theme'].\Setup::$RES_JS_FOLDER,
            'THEME_IMG' => \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->theme['theme'].\Setup::$RES_IMG_FOLDER,
            'THEME_SHADE' => $this->theme['shade'],
            'THEME_SETTINGS' => $this->theme_settings
        ]);
    }
    
    /**
    * Включает кэширование шаблонов, если это разрешено в настройках системы и если у пользователя не включен режим отладки
    * 
    * @param string $cache_id_str - устанавливает идентификатор кэша, для последующих вызовов is_cached, fetch, display
    * @return Engine
    */
    function cacheOn($cache_id_str = null)
    {
        if ($cache_id_str !== null) {
            $this->cache_id = crc32($cache_id_str.($this->site ? $this->site->id : ''));
        }
        if (\Setup::$CACHE_BLOCK_ENABLED && !\RS\Debug\Mode::isEnabled()) {
            $this->caching = true;
        }
        return $this;
    }
    
    /**
    * Возвращает параметры темы оформления
    * 
    * @param bool $cache - Если true, то используется кэширование
    * @return array
    */
    function getThemeSettings($cache = true)
    {   
        if (!\RS\Router\Manager::obj()->isAdminZone()) {
            $theme_str = \RS\Theme\Manager::getCurrentTheme('full_name');
            if ($cache) {
                return \RS\Cache\Manager::obj()
                            ->request([$this, __FUNCTION__], false, $this->site ? $this->site->id : '', $theme_str);
            } else {
                //Загружаем переменные темы только для клиентской части
                $theme = new \RS\Theme\Item($theme_str);
                if ($context = $theme->getContextOptions()) {
                    return $context->options_arr;
                }
            }
        }
        return [];
    }
}


