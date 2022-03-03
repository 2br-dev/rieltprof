<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\View;

/**
* Класс обеспечивает работу хуков в шаблонах.
* Хуки позволяют сторонним модулям добавлять собственный HTML "до", "вместо" или "после" хука
*/
class Hooks
{
    const
        /**
        * Путь относительно папки /modules/{МОДУЛЬ}/view,
        * в котором будет происходить поиск обработчиков хуков
        */
        HOOK_FOLDER = '/hooks';
        
    protected
        $view,
        $site_id;
        
    protected static 
        $hooks = [];
    
    function __construct(\RS\View\Engine $view = null, $site_id = null)
    {
        $this->view = $view;
        $this->site_id = $site_id === null ?  \RS\Site\Manager::getSiteId() : $site_id;
    }
    
    /**
    * Возвращает список обработчиков шаблонных хуков.
    * Вызывается при первом вызове хука в шаблоне
    * 
    * @param bool $cache - Если true, то значение возвращается из кэша
    * @return array
    */
    function getHookHandlers($cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->request([$this, __FUNCTION__], false, $this->site_id);
        } else {
            $hook_handlers = [];
            
            //Загружаем модули
            $mod_manager = new \RS\Module\Manager();
            $modules = $mod_manager->getActiveList($this->site_id);
            foreach($modules as $module) {
                $hook_handlers = array_merge_recursive($hook_handlers, $this->loadModuleHookDir($module));
            }
            
            //Сортируем обработчики
            return $this->sortHooks($hook_handlers);
        }
    }
    
    /**
    * Ищет обработчики хуков в модуле
    * 
    * @param \RS\Module\Item $module - объект одного модуля
    * @return array
    */
    private function loadModuleHookDir(\RS\Module\Item $module)
    {
        $hook_root = $module->getFolder().\Setup::$MODULE_TPL_FOLDER.self::HOOK_FOLDER;        
        $hook_handlers = [];
        if (is_dir($hook_root)) {

            foreach (new \DirectoryIterator($hook_root) as $dir_info) {
                if ($dir_info->isDot() || !$dir_info->isDir()) continue;
                
                foreach(new \DirectoryIterator($dir_info->getPathname()) as $file_info) {
                    if (!$file_info->isFile()) continue;
                    
                    $tmp_filename = pathinfo($file_info->getFilename(), PATHINFO_FILENAME);
                    $file = pathinfo($tmp_filename) + ['extension' => ''];
                    
                    if ( in_array($file['extension'], ['pre', 'post', '']) ) {
                        $hook_name = $dir_info->getFilename().':'.$file['filename'];
                        
                        $module_name = $module->getName();
                        $hook_handlers[$hook_name][$module_name]['module_title'] = $module->getConfig()->name;
                        $hook_handlers[$hook_name][$module_name][($file['extension'] ?: 'ovr')] = '%'.$module_name.'%'.self::HOOK_FOLDER.'/'.$dir_info->getFilename().'/'.$file_info->getFilename();                        
                    }
                }
            }
        }        
        
        return $hook_handlers;
    }
    
    /**
    * Сортирует обработчики в нужном порядке
    * 
    * @param array $hook_handlers
    * @return array
    */
    private function sortHooks($hook_handlers)
    {
        if ($sort = $this->getSortData()) {
            
            $sorted_list = [];
            foreach($hook_handlers as $hook_name => $modules) {
                if (isset($sort[$hook_name])) {
                    $sorted_list[$hook_name] = array_flip( array_intersect($sort[$hook_name], array_keys($modules)) );
                }
                foreach($modules as $module => $data) {
                    $sorted_list[$hook_name][$module] = $data;
                }
            }
            $hook_handlers = $sorted_list;
        }
        
        return $hook_handlers;
    }
    
    /**
    * Возвращает сведения о сортировке модулей в рамках кухов
    * 
    * @return array
    */
    private function getSortData()
    {
        //Для совместимости
        if (!class_exists('\Templates\Model\Orm\TemplateHookSort')) 
            return [];
            
        $context = \RS\Theme\Manager::getCurrentTheme('blocks_context');
            
        return \RS\Orm\Request::make()
                    ->from(new \Templates\Model\Orm\TemplateHookSort)
                    ->where([
                        'site_id' => $this->site_id,
                        'context' => $context
                    ])
                    ->orderby('hook_name, sortn')
                    ->exec()
                    ->fetchSelected('hook_name', 'module', true);
    }
    
    /**
    * Выполняет хук. Возвращает готовый HTML код, которым необходимо заменить контент по умолчанию.
    * 
    * @param string $name - имя хука
    * @param array $params - дополнительные параметры
    * @param string $content - контент по умолчанию хука
    * @return string
    */
    function callHook($name, $params, $content)
    {
        if (!isset(self::$hooks[$this->site_id])) {
            self::$hooks[$this->site_id] = $this->getHookHandlers();
        }
        
        if (!$this->view) {
            throw new \RS\Exception(t('Для выполнения хука, необходимо установить объект шаблонизатора view'));
        }

        $this->view->assign($params);
        
        //Если есть обработчики даного хука
        if (isset(self::$hooks[$this->site_id][$name])) {
            $parts = [
                'pre' => '',
                'ovr' => $content,
                'post' => ''
            ];
            
            foreach(self::$hooks[$this->site_id][$name] as $module => $data) {                                
                if (isset($data['pre'])) {
                    $parts['pre'] .= $this->addDebugComments($this->view->fetch($data['pre']), $data['pre']);
                }
                
                if (isset($data['ovr'])) {
                    $parts['ovr'] = $this->addDebugComments($this->view->fetch($data['ovr']), $data['pre']);
                }
                
                if (isset($data['post'])) {
                    $parts['post'] .= $this->addDebugComments($this->view->fetch($data['post']), $data['post']);
                }
            }
            
            return implode('', $parts);
        }
        
        return $content;
    }
    
    /**
    * Добавляет в режиме отладки комментарии о том, какой шаблон добавил тот или иной HTML код
    * 
    * @param string $html - исходный HTML
    * @param string $template - шаблон, который добавляет данный HTML
    * @return string возвращает итоговый HTML
    */
    private function addDebugComments($html, $template)
    {
        if (\RS\Debug\Mode::isEnabled()) {
            //Если включен режим отладки, то добавляем комментарий, кто добавил блок
            $comment_open = '<!-- '.t("Блок добавлен с помощью шаблона '%0'", [$template]).' -->';
            $comment_close = '<!-- '.t("Конец блока, добавленного с помощью шаблона '%0'", [$template]).' -->';
            return $comment_open.$html.$comment_close;
        }
        return $html;
    }
}
