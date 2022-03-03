<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class McryptOrOpenssl extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('PHP модуль MCrypt или OpenSSL');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректной работы ReadyScript необходимо наличие модуля Mcrypt с возможностью шифрования TWO_FISH или модуля OpenSSL');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return (function_exists('mcrypt_decrypt') && @mcrypt_module_open('twofish', '', 'ecb', '') !== false)
        || (function_exists('openssl_public_decrypt'));
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Включите или установите в настройках вашего хостинга один из двух требуемых модулей');
    }
}