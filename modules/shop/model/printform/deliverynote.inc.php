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
class DeliveryNote extends AbstractPrintForm
{
    /**
    * Возвращает краткий символьный идентификатор печатной формы
    * 
    * @return string
    */
    function getId()
    {
        return 'deliverynote';
    }
    
    /**
    * Возвращает название печатной формы
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Лист доставки');
    }
    
    /**
    * Возвращает шаблон формы
    * 
    * @return string
    */
    function getTemplate()
    {
        return '%shop%/printform/deliverynote.tpl';
    }
}