<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

/**
* Интерфейс классов деинсталяции модулей
*/
interface UninstallInterface
{
    /**
    * Подготавливает модуль к последующему удалению с диска. В этом методе должны быть описаны действия по
    * - удалению зависимостей от данного модуля, 
    * - удалению таблиц из БД, если таковые имеются
    * - удалению пунктов меню, созданных данным модулем
    * 
    * @return bool возвращает true, если подготовка к удалению прошла успешно, в случае, 
    * если будет возвращен false - физическое удаление модуля с диска не произойдет
    */
    public function uninstall();
    
    /**
    * Должен возвращать ошибки, возникшие в процессе удаления модуля
    * 
    * @return array of error messages
    */
    public function getErrors();    
    
}
