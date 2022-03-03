<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Behavior;

/**
 * Абстрактный класс Библиотеки методов. Используйте данный класс
 * в качестве родительского, чтобы создавать свои наборы методов и прикреплять их к другим классам
 */
abstract class BehaviorAbstract
{
    protected $owner;

    /**
     * Устанавливает идентификатор основного класса
     *
     * @param mixed $owner
     */
    function _init($owner)
    {
        $this->owner = $owner;
    }
}
