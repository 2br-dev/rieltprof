<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model;  

/**
* Класс АПИ модуля мобильного приложения
*/
class ExtendApi
{
    const
        EXTEND_API_CACHE_TAG = 'mobilesiteapp_extend_cache_tag';
    
    protected static 
        $json_map = []; //Массив с функциями для расширения
    
    /**
    * Добавляет в массив JSON карту для расширения JS
    * 
    * @param string $file - путь к файлу
    */
    private static function appendJSONMapFromFile($file)
    {
        $info = pathinfo($file);
        if ($info['extension'] == 'js'){ //Нам нужны только js файлы
            $content = file_get_contents($file); //Содержимое функции
            $tree    = explode(".", $info['filename']);
            
            $class  = @(string)$tree[0];
            $method = @(string)$tree[1];
            self::$json_map[$class][$method] = $content;
        }
    }
    
    /**
    * Метод собирает javascript с разных модулей и объединяет в один JSON
    * Возвращает JSON с функциями для расширения функционала
    * 
    * @param string $app_module_name - наименование модуля с приложением для телефона
    * @param boolean $cache - использовать кэш?
    * @return array
    */
    public static function getExtendsJSON($app_module_name = "mobilesiteapp", $cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                    ->tags(self::EXTEND_API_CACHE_TAG)
                    ->request(['\MobileSiteApp\Model\ExtendApi', 'getExtendsJSON'], $app_module_name, false);
        }else{ 
            //Получим список установленных и активных модулей модулей.
            $module_manager = new \RS\Module\Manager();
            $active_modules = $module_manager->getActiveList();
            
            //Пеберём молули и надём в папках соответствующие файлы
            foreach ($active_modules as $active_module){
                /**
                * @var \RS\Module\Item $active_module 
                */
                $module_folder = $active_module->getFolder();
                $scan_folder   = $module_folder.\Setup::$MODULE_TPL_FOLDER.DIRECTORY_SEPARATOR."jshook".DIRECTORY_SEPARATOR.$app_module_name.DIRECTORY_SEPARATOR;

                if (file_exists($scan_folder)){ //Если директорию нашли
                    $dirs = scandir($scan_folder);

                    foreach ($dirs as $file){
                        if (($file == '.') || ($file == '..')){ //Пропустим директории
                            continue;
                        }
                        self::appendJSONMapFromFile($scan_folder.$file);
                    }
                }
            }
            return self::$json_map;
        }
    }
    
    /**
    * Проверяет авторизационный токен, действителен он или нет
    * 
    * @param string $token - строка идентификатора токена
    *
    * @return boolean
    */
    function checkToken($token)
    {
        $token_obj = new \ExternalApi\Model\Orm\AuthorizationToken();
        if (!$token_obj->load($token) || $token_obj['expire'] < time()){
            return false;
        } 
        return true;
    }
}