<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CashRegisterType;

/**
* Заглушка онлайн касс. Возвращается в случае отсутствия реального класса обмена информацией с кассами.
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
    * Возвращает название модуля обмена с онлайн кассами
    * 
    * @return string
    */
    function getTitle()
    {
        return t("Класс онлайн касс '%0' удален", [$this->name]);
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t("Класс онлайн касс '%0' не найден. Возможно этот модуль оплаты был удален", [$this->name]);
    }
    
    /**
    * Возвращает идентификатор данного онлайн касс. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return $this->name;
    }
    
    /**
    * Создаёт транзакцию на выставление чека в ОФД
    * 
    * @param \Shop\Model\Orm\Transaction $transaction - объект транзакции
    * @param string $operation_type - тип чека
    * @return string
    */
    function createReceipt(\Shop\Model\Orm\Transaction $transaction, $operation_type = 'sell'){
        $this->addError(t('Несуществующуй провайдер ККТ'));
        return $this->getErrorsStr();
    }

    /**
     * Отправляет запрос на создание чека корректировки
     *
     * @param $transaction_id - id транзакции
     * @param $form_object - объект с заполненными данными формы, возвращенной методом getCorrectionReceiptFormObject
     */
    function createCorrectionReceipt($transaction_id, $form_object){
        $this->addError(t('Несуществующуй провайдер ККТ'));
        return $this->getErrorsStr();
    }
    
    /**
    * Делает запрос на запрос статуса чека и возвращаетданные записывая их в чек, если произошли изменения
    * 
    * @param \Shop\Model\Orm\Receipt $receipt - объект чека
    * @return string
    */
    function getReceiptStatus(\Shop\Model\Orm\Receipt $receipt){
        return t('Это ответ заглушки для '.$this->getTitle()); 
    }
    
    /**
    * Функция обработки запроса продажи от провайдера чека продажи
    * 
    * @param \RS\Http\Request $request - объект запроса
    * @return string
    */
    function onResultSell(\RS\Http\Request $request){
       return t('Это ответ заглушки для '.$this->getTitle()); 
    }
    
    /**
    * Функция обработки запроса продажи от провайдера чека возврата
    * 
    * @param \RS\Http\Request $request - объект запроса
    * @return string
    */
    function onResultRefund(\RS\Http\Request $request){
       return t('Это ответ заглушки для '.$this->getTitle()); 
    }
    
    /**
    * Функция обработки запроса продажи от провайдера чека коррекции
    * 
    * @param \RS\Http\Request $request - объект запроса
    * @return string
    */
    function onResultCorrection(\RS\Http\Request $request){
       return t('Это ответ заглушки для '.$this->getTitle()); 
    }
}