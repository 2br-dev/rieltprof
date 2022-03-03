<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\SystemCheck\Test;
use Main\Model\SystemCheck\AbstractSystemTest;

class SqlMode extends AbstractSystemTest
{
    /**
     * Возвращает название теста
     *
     * @return string
     */
    function getTitle()
    {
        return t('Strict режим работы Mysql должен быть отключен');
    }

    /**
     * Возвращает описание теста
     *
     * @return string
     */
    function getDescription()
    {
        return t('ORM подсистема ReadyScript требует не строгий режим формирования SQL запросов. Это определяется в настройках MySQL');
    }

    /**
     * Выполняет тест, возвращает true  случае успеха, в противном случае false
     *
     * @return bool
     */
    function test()
    {
        return \RS\Db\Adapter::sqlExec('SELECT @@session.sql_mode as sql_mode')->getOneField('sql_mode') == '';
    }

    /**
     * Возвращает рекомендации для прохождения теста. Метод вызывается, если test() вернул false
     *
     * @return string
     */
    function getRecommendation()
    {
        return t('Опция sql_mode в настройках MySQL (my.cnf) должна иметь пустое значение (sql_mode="")');
    }
}