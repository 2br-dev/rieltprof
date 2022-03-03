<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

/**
* Базовый класс, отвечающий за удаление модуля. Выполняет роль деустановщика "по-умолчанию" для модуля.
* Вызывается с параметром $module_name в конструкторе, если у модуля не определн собственный класс МОДУЛЬ/Config/Uninstall
*/
class AbstractUninstall implements \RS\Module\UninstallInterface
{
    protected
        $module,
        $errors = [];
    
    /**
    * Конструктор класса
    * 
    * @param string $module_name - имя модуля
    * @return AbstractUninstall
    */
    function __construct($module_name = null)
    {
        $this->module = $module_name ?: Item::nameByObject($this);
    }

    /**
    * Подготавливает модуль к удалению
    * 
    * @return bool
    */
    function uninstall()
    {
        $orm_objects = $this->findOrmObjects();
        foreach($orm_objects as $object) {
            if (!$object->dropTable()) {
                $this->addError(t("Ошибка при удалении таблицы объекта %0", [get_class($object)]));
            }
        }
        
        return !count($this->errors);
    }

    
    /**
    * Возвращает список ORM объектов, находящихся в указанной папке
    * 
    * @param mixed $base - путь к корневой папке orm объектов
    * @param mixed $subfolder - путь к объектам, отностельно корневой папки
    * @param mixed $prefix - текст, приписываемый вначале к имени класса
    * @return array of \RS\Orm\AbstractObject
    */
    protected function findOrmObjects($base = null, $subfolder = '', $prefix = null)
    {
        if ($base === null) {
            $base = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$this->module.'/model/orm/';
            $prefix = '\\'.$this->module.'\model\orm\\';
        }
        
        $result = [];
        $dir = $base.$subfolder;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') continue;
                    if (is_dir($dir.$file)) {
                        $result = array_merge($result, $this->findOrmObjects($base, $subfolder.$file.'/', $prefix));
                    } else {
                        $classname =  $prefix. str_replace('/', '\\', $subfolder.str_replace('.'.\Setup::$CLASS_EXT, '', $file));
                        if (is_subclass_of($classname, '\RS\Orm\AbstractObject')) {
                            $result[] = new $classname();
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $result;
    }
    
    /**
    * Добавляет ошибку в список
    * 
    * @param string $message
    * @return bool(false)
    */
    function addError($message)
    {
        $this->errors[] = $message;
        return false;
    }
    
    /**
    * Возвращает список ошибок 
    * 
    * @return array
    */
    function getErrors()
    {
        return $this->errors;
    }
}