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
class Torg12 extends AbstractTorgForm
{
    /**
     * Возвращает краткий символьный идентификатор печатной формы
     *
     * @return string
     */
    function getId()
    {
        return 'torg12';
    }

    /**
     * Возвращает название печатной формы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Торг 12');
    }

    /**
     * Возвращает шаблон формы
     *
     * @return string
     */
    function getTemplate()
    {
        return '%shop%/printform/torg12.tpl';
    }
}
