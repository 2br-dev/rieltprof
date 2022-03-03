<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\PaymentType;

use RS\Config\Loader;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Orm\Exception as OrmException;
use RS\Orm\FormObject;
use RS\Orm\Type;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Tax;
use Shop\Model\Orm\Transaction;
use Shop\Model\TaxApi;

/**
 * Способ оплаты - Robokassa
 */
class Robokassa extends AbstractType
{
    const
        TAX_NDS_NONE = 'none',
        TAX_NDS_0    = 'vat0',
        TAX_NDS_10   = 'vat10',
        TAX_NDS_18   = 'vat18',
        TAX_NDS_20   = 'vat20',
        TAX_NDS_110  = 'vat110',
        TAX_NDS_118  = 'vat118',
        TAX_NDS_120  = 'vat120';

    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Робокасса');
    }
    
    /**
    * Возвращает описание типа оплаты. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Оплата через агрегатор платежей "Робокасса"');
    }
    
    /**
    * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'robokassa';
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
            'login' => new Type\Varchar([
                'description' => t('Логин')
            ]),
            'password_1' => new Type\Varchar([
                'description' => t('Пароль #1')
            ]),
            'password_2' => new Type\Varchar([
                'description' => t('Пароль #2')
            ]),
            'incCurrLabel' => new Type\Varchar([
                'description' => t('Рекомендуемый способ оплаты'),
                'template' => '%shop%/form/payment/robokassa/inccurlabel.tpl'
            ]),
            'tax_system' => new Type\Integer([
                'description' => t('Система налогообложения магазина'),
                'hint' => t('Используется для передачи данных для чека по 54-ФЗ.<br>
                            Необходим только если у вас несколько систем налогообложения.'),
                'length' => 1,
                'listFromArray' => [[
                    '0' => t('- Не указана -'),
                    'osn' => t('общая СН'),
                    'usn_income' => t('упрощенная СН (доходы)'),
                    'usn_income_outcome' => t('упрощенная СН (доходы минус расходы)'),
                    'envd' => t('единый налог на вмененный доход'),
                    'esn' => t('единый сельскохозяйственный налог'),
                    'patent' => t('патентная СН')
                ]],
            ]),
            '__help__' => new Type\MixedType([
                'description' => t(''),
                'visible' => true,  
                'template' => '%shop%/form/payment/robokassa/help.tpl'
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }
    
    /**
    * Возвращает список доступных для текущего пользователя типов оплат
    * 
    * @return array
    */
    public static function getIncCurrLabels($params)
    {
        if (!isset($params['login'])) {
            return [
                'error' => t('Не указан логин для загрузки списка способов оплат')
            ];
        } else {
            $url = "https://auth.robokassa.ru/Merchant/WebService/Service.asmx/GetCurrencies";
            $opts = [
                  'http'=> [
                    'method'=>"GET",
                    'timeout' => 5,
                  ],
                  "ssl"=> [
                    "allow_self_signed" => true,
                    "verify_peer" => false,
                  ],
            ];

            $context = stream_context_create($opts);
            $xml_text = @file_get_contents($url.'?'.http_build_query([
                'Language' => 'ru',
                'MerchantLogin' => $params['login']
                ]), false, $context);
            
            $xml = new \SimpleXMLElement($xml_text);
            if ((string)$xml->Result->Code != 0) {
                return [
                    'error' => (string)$xml->Result->Description
                ];
            } else {
                $list = [];
                foreach($xml->Groups->Group as $group) {
                    foreach($group->Items as $item) {
                        foreach($item->Currency as $currency) {
                            $list[(string)$currency['Label']] = (string)$currency['Name'];
                        }
                    }
                }
                return [
                    'list' => $list
                ];
            }
        }
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
        $shp_item   = 0;
        $shp_trid   = $transaction->id;                     // id транзакции
        $inv_id     = $transaction->order_id; //номер заказа
        $out_summ   = round($transaction->cost, 2);
        $inv_desc   = $transaction->reason;
        $mrh_login  = $this->getOption('login');
        $receipt    = $this->getParamsForFZ54Check($transaction);
        $receipt_str = ($receipt) ? "$receipt:" : '';
        $mrh_pass1  = $this->getOption('password_1');
        $crc        = md5("$mrh_login:$out_summ:$inv_id:$receipt_str$mrh_pass1:Shp_item=$shp_item:Shp_trid=$shp_trid");
        $incCurrLabel = $this->getOption('incCurrLabel');
        
        $culture  = "ru";       // язык
        $encoding = "utf-8";    // кодировка
        $in_curr  = "";         // предлагаемая валюта платежа

        $url = "https://auth.robokassa.ru/Merchant/Index.aspx";
        
        //if($this->getOption('testmode')){
            //$url = "http://test.robokassa.ru/Index.aspx";
        //}
        
        $params = [];
        $params['Shp_trid']         = $shp_trid;
        $params['MrchLogin']        = $mrh_login;
        $params['OutSum']           = $out_summ;
        $params['InvId']            = $inv_id;
        $params['IncCurrLabel']     = $in_curr;
        $params['Desc']             = $inv_desc;
        $params['SignatureValue']   = $crc;
        if($this->getOption('testmode')){
            $params['IsTest'] = 1;
        }
        if ($incCurrLabel) {
            $params['IncCurrLabel'] = $incCurrLabel;
        }
        
        $params['Shp_item']         = $shp_item;
        $params['Culture']          = $culture;
        $params['Encoding']         = $encoding;
        if ($receipt) {
            $params['Receipt'] = $receipt;
        }
        
        $this->addPostParams($params); //Добавим пост параметры
        
        return $url;
    }
    
    /**
    * Возвращает дополнительные параметры для печати чека по ФЗ-54
    * 
    * @param \Shop\Model\Orm\Transaction $transaction
    * @return array|false
    */
    protected function getParamsForFZ54Check($transaction)
    {
        $rub_currency = \Catalog\Model\CurrencyApi::getByUid('RUB');
        // Робокасса принимает данные только в рублях
        if ($rub_currency === false) {
            return false;
        }
        
        $receipt = [];
        if ($this->getOption('tax_system')) {
            $receipt['sno'] = $this->getOption('tax_system');
        }

        if ($transaction['order_id']) {
            //Оплата заказа
            $order = $transaction->getOrder();
            $cart = $order->getCart();
            if ($cart) {
                $address = $order->getAddress();
                $tax_api = new \Shop\Model\TaxApi();
                $products = $cart->getProductItems();
                foreach ($products as $product) {
                    $taxes = $tax_api->getProductTaxes($product['product'], $this->transaction->getUser(), $address);
                    $item = [
                        'name' => $this->itemName($product),
                        'quantity' => $product['cartitem']['amount'],
                        'sum' => round(($product['cartitem']['price'] - $product['cartitem']['discount']) * $rub_currency['ratio'], 2),
                        'tax' => $this->getNdsCode($taxes, $address),
                        'payment_method' => $product['product']['payment_method'] ? $product['product']['payment_method'] : $order->getDefaultPaymentMethod(),
                        'payment_object' => $product['product']['payment_subject'],
                    ];
                    $receipt['items'][] = $item;
                }
                $delivery = $cart->getCartItemsByType(\Shop\Model\Cart::TYPE_DELIVERY);
                foreach ($delivery as $delivery_item) {
                    $taxes = $tax_api->getDeliveryTaxes($order->getDelivery(), $this->transaction->getUser(), $address);
                    $item = [
                        'name' => mb_substr($delivery_item['title'], 0 , 50),
                        'quantity' => 1,
                        'sum' => round(($delivery_item['price'] - $delivery_item['discount']) * $rub_currency['ratio'], 2),
                        'tax' => $this->getNdsCode($taxes, $address),
                        'payment_method' => $delivery['payment_method'] ? $delivery['payment_method'] : $order->getDefaultPaymentMethod(),
                        'payment_object' => 'service',
                    ];
                    $receipt['items'][] = $item;
                }
            }
        } else {
            $shop_config = Loader::byModule($this);
            //Пополнение лицевого счета
            $item = [
                'name' => $transaction->reason,
                'quantity' => 1,
                'sum' => round($transaction->cost * $rub_currency['ratio'], 2),
                'tax' => self::handbookNds()[$shop_config['nds_personal_account']] ?? self::handbookNds()[TaxApi::TAX_NDS_NONE],
                'payment_method' => $shop_config['personal_account_payment_method'],
                'payment_object' => $shop_config['personal_account_payment_subject'],
            ];
            $receipt['items'][] = $item;
        }
        $return = urlencode(json_encode($receipt));
        
        return $return;
    }

    function itemName($product)
    {
        if ($product['product']['barcode']){
            $result = $product['product']['barcode'];
        }
        $result = $result.' '.$product['product']['title'];
        if (iconv_strlen($result)>64){
            $result = iconv_substr($result, 0 , 61 , 'UTF-8' );
            $result = $result.'...';
        }
    return $result;
    }





    /**
    * Возвращает ID заказа исходя из REQUEST-параметров соотвествующего типа оплаты
    * Используется только для Online-платежей
    * 
    * @return mixed
    */
    function getTransactionIdFromRequest(\RS\Http\Request $request)
    {
        return $request->request('Shp_trid', TYPE_INTEGER, false);
    }

    private function checkSign($password, \RS\Http\Request $request)
    {
        // Чтение параметров
        $shp_trid   = $request->request("Shp_trid", TYPE_INTEGER);
        $out_summ   = $request->request("OutSum", TYPE_STRING);
        $inv_id     = $request->request("InvId", TYPE_INTEGER);
        $shp_item   = $request->request("Shp_item", TYPE_STRING);
        $sign       = $request->request("SignatureValue", TYPE_STRING);
        $sign       = strtoupper($sign);
        
        // Вычисление подписи
        $my_sign    = strtoupper(md5("$out_summ:$inv_id:$password:Shp_item=$shp_item:Shp_trid=$shp_trid"));

        // Проверка корректности подписи
        return $my_sign == $sign;
    }
    
    function onResult(\Shop\Model\Orm\Transaction $transaction, \RS\Http\Request $request)
    {
        // Получение пароля #2 из настроек профиля
        $password_2 = $this->getOption('password_2');

        // Проверка подписи запроса
        if(!$this->checkSign($password_2, $request)){
            $exception = new ResultException(t('Неверная подпись запроса'));
            $exception->setResponse('bad sign'); // Строка направится как ответ серверу
            throw $exception;
        }
        
        // Проверка, соответсвует ли сумма платежа сумме, сохраненной в транзакции
        if($request->request('OutSum', TYPE_STRING) != $transaction->cost){
            $exception = new ResultException(t('Неверная сумма платежа %0', [$request->request('OutSum', TYPE_STRING)]));
            $exception->setResponse('bad summ');
            throw $exception;
        }
        
        return 'OK' . $request->request("InvId", TYPE_INTEGER);
    }

    /**
     * Вызывается при переходе на страницу успеха, после совершения платежа
     *
     * @param Transaction $transaction
     * @param HttpRequest $request
     * @return void
     * @throws RSException
     */
    function onSuccess(Transaction $transaction, HttpRequest $request)
    {
        // Получение пароля #1 из настроек профиля
        $password_1 = $this->getOption('password_1');
        // Проверка подписи
        if (!$this->checkSign($password_1, $request)) {
            throw new RSException(t('Неверная подпись запроса'));
        }
    }

    /**
     * Возвращает true, если необходимо использовать
     * POST запрос для открытия страницы платежного сервиса
     *
     * @return bool
     */
    function isPostQuery()
    {
        return true;
    }

    /**
     * Справочник кодов НДС
     * Ключи справочника должны соответствовать списку кодов НДС в TaxApi
     *
     * @return string[]
     */
    protected static function handbookNds()
    {
        static $nds = [
            TaxApi::TAX_NDS_NONE => 'none',
            TaxApi::TAX_NDS_0 => 'vat0',
            TaxApi::TAX_NDS_10 => 'vat10',
            TaxApi::TAX_NDS_20 => 'vat20',
            TaxApi::TAX_NDS_110 => 'vat110',
            TaxApi::TAX_NDS_120 => 'vat120',
        ];
        return $nds;
    }


}
