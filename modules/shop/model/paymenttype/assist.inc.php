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
use \Shop\Model\Orm\Transaction;

/**
* Способ оплаты - Robokassa
*/
class Assist extends AbstractType
{
    function __construct()
    {
        $this->setOption([
            'payurl' => 'https://test.paysecure.ru/pay/order.cfm'
        ]);
    }
    
    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Assist');
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Оплата через агрегатор платежей "Assist"');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'assist';
    }
    
    /**
    * Возвращает ORM объект для генерации формы или null
    * 
    * @return \RS\Orm\FormObject | null
    */
    function getFormObject()
    {
        $properties = new \RS\Orm\PropertyIterator([
            'payurl' => new Type\Varchar([
                'description' => t('URL оплаты'),
            ]),
            'merchant_id' => new Type\Varchar([
                'description' => t('Merchant ID')
            ]),
            'secret_word' => new Type\Varchar([
                'description' => t('Секретное слово')
            ]),
            '__help__' => new Type\MixedType([
                'description' => t(''),
                'visible' => true,  
                'template' => '%shop%/form/payment/assist/help.tpl'
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }
    
    /**
    * Возвращает true, если данный тип поддерживает проведение платежа через интернет
    * 
    * @return bool
    */
    function canOnlinePay()
    {
        return true;
    }
    
    /**
    * Возвращает URL для перехода на сайт сервиса оплаты
    * 
    * @param Transaction $transaction
    * @return string
    */
    function getPayUrl(Transaction $transaction)
    {
        //Получим текущего пользователя плательщика
        /**
        * @var \Users\Model\Orm\User
        */
        $user = $transaction->getUser();
        
        $params = [];
        $params['Merchant_ID']  = $this->getOption('merchant_id');
        $params['OrderNumber']  = $transaction->id;
        $params['OrderAmount']  = round($transaction->cost, 2);
        $params['OrderComment'] = $transaction->reason;
        //Доп. параметры
        if (!empty($user['name'])){
            $params['FirstName'] = $user['name'];
        }
        if (!empty($user['surname'])){
            $params['LastName']  = $user['surname'];
        }
        
        if (!empty($user['e_mail'])){
            $params['Email']  = $user['e_mail'];
        }
        
        $url = $this->getOption('payurl');
        $url = $url."?".http_build_query($params);
        
        return $url;
    }
    
    /**
    * Возвращает ID заказа исходя из REQUEST-параметров соотвествующего типа оплаты
    * Используется только для Online-платежей
    * 
    * @return mixed
    */
    function getTransactionIdFromRequest(\RS\Http\Request $request)
    {
        return $request->request('ordernumber', TYPE_INTEGER, false);
    }

    private function checkSign(\RS\Http\Request $request)
    {
        // Для типа подписи MD5 вычисляется по формуле: uppercase(md5(uppercase(md5(SALT) + md5( Х )))), 
        // где SALT – секретное слово; 
        // Х – результат строковой склейки параметров:
        // merchant_id, ordernumber, amount, currency, orderstate ( без разделителей ); + - строковая склейка  
        
        $checkvalue = $request->request('checkvalue', TYPE_STRING);
        $secret_word = $this->getOption('secret_word');
        
        $string = "";
        $string .= $request->request('merchant_id', TYPE_STRING);
        $string .= $request->request('ordernumber', TYPE_STRING);
        $string .= $request->request('amount', TYPE_STRING);
        $string .= $request->request('currency', TYPE_STRING);
        $string .= $request->request('orderstate', TYPE_STRING);
        
        return $checkvalue == strtoupper(md5(strtoupper(md5($secret_word).md5($string))));
    }
    
    function onResult(\Shop\Model\Orm\Transaction $transaction, \RS\Http\Request $request)
    {
        // Так как ответ будет в любом случае в XML, мы отправляем соотвествующий заголовк
        header('Content-type: text/xml; charset=utf-8');
        
        // Заготовка ответа серверу, который отправляется в случае ошибки
        $fail_response = '<?xml version="1.0" encoding="UTF-8"?>'.
                            '<pushpaymentresult firstcode="1" secondcode="0">'.
                         '</pushpaymentresult>';
        
        // Проверка подписи запроса
        if(!$this->checkSign($request)){
            // Ответ серверу, если подпись не совпала
            $exception = new ResultException(t('Неверная подпись запроса'));
            $exception->setResponse($fail_response);   // Строка направится как ответ серверу
            throw $exception;
            
        }
        
        // Проверка, соответсвует ли сумма платежа сумме, сохраненной в транзакции
        if($request->request('amount', TYPE_STRING) != $transaction->cost){
            $exception = new ResultException(t('Неверная сумма платежа %0', [$request->request('amount', TYPE_STRING)]));
            $exception->setResponse($fail_response);
            throw $exception;
        }
        
        // Ответ серверу, если подпись верна и сумма соответсвует
        return  '<?xml version="1.0" encoding="UTF-8"?>'.
                '<pushpaymentresult firstcode="0" secondcode="0">'.
                    '<order>'.
                        '<billnumber>'.$transaction->id.'</billnumber>'.
                        '<packetdate>'.date("d.m.Y H:i:s").'</packetdate>'.
                    '</order>'.
                '</pushpaymentresult>';
    }
    
    /**
    * Вызывается при переходе на страницу успеха, после совершения платежа 
    * 
    * @return void 
    */
    function onSuccess(\Shop\Model\Orm\Transaction $transaction, \RS\Http\Request $request)
    {
    }
    
}