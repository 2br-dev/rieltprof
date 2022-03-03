<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module;

/**
* Интерфейс классов инсталяции модулей
*/
interface InstallInterface
{
    /**
    * Выполняет установку модуля. Вызывается когда модуль уже скопирован в основную папку.
    * 
    * @return bool
    */
    public function install();
    
    /**
    * Обновляет модуль, приводит в соответствие базу данных.
    * Вызывается, когда текущий модуль уже был установлен раннее.
    * 
    * @return bool
    */
    public function update();
    
    /**
    * Должен возвращать true, в случае, если модуль поддерживает вставку демонстрационных данных, иначе - false
    * 
    * @return bool
    */
    public function canInsertDemoData();
    
    /**
    * Устнавливает демонстрационные данные.
    * 
    * @return bool
    */
    public function insertDemoData();
    
    /**
    * Должен возвращать ошибки, возникшие в процессе установки
    * 
    * @return array of error messages
    */
    public function getErrors();
    
    /**
    * Выполняется, после того, как были установлены все модули.
    * Здесь можно устанавливать настройки, которые связаны с другими модулями.
    * 
    * @param array $options параметры установки
    * @return bool
    */
    public function deferredAfterInstall($options);
}