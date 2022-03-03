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
use \RS\Exception;

/**
* Способ оплаты - Robokassa
*/
class PayPal extends AbstractType
{
    protected
        $response_status = '',  //Статус ответа
        $response        = '';  //Сам ответ
    
    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('PayPal');
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Оплата платежей через "PayPal"');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'paypal';
    }
    
    /**
    * Возвращает ORM объект для генерации формы или null
    * 
    * @return \RS\Orm\FormObject | null
    */
    function getFormObject()
    {
        $properties = new \RS\Orm\PropertyIterator([
            'testmode' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Тестовый режим'),
                'checkboxview' => [1,0],
            ]),
            'bussiness_email' => new Type\Varchar([
                'description' => t('E-mail продавца на PayPal')
            ]),
            'culture' => new Type\Varchar([
                'description'   => t('Язык интерфейса PayPal'),
                'listFromArray' => [[
                     'RU' => t('Русский'),
                     'EN' => t('Анлийский'),
                ]]
            ]),
           
            '__help__' => new Type\MixedType([
                'description' => t(''),
                'visible' => true,  
                'template' => '%shop%/form/payment/paypal/help.tpl'
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
        
        $inv_id     = $transaction->id;
        $out_summ   = round($transaction->cost, 2);
        $inv_desc   = $transaction->reason;
        
       
        
        $encoding   = "utf-8";    // кодировка
        //Получим доп информацию
        $order      = new \Shop\Model\Orm\Order($transaction->order_id);
        $order_id   = $order->id;
        
        // валюта платежа всегда базовая, так как сумма всегда в базовой валюте
        $in_curr = \Catalog\Model\CurrencyApi::getBaseCurrency()->title;
        
        $url = "https://www.paypal.com/cgi-bin/webscr";
        
        if($this->getOption('testmode')){
            $url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        
        $current_site = \RS\Site\Manager::getSite()->title;
        
        $params = [];
        $params['cmd']              = '_xclick';
        $params['business']         = $this->getOption('bussiness_email');  //E-mail продавца
        if ($order_id) {
            $params['item_name'] = t("Заказ №%0. %1", [$order_id, $current_site]); //Id заказа
        } else {
            $params['item_name'] = t("Пополнение баланса лицевого счета. %0", [$current_site]); //Id заказа
        }
        $params['lc']               = $this->getOption('culture');          //Интерфейс языковой для PayPal
        $params['currency_code']    = $in_curr;                             //Валюта
        $params['custom']           = $inv_id;                              //Заказ
        $params['charset']          = 'utf-8';                              //Кодировка
        $params['amount']           = $out_summ;                            //Сумма заказа
        
        
        //Урл обработки
         
        $router  = \RS\Router\Manager::obj();
        
        $result  = $router->getUrl('shop-front-onlinepay', [
                'Act'         => 'result',
                'PaymentType' => $this->getShortName(),
        ],true);
          
        $fail    = $this->makeRightAbsoluteUrl($router->getUrl('shop-front-onlinepay', [
                'Act'         => 'fail',
                'custom'      => $inv_id,
                'PaymentType' => $this->getShortName(),
        ]));

        $success = $this->makeRightAbsoluteUrl($router->getUrl('shop-front-onlinepay', [
                'Act'         => 'success',
                'custom'      => $inv_id,
                'PaymentType' => $this->getShortName(),
        ]));
        
        $params['notify_url']       = $result;                          //Url обработки
        $params['cancel_return']    = $fail;                            //Url отмены платежа
        $params['return']           = $success;                         //Успешная оплата
        
         
        
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
        return $request->request('custom', TYPE_INTEGER, false);
    }
    
    /**
     *  Дедает curl запрос на сервер для получения верификации
     *
     *  @param  string  The post data as a URL encoded string
     */
    protected function curlRequest($url_to_server,$encoded_data)
    {
        $uri = 'https://'.$url_to_server.'/cgi-bin/webscr?'.$encoded_data;
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  //таймаут
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        $this->response        = curl_exec($ch);
        $this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        
        if ($this->response === false || $this->response_status == '0') {
            $errno  = curl_errno($ch);
            $errstr = curl_error($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
    }
    
    /**
    * Валидация оплаты
    * 
    * @param \Shop\Model\Orm\Transaction $transaction  - транзакция
    * @param \RS\Http\Request $request                 - входящий запрос
    */
    private function checkSign(\Shop\Model\Orm\Transaction $transaction, \RS\Http\Request $request)
    {
        // Для чтения статуса добавляем команду _notify-validate 
        $encoded_data = 'cmd=_notify-validate';
        foreach ($request->getSource(POST) as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
            $encoded_data .= "&$key=$value";
            
        } 
        
        //Делаем запрос подтвержения, что всё прошло удачно
        $url = "ipnpb.paypal.com";
        
        if($this->getOption('testmode')){
            $url = "www.sandbox.paypal.com";
        }
        
       
        $this->curlRequest($url,$encoded_data);   //curl
      
        
            
        if (strpos($this->response_status, '200') === false) {
            throw new Exception("Invalid response status: ".$this->response_status);
        }
        
        if (strpos($this->response, "VERIFIED") !== false) { //Всё удачно. Подтверждён
            return true;
        } elseif (strpos($this->response, "INVALID") !== false) { //Платёж не прошёл
            return false;
        } else {
            throw new Exception("Unexpected response from PayPal.");
        }
         
        
        return false;
    }
    
    function onResult(\Shop\Model\Orm\Transaction $transaction, \RS\Http\Request $request)
    {

        // Проверка подписи запроса его валидация
        if(!$this->checkSign($transaction, $request)){
            $exception = new ResultException(t('Запрос на оплату не прошёл валидацию'));
            $exception->setResponse('bad sign'); // Строка направится как ответ серверу
            throw $exception;
        }
        
        // Проверка, соответсвует ли сумма платежа сумме, сохраненной в транзакции
        if($request->request('mc_gross', TYPE_STRING) != $transaction->cost){
            $exception = new ResultException(t('Неверная сумма платежа %0', [$request->request('mc_gross', TYPE_STRING)]));
            $exception->setResponse('bad summ');
            throw $exception;
        }
        
        return 'OK'.$transaction->id;
    }
}
