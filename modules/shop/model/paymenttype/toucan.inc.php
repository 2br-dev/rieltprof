<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PaymentType;
use RS\Orm\FormObject;
use \RS\Orm\Type,
    \Shop\Model\Orm\Transaction;


/**
* Тип оплаты - с помощью сервиса 2can.ru
*/
class Toucan extends AbstractType
{
    const
        ENTITY_TOUCAN = '2can';
    
   /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Оплата курьеру картой через сервис 2can');
    }
    
    /**
    * Возвращает описание типа оплаты для администратора. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Способ предназначен для курьерского мобильного приложения ReadyScript. Оплата осуществляется через устройство чтения карт. Подробности на сайте сервиса <a href="http://2can.ru">2can.ru</a>');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'toucan';
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
    * Возвращает true, если можно обращаться к ResultUrl для данного метода оплаты.
    * Обычно необходимо для способов оплаты, которые применяются только на мобильных приложениях.
    * По умолчанию возвращает то же, что и canOnlinePay.
    * 
    * @return bool
    */
    function isAllowResultUrl()
    {
        return true;
    }    
    
    /**
    * Возвращает ORM объект для генерации формы в административной панели 
    * 
    * @return \RS\Orm\FormObject
    */
    function getFormObject()
    {
         $properties = new \RS\Orm\PropertyIterator([
            //Закомментировано, пока сервис 2can не восстановит возможность 
            //уведомления о транзакции через HTTP запрос
            /*         
            'secret_word' => new Type\Varchar(array(
                'description' => t('Секретное слово(придумайте его), должно быть написано английскими буквами или цифрами, не менее 8 знаков'),
                'checker' => array('Chkempty', 'Не может быть пустым')
            )),
            '__help__' => new Type\Mixed(array(
                'description' => '',
                'visible' => true,  
                'template' => '%shop%/form/payment/toucan/help.tpl'
            )),
            */
         ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }
    
    /**
    * Возвращает секретный идентификатор магазина 
    * 
    */
    function getShopSecret()
    {
        $site_id = \RS\Site\Manager::getSiteId();
        return sha1( \Setup::$SECRET_KEY.$site_id.'2can' );
    }
    
    /**
    * Возвращает объект SimpleXml
    * 
    * @return \SimpleXMLElement
    */
    function getXml()
    {
        $body = file_get_contents('php://input');
        
        //Test Success XML
        //$body = '<Payment Id="18" Amount="1200.00" CreatedAt="2013-09-30T13:34:42.5+04:00" RRN="633415179980" CardType="Visa" TID="" MID="" Card="424242** **** **** 4242" Description="Оплата заказа N XXX" AuthCode="8e2a73" Status="Completed"><Device Id="66" Name="" Model="Galaxy Gio" /></Payment>';

        //Test Fail XML
        //$body = '<Payment Id="19" Amount="1200.00" CreatedAt="2013-09-30T13:36:35.6+04:00" RRN="633415179980" CardType="Visa" TID="" MID="" Card="424242** **** **** 4242" Description="" AuthCode="21eabd" Status="Voided"><Device Id="70" Name="" Model="Galaxy Gio" /></Payment>';

        return new \SimpleXMLElement($body);
    }
    
    /**
    * Возвращает true, если секретные слова совпадают
    * 
    * @param mixed $secret_shop
    * @param mixed $secret_word
    */
    function checkSecret($secret_shop)
    {
        return $secret_shop == $this->getShopSecret();
    }
    
    /**
    * Находим подходящий ID способа оплаты по секретному слову
    * 
    * @param string $word
    * @return integer
    * @throws \RS\Exception
    */
    function findPaymentIdBySecretWord($word)
    {
        $payemnt_api = new \Shop\Model\PaymentApi();
        $payemnt_api->setFilter('class', $this->getShortName());
        foreach($payemnt_api->getList() as $payment) {
            if ($payment['data']['secret_word'] == $word) {
                return $payment['id'];
            }
        }
        
        throw new \RS\Exception(t('Неверное значение secret_word'));
    }
    
    /**
    * Возвращае ID транзакции
    * 
    * @param mixed $request
    */
    function getTransactionIdFromRequest(\RS\Http\Request $request)
    {   
        //Создадим/обновим транзакцию в момент получения запроса на Onresult
        $secret_word = $request->get('secret_word', TYPE_STRING);
        $secret_shop = $request->get('secret_shop', TYPE_STRING);
        
        if (!$this->checkSecret($secret_shop)) {
            throw new \RS\Exception(t('Неверное значение secret_shop'));
        }
        
        $payment = $this->getXml();
        if ($payment->getName() == 'Payment') { //Это операция оплаты

            $payment_id = $this->findPaymentIdBySecretWord($secret_word);
            
            $transaction = \Shop\Model\ToucanApi::getTransaction((string)$payment['RRN']);
            $transaction['status'] = Transaction::STATUS_NEW;
            $transaction['cost'] = (string)$payment['Amount'];
            $transaction['reason'] = (string)$payment['Description'];
            $transaction['payment'] = $payment_id;
            
            $transaction['extra_arr'] = [
                'PaymentId' => (string)$payment['Id'],
                'Card' => (string)$payment['Card']
            ];
            
            $transaction['sign'] = \Shop\Model\TransactionApi::getTransactionSign($transaction);
            $transaction->update();

            return $transaction['id'];
        } else {
            echo "Success. No action need. (Process only: Payment(Status=Completed))";
            exit;
        }
    }

    /**
    * Вызывается при оплате сервером платежной системы. 
    * Возвращает строку - ответ серверу платежной системы.
    * В случае неверной подписи бросает исключение
    * Используется только для Online-платежей
    * 
    * @param Transaction $transaction
    * @param \RS\Http\Request $request
    * @return string 
    */
    function onResult(Transaction $transaction, \RS\Http\Request $request)
    {
        $payment = $this->getXml();
        if ($payment->getName() == 'Payment') {
            $transaction['status'] = $payment['Status'] == 'Completed' ? Transaction::STATUS_SUCCESS : Transaction::STATUS_FAIL;
            $transaction['sign'] = \Shop\Model\TransactionApi::getTransactionSign($transaction);
            $transaction->update();
        }
        
        $result_exception = new ResultException('Success');
        $result_exception->setUpdateTransaction(false);
        $result_exception->setResponse('Success');

        throw $result_exception;
    }    
}