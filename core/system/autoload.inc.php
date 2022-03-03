<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

/**
* Класс создан для установки дополнительных параметров функции __autoload
*/
class Autoload 
{
    private static  $require_path;
    
    function __construct()
    {
        self::restoreDefaultPath(); //Инициализируем стандартные пути к автозагрузке.
        spl_autoload_register([$this, 'autoload']);
    }
    
    /*
    * Автозагрузка классов.
    * Если класс начинается с последовательности RS\, значит это класс ядра, иначе - это класс модуля
    * Класс ищется в соответствующей пространству имен папке с расширениями: .my.inc.php и .inc.php 
    * Поддержка .my.inc.php осуществляется для возможности кастомизации системных классов.
    * Классы с расширениями .my.inc.php - не изменяются при обновлении системы.
    * При использовании кастомных классов и регулярном обновлении системы, стабильность системы не гарантируется,
    * т.к. кастомные классы могут утратить актуальность и не соответствовать обновленному состоянию системы
    * 
    * @return void
    */
    function autoload($class_name) 
    {
        $class_name = strtolower(str_replace(['\\', '.'], ['/', ''], $class_name));
        $require_path = self::getPath();
        
        if ($class_name[0] == 'r' && $class_name[1] == 's' && $class_name[2] == '/') {
            //Классы ядра
            $class_path = $require_path['systemClass'].$class_name;
        } else {
            //Классы модулей
            $class_path = $require_path['moduleClass'].$class_name;
        }
        
        $custom_class = $class_path.'.'.\Setup::$CUSTOM_CLASS_EXT;
        $class = $class_path.'.'.\Setup::$CLASS_EXT;
        
        if (file_exists( $custom_class )) {
            require( $custom_class );
        } 
        elseif (file_exists( $class )) {
            require( $class );
        }
    }
    
    /**
    * Устанавливает пути к каталогам с классами. Удобно применять если нужно искать классы во временных папках.
    * 
    * @param string $moduleClass путь к классам модулей
    * @param string $systemClass путь к системным классам
    * @return void
    */
    public static function setPath($moduleClass, $systemClass = null)
    {
        if (isset($moduleClass)) self::$require_path['moduleClass'] = $moduleClass;
        if (isset($systemClass)) self::$require_path['systemClass'] = $systemClass;
    }
    
    /**
    * Возвращает массив текущих путей для поиска классов
    * 
    * @return array
    */
    public static function getPath() 
    {
        return self::$require_path;
    }    
    
    /**
    * Устанавливает пути к классам, определенные по умолчанию
    * 
    * @return void
    */
    public static function restoreDefaultPath()
    {
        self::$require_path = [
                'systemClass' => \Setup::$PATH."/core/",
                'moduleClass' => \Setup::$PATH."/modules/"
        ];
    }
}

new Autoload();
