<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PaymentType;
use RS\Orm\FormObject;
use RS\Orm\Type;

/**
* Тип оплаты - наличные
*/
class Cash extends AbstractType
{

    /**
    * Возвращает ORM объект для генерации формы или null
    *
    * @return \RS\Orm\FormObject | null
    */
    function getFormObject()
    {
        $properties = new \RS\Orm\PropertyIterator([
            'is_cash' => new Type\Integer([
                'description' => t('Это наличный расчет?'),
                'hint' => t('Не устанавливайте данный флаг, если расчет будет происходить по карте. Данный флаг могут использовать некоторые модули для фискализации'),
                'checkboxView' => [1,0]
            ])
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

   /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Оплата на месте');
    }
    
    /**
    * Возвращает описание типа оплаты для администратора. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Метод не предусматривает никакого процессинга');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'cash';
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
