<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck;

/**
 * Абстрактный класс одного системного теста
 */
abstract class AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    abstract public function test();

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    public function getRecommendation()
    {}
}