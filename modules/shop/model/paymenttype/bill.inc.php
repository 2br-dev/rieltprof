<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PaymentType;
use RS\Orm\FormObject;
use \RS\Orm\Type;
use Shop\Model\PaymentApi;

/**
* Способ оплаты - счет
*/
class Bill extends AbstractType
{
    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Безналичный расчет (счет)');
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Предусматривает формирование счета по основным реквизитам компании');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'bill';
    }


    /**
     * Возвращает true, если данный тип подразумевает наложенный платеж при оплате заказа
     *
     * @return bool
     */
    function cashOnDelivery()
    {
        return false;
    }


    /**
    * Возвращает список названий документов и ссылки, по которым можно открыть данные документы, 
    * генерируемых данным типом оплаты
    * 
    * @return array
    */
    function getDocsName()
    {
        return [
            'bill' => [
                'title' => t('Счет')
            ]
        ];
    }
    
    /**
    * Возвращает ORM объект для генерации формы или null
    * 
    * @return \RS\Orm\FormObject | null
    */
    function getFormObject()
    {
        $company = new \Shop\Model\Orm\Company();
        $properties = new \RS\Orm\PropertyIterator([
            'number_prefix' => new Type\Varchar([
                'description' => t('Префикс для номера счета')
            ]),
            'seal_url' => new Type\Varchar([
                'description' => t('Ссылка на изображение печати')
            ]),
            'use_site_company' => new Type\Integer([
                'description' => t('Использователь основные реквизиты компании'),
                'checkboxView' => [1,0],
                'template' => '%shop%/payment/bill_use_site_company.tpl'
            ])
            ] + $company->getPropertyIterator()->export());

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }
    
    /**
    * Возвращает объект компании, которая предоставляет услуги
    * 
    * @return \Shop\Model\Orm\Company
    */
    function getCompany()
    {
        if (!$this->getOption('use_site_company')) {
            $company = new \Shop\Model\Orm\Company();
            $company->getFromArray($this->getOption());
            return $company;
        }
        return parent::getCompany();
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
    
    /**
    * Возвращает html документа для печати пользователем
    * 
    * @param mixed $dockey
    */    
    function getDocHtml($dockey = null)
    {        
        $view = new \RS\View\Engine();
        $view->assign([
            'bill' => $this
        ]);
        // Счет по оплате заказа
        if($this->order){
            $view->assign('order', $this->order);
            return $view->fetch('%shop%/payment/bill.tpl');
        }
        // Счет по оплате транзации пополнения счета
        if($this->transaction){
            $view->assign('transaction', $this->transaction);
            return $view->fetch('%shop%/payment/bill_transaction.tpl');
        }
        
        throw new \Exception(t('Невозможно сформировать документ. Не передан ни объект заказа, ни объект транзакции'));
    }
    
    /**
    * Возвращает URL для перехода на сайт сервиса оплаты для совершения платежа
    * Используется только для Online-платежей
    * 
    * @param Transaction $transaction
    * @return string
    */
    function getPayUrl(\Shop\Model\Orm\Transaction $transaction)
    {
        return $this->getDocUrl('bill');
    }


    /**
     * Возвращает строку-ссылку на QR код
     *
     * @param int $width Ширина QR-кода
     * @param int $height Высота QR-кода
     * @param bool $absolute Если true, то абсолютный URL
     * @return string
     */
    function getQrCodeUrl($width = 200, $height = 200, $absolute = false)
    {
        return PaymentApi::getQrCodeUrl($this->order ?? $this->transaction, $width, $height, $absolute);
    }
}
