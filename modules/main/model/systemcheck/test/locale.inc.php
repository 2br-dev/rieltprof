<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class Locale extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Наличие локали на сервере');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректной работы ReadyScript требуется наличие локали %all и %numeric', [
            'all' => \Setup::$LOCALE,
            'numeric' => \Setup::$NUMERIC_LOCALE
        ]);
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        $ru = setlocale(LC_ALL, \Setup::$LOCALE);
        $en = setlocale(LC_NUMERIC, \Setup::$NUMERIC_LOCALE);
        return $ru && $en;
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Установите на сервере локаль %all и %numeric', [
            'all' => \Setup::$LOCALE,
            'numeric' => \Setup::$NUMERIC_LOCALE
        ]);
    }
}