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
class TransportnayaNakladnaya extends AbstractTorgForm
{
    /**
     * Возвращает краткий символьный идентификатор печатной формы
     *
     * @return string
     */
    function getId()
    {
        return 'transportnayanakladnaya';
    }

    /**
     * Возвращает название печатной формы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Транспортная накладная');
    }

    /**
     * Возвращает шаблон формы
     *
     * @return string
     */
    function getTemplate()
    {
        return '%shop%/printform/transportnayanakladnaya.tpl';
    }
}
