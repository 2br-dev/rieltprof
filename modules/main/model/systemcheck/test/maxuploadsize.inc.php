<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class MaxUploadSize extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Возможность загрузки файлов 50 MB');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        $max_upload_size = \RS\File\Tools::fileSizeToStr( \RS\File\Tools::getMaxPostFileSize() );
        return t('Для комфортной работы с системой, переменным upload_max_filesize и post_max_size в PHP.ini рекомендуется установить значение не менее 50 MB. У вас установлено: %0', [$max_upload_size]);
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return \RS\File\Tools::getMaxPostFileSize() > 50000000;
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Установите upload_max_filesize = 50M и post_max_size = 50M в PHP.ini');
    }
}