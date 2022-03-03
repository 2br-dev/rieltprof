<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class SendMail extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Возможность отправлять почту командой mail()');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Для корректной работы рассылки уведомлений, сервер должен позволять отправлять письма с помощью команды mail()');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return function_exists('mail') && mail('test@example.com', 'Test Subject', 'Test Message');
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Произведите настройку sendmail на вашем сервере');
    }
}