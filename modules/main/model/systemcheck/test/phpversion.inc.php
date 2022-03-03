<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class PhpVersion extends AbstractSystemTest
{
    const
        NEED_PHP_VERSION = '7.1';

    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Версия PHP');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Версия PHP должна быть %version или выше', ['version' => self::NEED_PHP_VERSION]);
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return version_compare(phpversion(), self::NEED_PHP_VERSION) >= 0;
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Установите на хостинге версию PHP, не ниже %version', ['version' => self::NEED_PHP_VERSION]);
    }
}