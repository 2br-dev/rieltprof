<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class ZipArchive extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Возможность распаковки Zip архивов');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректной работы обмена с 1С, загрузки фото из ZIP архивов, установки модуелй из маркетплейса и многих других функций требуется наличие функции распаковки архивов.');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return class_exists('ZipArchive');
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Включите или установите в настройках вашего хостинга модуль ZipArchive');
    }
}