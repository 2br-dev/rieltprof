<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\AccessControl\AutoCheckers;

/**
* Абстрактный клас для объектов автоматической проверки прав
*/
interface AutoCheckerInterface
{
    /**
    * Если условия проверки соблюдены - проверят наличие права
    * Возвращает текст ошибки или false
    * 
    * @param array $params - параметры для проверки
    * @return string|false
    */
    public function checkError($params);

    /**
     * Возвращает тип объекта автоматической проверки прав
     *
     * @return string
     */
    public static function getCheckerType();
}
