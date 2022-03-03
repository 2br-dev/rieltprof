<?php
/**
* ReadyScript Resource File Extension
* Расширение стандартного типа ресурсов "file".
* Добавляем поддержку в пути к шаблону включений %SYSTEM%, %THEME%, %ИМЯ_МОДУЛЯ%
* 
*/
class Smarty_Resource_RS extends Smarty_Internal_Resource_File {

    protected function buildFilepath(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        //Если в пути к шаблону присутствует %имя модуля%, то заменяем на пути к шаблонам модуля
        if (preg_match('/^%(.*?)%(.+)$/u', $source->name, $match)) {
            $theme = \RS\Theme\Manager::getCurrentTheme('theme');
            $match[2] = ltrim($match[2], '/'); //Удаляем первый слеш из имени шаблона, чтобы можно было использовать '%ИМЯ_МОДУЛЯ%/ИМЯ_ШАБЛОНА'

            if (strtoupper($match[1]) == 'SYSTEM') {

                $in_custom_path = \Setup::$SM_TEMPLATE_PATH . 'mysystem/' . $match[2];
                if (file_exists($in_custom_path)) {
                    $source->name = $in_custom_path;
                } else {
                    $source->name = \Setup::$SM_TEMPLATE_PATH . 'system/' . $match[2];
                }

            } elseif (strtoupper($match[1]) == 'THEME') {

                //Меняем %THEME% на папку с текущей темой
                $in_theme = \Setup::$SM_TEMPLATE_PATH . $theme . '/' . $match[2];
                $in_theme_my = $this->getMyCustomFilename($in_theme);
                $source->name = file_exists($in_theme_my) ? $in_theme_my : $in_theme;
         
            }elseif (strtoupper($match[1]) == 'MOBILEPATH') { 
                //Если указана папка для мобильного приложения
                $current_module = \MobileSiteApp\Model\TemplateManager::getCurrentThemeModule(); //Текущий модуль который отвечает за мобильное приложение
                $path = str_replace("view/", "", $match[2]);
                $in_theme_path  = \Setup::$SM_TEMPLATE_PATH.$theme.\Setup::$MODULE_WATCH_TPL."/".$current_module.'/'.mb_strtolower($path);

                //Проверим сначала свой путь
                if (file_exists($in_theme_path)) {
                    $source->name = $in_theme_path;
                }else{
                    $source->name = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$current_module.'/'.mb_strtolower($match[2]);
                }   
            }
            else {
                //Если указано %ИМЯ_МОДУЛЯ%
                $in_theme_path = \Setup::$SM_TEMPLATE_PATH . $theme . \Setup::$MODULE_WATCH_TPL . '/' . strtolower($match[1]) . '/' . $match[2];
                $in_theme_path_my = $this->getMyCustomFilename($in_theme_path);
                  
                if (file_exists($in_theme_path_my)) { //.my.tpl
                    $source->name = $in_theme_path_my;
                } elseif(file_exists($in_theme_path)) { //.tpl
                    $source->name = $in_theme_path;
                } else { //Путь из папки модуля
                    $in_theme_path    = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $match[1] . \Setup::$MODULE_TPL_FOLDER . '/' . $match[2];
                    $in_theme_path_my = $this->getMyCustomFilename($in_theme_path);
                    $source->name = file_exists($in_theme_path_my) ? $in_theme_path_my : $in_theme_path;
                }
            }
        }

        elseif ($theme_mod_path = $source->smarty->getTemplateDir('theme_module')) {
            $custom_path = $this->getMyCustomFilename( $theme_mod_path.$source->name );
            if (file_exists($custom_path)) {
                $source->name = $custom_path;
            }
        }
        
        return parent::buildFilepath($source, $_template);
    }

    /**
     * Добавляет к расширению префикс .my
     *
     * @param string $filename Имя файла
     * @return string
     */
    private function getMyCustomFilename($filename)
    {
        return str_replace('.tpl', '.my.tpl', $filename);
    }
    
    /**
     * Сделаем читаемые имена компилированных шаблонов.
     *
     * @param Smarty_Template_Compiled $compiled  compiled object
     * @param Smarty_Internal_Template $_template template object
     */
    public function populateCompiledFilepath(Smarty_Template_Compiled $compiled, Smarty_Internal_Template $_template)
    {
        if (DS != '/') {
            //windows
            $_path = str_replace('/', '\\', \Setup::$PATH);
        } else {
            //other
            $_path = str_replace('\\', '/', \Setup::$PATH);
        }
        
        $_path = mb_strtolower($_path);
        $uniq_filename = str_replace($_path, '', mb_strtolower($_template->source->filepath));
        $uniq_filename = trim(preg_replace('![^\w\|]+!', '_', $uniq_filename),'_');
        
        $_compile_dir = $_template->smarty->getCompileDir();
        
        // caching token
        $_cache = ($_template->caching) ? '.cache' : '';
        $compiled->filepath = $_compile_dir . $uniq_filename . '.' . $compiled->source->type . $_cache . '.tpl.php';
    }
    
    
    
}

