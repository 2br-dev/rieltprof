<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType;

/**
* Заглушка рассчетного класса доставки. Возвращается в случае отсутствия реального класса доставки.
*/
class Stub extends AbstractType
{
    protected 
        $name;
        
    /**
    * Конструктор 
    * 
    * @param string $name Короткое имя реального класса, который не удается загрузить
    * @return Stub
    */
    function __construct($name)
    {
        $this->name = $name;
    }
        
    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t("Класс доставки '{$this->name}' удален");
    }
    
    /**
    * Возвращает описание типа доставки
    * 
    * @return string
    */
    function getDescription()
    {
        return t("Класс доставки '{$this->name}' не найден. Возможно этот модуль оплаты был удален");
    }
    
    /**
    * Возвращает идентификатор данного типа доставки. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return $this->name;
    }

    /**
    * Возвращает стоимость доставки для заданного заказа. Только число. Всегда в базовой валюте
    * 
    * @param \Shop\Model\Orm\Order $order
    * @param \Shop\Model\Orm\Address $address - Адрес доставки
    * @return double
    */
    function getDeliveryCost(\Shop\Model\Orm\Order $order, \Shop\Model\Orm\Address $address = null, \Shop\Model\Orm\Delivery $delivery, $use_currency = true)
    {
        return 0;
    }
    
}
