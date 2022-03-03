<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PaymentType;

/**
* Заглушка класса оплаты. Возвращается в случае отсутствия реального класса оплаты.
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
        return t("Класс оплаты '{$this->name}' удален");
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t("Класс оплаты '{$this->name}' не найден. Возможно этот модуль оплаты был удален");
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return $this->name;
    }

    /**
    * Возвращает true, если данный тип поддерживает проведение платежа через интернет
    * 
    * @return bool
    */
    function canOnlinePay()
    {
        return false;
    } 
}
