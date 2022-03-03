<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class XmlReader extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('PHP модуль XMLReader');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректного чтения больших XML файлов требуется модуль XMLReader');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return class_exists('\XMLReader', false);
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Включите или установите модуль XMLReader в настройках вашего хостинга');
    }
}