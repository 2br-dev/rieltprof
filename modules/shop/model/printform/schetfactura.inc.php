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
class SchetFactura extends AbstractTorgForm
{

    protected
        $amount_on_first_page = 5,
        $amount_on_middle = 17,
        $amount_on_last_page = 10;

    /**
     * Возвращает краткий символьный идентификатор печатной формы
     *
     * @return string
     */
    function getId()
    {
        return 'schetfactura';
    }

    /**
     * Возвращает название печатной формы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Счет-фактура');
    }

    /**
     * Возвращает шаблон формы
     *
     * @return string
     */
    function getTemplate()
    {
        return '%shop%/printform/schetfactura.tpl';
    }
}
