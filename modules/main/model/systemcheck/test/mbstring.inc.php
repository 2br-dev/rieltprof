<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class MbString extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('PHP модуль MbString с опцией func_overload = 0 или 1');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('Шаблонизатор Smarty, который является компонентом ReadyScript требует модуль mb_string с опцией func_overload = 0 или 1');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        $func_overload = ini_get('mbstring.func_overload');
        return function_exists('mb_strlen') && ($func_overload == 0 || $func_overload == 1);
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Включите или установите модуль mbstring в настройках вашего хостинга и установите mbstring.func_overload в php.ini в значение 0 или 1');
    }
}