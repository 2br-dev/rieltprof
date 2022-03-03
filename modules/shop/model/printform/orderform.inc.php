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
class OrderForm extends AbstractPrintForm
{
    /**
    * Возвращает краткий символьный идентификатор печатной формы
    * 
    * @return string
    */
    function getId()
    {
        return 'orderform';
    }
    
    /**
    * Возвращает название печатной формы
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Заказ');
    }
    
    /**
    * Возвращает шаблон формы
    * 
    * @return string
    */
    function getTemplate()
    {
        return '%shop%/printform/orderform.tpl';
    }
}
