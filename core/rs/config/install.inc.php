<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Config;

use RS\Module\ModuleLicense;

/**
* Класс отвечает за установку и обновление ядра ReadyScript
*/
class Install
{
    private
        /**
        * @var \RS\Module\AbstractPatches
        */    
        $patches;
        
    function __construct()
    {
        $this->patches = new Patches();
    }
    
    /**
    * Выполняет установку модуля. Вызывается когда модуль уже скопирован в основную папку.
    * 
    * @return bool
    */
    public function install()
    {
        return $this->update();
    }
    
    /**
    * Обновляет модуль, приводит в соответствие базу данных.
    * Вызывается, когда текущий модуль уже был установлен раннее.
    * 
    * @return bool
    */
    public function update()
    {
        $this->patches->runBeforeUpdatePatches();
        
        \RS\Cache\Cleaner::obj()->clean(\RS\Cache\Cleaner::CACHE_TYPE_FULL); //Очищаем весь кэш
        
        //Устанавливаем хранилище для конфигураций модулей
        $config_storage = new \RS\Module\ModuleConfig();
        $config_storage->dbUpdate();
        
        //Устанавливаем хранилище ключ -> значение
        $hashstore = new \RS\HashStore\Api();
        $hashstore->dbUpdate();

        //Лицензия для модуля в маркетплейсе
        $module_license = new ModuleLicense();
        $module_license->dbUpdate();

        
        $this->patches->runAfterUpdatePatches();        
        
        return true;
    }
    
    /**
    * Возвращает ошибки, возникшие в процессе установки
    * 
    * @return array of error messages
    */
    public function getErrors()
    {
        return [];
    }
}