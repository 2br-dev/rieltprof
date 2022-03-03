<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PrintForm;

/**
 * Обычная печатная форма заказа
 */
class Upd extends AbstractTorgForm
{

    protected
        $amount_on_first_page = 10,
        $amount_on_middle = 20,
        $amount_on_last_page = 5;

    /**
     * Возвращает краткий символьный идентификатор печатной формы
     *
     * @return string
     */
    function getId()
    {
        return 'upd';
    }

    /**
     * Возвращает название печатной формы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Упд');
    }

    /**
     * Возвращает шаблон формы
     *
     * @return string
     */
    function getTemplate()
    {
        return '%shop%/printform/upd.tpl';
    }
}
