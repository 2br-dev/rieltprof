<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model;
use RS\Exception;

/**
* Класс АПИ для модуля мобильное приложение для работы с шаблонами 
*/
class TemplateManager{

    /**
     * Возвращает список с доступными путями к шаблонам для мобильного приложения, собирая сведения модулей
     *
     * @return array
     * @throws \RS\Event\Exception
     */
    public static function getTemplatesListFromModules()
    {
        static 
            $templates;
        //Получим пути к шаблонам из модулей
        if ($templates === null){
            $result    = \RS\Event\Manager::fire('mobilesiteapp.gettemplates', []);
            $templates = $result->getResult();
        }
        
        return $templates;
    }

    /**
     * Возвращает список с доступными путями к шаблонам для мобильного приложения
     * ключ - путь к теме с шаблоном мобильного приложения
     * знычени - наимененование
     *
     * @return array
     * @throws \RS\Event\Exception
     */
    public static function staticTemplatesList()
    {
        $templates_data = self::getTemplatesListFromModules();
        $list      = [];
        if (!empty($templates_data)){
            foreach ($templates_data as $path=>$data){
                $list[$path] = $data['title'];
            }    
        }
        
        return $list;
    }

    /**
     * Возвращает путь к текущей теме оформления для мобильного приложения
     *
     * @return string
     * @throws Exception
     */
    public static function getCurrentThemeModule()
    {
        return \RS\Config\Loader::byModule('mobilesiteapp')->default_theme;
    }

    /**
     * Возвращает текущую директорию с шаблонами для подключения в мобильном приложении
     *
     * @return string
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    function getCurrentThemeTemplatesFolder()
    {
        $templates_data          = self::getTemplatesListFromModules();
        $current_template_module = self::getCurrentThemeModule();
        
        $path = "%".$current_template_module."%";
        if (isset($templates_data[$current_template_module])){ //Получим нужный путь
           $path = $templates_data[$current_template_module]['templates_root'];   
        }
        return $path;
    }

    /**
     * Возвращает текущую директорию с шаблонами для подключения в мобильном приложении
     *
     * @return string
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    function getCurrentThemeMobileRootFolder()
    {
        $templates_data          = self::getTemplatesListFromModules();
        $current_template_module = self::getCurrentThemeModule();
        
        $path = "%".$current_template_module."%";
        if (isset($templates_data[$current_template_module])){ //Получим нужный путь
           $path = $templates_data[$current_template_module]['mobile_root'];   
        }
        return $path;
    }
    
    /**
    * Возврашает реальный относительный путь к модулю к шаблонам исходя их пути указанному с '%имямодуля%"
    * 
    * @param string $current_template_module - текущий модуль мобильного приложения
    * 
    * @return string
    */
    function getRelativeCurrentModuleTemplatePath($current_template_module)
    {
        return \Setup::$FOLDER.\Setup::$MODULE_FOLDER."/".mb_strtolower($current_template_module)."/appsource";
    }
    
    /**
    * Возврашает реальный относительный путь к модулю в папку view исходя их пути указанному с '%имямодуля%"
    * 
    * @param string $current_template_module - имя модуля который обрабатывает приложение
    * 
    * @return string
    */
    function getRelativeCurrentModuleViewPath($current_template_module)
    {
        return \Setup::$FOLDER.\Setup::$MODULE_FOLDER."/".mb_strtolower($current_template_module)."/view";
    }


    /**
     * Возвращает массив из переменных с относительными путями модуля для шаблона, css, js, картинок и т.д.
     *
     * @return array
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    function getResourseVariables()
    {
        $templates_data          = self::getTemplatesListFromModules();
        $current_template_module = self::getCurrentThemeModule();
        
        $resourse = [];
        
        if (isset($templates_data[$current_template_module])){
            $module_current_path     = \Setup::$FOLDER.\Setup::$MODULE_FOLDER."/".mb_strtolower($current_template_module);
            $module_template_path    = $this->getRelativeCurrentModuleTemplatePath($current_template_module);    
            $module_view_path        = $this->getRelativeCurrentModuleViewPath($current_template_module);    
                 
            return [
                'module_tpl'     => $module_view_path,
                'mobile_css'     => mb_strtolower(str_replace('%MOBILEROOT%', $module_template_path, $templates_data[$current_template_module]['css'])),
                'mobile_js'      => mb_strtolower(str_replace('%MOBILEROOT%', $module_template_path, $templates_data[$current_template_module]['js'])),
                'mobile_fonts'   => mb_strtolower(str_replace('%MOBILEROOT%', $module_template_path, $templates_data[$current_template_module]['fonts'])),
                'mobile_img'     => mb_strtolower(str_replace('%MOBILEROOT%', $module_template_path, $templates_data[$current_template_module]['img'])),
                'mobile_www'     => mb_strtolower(str_replace('%MOBILEROOT%', $module_template_path, $templates_data[$current_template_module]['mobile_root'])),
                'mobile_root'    => mb_strtolower(str_replace('%MOBILEPATH%', $module_current_path, $templates_data[$current_template_module]['mobile_root'])),
                'templates_root' => mb_strtolower(str_replace('%MOBILEPATH%', $module_current_path, $templates_data[$current_template_module]['templates_root'])),
            ];
        }
        return $resourse;
    }


    /**
     * Проверяет путь к шаблону
     *
     * @param string $template_path - путь к шаблону
     * @return string
     */
    function checkTemplatePath($template_path)
    {
        return str_replace(["../", "./"], ["", ""], $template_path);
    }

    /**
     * Проверяет существует шаблон или нет
     *
     * @param string $relative_template_path - относительный путь к шаблону
     * @return boolean
     * @throws Exception
     */
    function checkTemplateExists($relative_template_path)
    {
        //Посмотрим шаблон в наших путях
        $template_path  = str_replace("%MOBILEPATH%", "", $relative_template_path);
        $theme          = \RS\Theme\Manager::getCurrentTheme('theme');
        $current_module = \MobileSiteApp\Model\TemplateManager::getCurrentThemeModule(); //Текущий модуль который отвечает за мобильное приложение
        $in_theme_path  = \Setup::$SM_TEMPLATE_PATH.$theme.\Setup::$MODULE_WATCH_TPL."/".$current_module.'/'.mb_strtolower($template_path);

        //Проверим сначала свой путь
        if (file_exists($in_theme_path) || (file_exists(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$current_module.'/'.mb_strtolower($template_path)))) {
            return true;
        }
        return false;
    }

    /**
     * Возвращает список шаблоно по определённому пути
     *
     * @param string $folder - папка из которой надо достать пути.
     * @param string $exclude_path - путь который надо отбросить
     * @return array
     */
    private function getFolderAndFiles($folder, $exclude_path)
    {
        $paths_array = [];
        $paths = array_slice(scandir($folder), 2);

        if (!empty($paths)){ //Пройдемся по файлам и папкам, чтобы получить все пути
            foreach ($paths as $path) {
                $file_or_dir = $folder."/".$path; //папка или файл, для составления массива
                if (is_dir($file_or_dir)){
                    $paths_array = array_merge($paths_array, $this->getFolderAndFiles($file_or_dir, $exclude_path));
                }else{
                    $ext = pathinfo($file_or_dir, PATHINFO_EXTENSION); //Смотрим окончание
                    if ($ext == 'tpl'){ //Берём только tpl файлы
                        //Добавим путь, но вырежем ненужные части
                        $paths_array[] = ltrim(str_replace([$exclude_path, ".tpl"], ["", ""], $file_or_dir), "/");
                    }
                }
            }
        }
        return $paths_array;
    }

    /**
     * Возвращает список из всех путей
     *
     * @throws \RS\Exception
     * @return array
     */
    function getAllTemplatesPaths()
    {
        //Получим пути исходя из настроек
        $current_module = \MobileSiteApp\Model\TemplateManager::getCurrentThemeModule(); //Получим текущую тему модуля

        $paths_array = [];
        //Посмотрим пути в модуле
        $in_module_path = \Setup::$PATH.\Setup::$MODULE_FOLDER.DIRECTORY_SEPARATOR.$current_module.\Setup::$MODULE_TPL_FOLDER.DIRECTORY_SEPARATOR;

        if (file_exists($in_module_path)){
            if (!file_exists($in_module_path."pages")){ //Если такой папки нет у самого модуля, то мы его создадим
                throw new \RS\Exception(t('Не создан путь к шаблонам модулей %0', $in_module_path."pages"));
            }
            $paths_array = $this->getFolderAndFiles($in_module_path."pages", $in_module_path);
            if (empty($paths_array)){
                throw new \RS\Exception(t('Нет шаблонов мобильного приложения. Установлен активный модуль "%0"', $current_module));
            }
        }

        $theme_paths_array = [];
        //Посмотрим пути в текущей теме
        $theme         = \RS\Theme\Manager::getCurrentTheme('theme'); //Текущая тема оформления
        $in_theme_path = \Setup::$SM_TEMPLATE_PATH.$theme.\Setup::$MODULE_WATCH_TPL.DIRECTORY_SEPARATOR.$current_module;
        if (file_exists($in_theme_path)) {
            $theme_paths_array = $this->getFolderAndFiles($in_theme_path, $in_theme_path);
        }

        return array_unique(array_merge($paths_array, $theme_paths_array));
    }

    /**
     * Возвращает массив шаблоны в подготовленном формате для расширения приложения для JSON
     *
     * @param \RS\View\Engine $view - объект движка для рендеринга
     *
     * @return array
     * @throws Exception
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function getTemplatesJSONPrepared($view)
    {
        //Получим все ключи шаблонов
        $paths_keys = $this->getAllTemplatesPaths();

        $paths = [];
        //Получившиеся ключи массива шаблонов мы превращаем в пути шаблонов и парсим
        if (!empty($paths_keys)){
            foreach ($paths_keys as $path_key){
                $template        = $this->getCurrentThemeTemplatesFolder().'/'.$path_key.".tpl";
                $wrapped_content = $view->fetch($template);
                $view->assign([
                    'wrapped_content' => $wrapped_content
                ]);
                $html = $view->fetch('wrapper.tpl');
                $paths[$path_key] = $html;
            }
        }
        return $paths;
    }
}