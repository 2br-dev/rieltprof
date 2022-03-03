<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Main\Model\StatisticEvents;
use \ExternalApi\Model\Exception as ApiException;

/**
* Реализует третий шаг оформления заказа. Этап отправления подтверждения заказа
*/
class Confirm extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
        
    public
        /**
        * @var \Shop\Model\Orm\OrderApi
        */
        $order_api,
        /**
        * @var \Shop\Model\Orm\Order
        */
        $order,
        $shop_config;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
    }
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Отправка данных')
        ];
    }

    /**
    * Реализует третий шаг оформления заказа. Этап подтверждения заказа
    * @param integer $iagree согласие с условиями продаж. Нужно только когда включен показ лицензионного соглашения в настроках модуля магазин.
    * @param integer $comments комментарий к заказу.
    * 
    * @example POST /api/methods/checkout.confirm
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Ошибка'],    
    *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если успешно
    */
    protected function process($token = null, $iagree = null, $comments = null)
    {
        $errors = [];
        $response['response']['success'] = false; 
              
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        } 
        
        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;
        if ($cart_data === null || !count($cart_data['items']) || $cart_data['has_error'] || $this->order['expired']) {
            //Если корзина пуста или заказ уже оформлен или имеются ошибки в корзине, то выполняем redirect на главную сайта
            $errors[] = "Корзина заказа пуста, истекла сессия или в ней имеются ошибки. Оформите корзину заново.";
            $response['response']['errors']  = $errors;
            $response['response']['error_status'] = 3;
            return $response;
        }   
        
        $this->order->clearErrors();
        if ($this->shop_config->require_license_agree && !$iagree) {
            $this->order->addError(t('Подтвердите согласие с условиями предоставления услуг'));
        }
        
        $sysdata = ['step' => 'confirm'];
        $work_fields = $this->order->useFields($sysdata + $_POST);
        
        $this->order->setCheckFields($work_fields);
        if (!$this->order->hasError() && $this->order->checkData($sysdata, null, null, $work_fields)) {
            $this->order['is_payed'] = 0;
            $this->order['delivery_new_query'] = 1;
            $this->order['payment_new_query'] = 1;
            $this->order['is_mobile_checkout'] = 1; //Выгружен из мобильного приложения
           
            // Событие для модификации корзины (вызывается повторно непосредственно перед сохранением заказа)
            \RS\Event\Manager::fire('checkout.confirm', [
                'order' => $this->order,
                'cart' => $this->order->getCart()
            ]);
           
            //Создаем заказ в БД
            if ($this->order->insert()) {
               // Фиксация события "Подтверждение заказа" для статистики
               \RS\Event\Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_CONFIRM_ORDER]);

               $this->order['expired'] = true; //заказ уже оформлен. больше нельзя возвращаться к шагам.
               \Shop\Model\Cart::currentCart()->clean(); //Очищаем корзиу     
            }
        }
          
        
        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
            $response['response']['success'] = true;
            //Отправим сведения по заказу
            $response['response']['order']                     = \ExternalApi\Model\Utils::extractOrm($this->order);
            $response['response']['order']['can_online_pay']   = $this->order->canOnlinePay();
            $response['response']['order']['dateof_timestamp'] = strtotime($this->order['dateof']); //Дата цифрой
            $response['response']['order']['dateof']           = $this->order['dateof']; //Дата dd.mm.YYYY HH:ii:ss
            $response['response']['order']['dateof_date']      = date('d.m.Y', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY
            $response['response']['order']['dateof_datetime']  = date('d.m.Y H:i', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY HH:ii
            $response['response']['order']['dateof_iso']       = date('c', strtotime($this->order['dateof'])); //Дата dd.mm.YYYY

            //Дополнительные секции
            if ($this->order['payment']){
                $payment = $this->order->getPayment();
                if ($payment->hasDocs()){ //Если есть документы для оплаты
                    $payment->getPropertyIterator()->append([
                        'docs' => new \RS\Orm\Type\ArrayList([
                            'description' => t('Список документов'),
                            'appVisible' => true
                        ]),
                    ]);
                    $type_object = $payment->getTypeObject();
                    $docs = [];
                    foreach ($type_object->getDocsName() as $key=>$doc){
                       $docs[] = [
                          'title' => $doc['title'],   
                          'link' => $type_object->getDocUrl($key, true),
                       ];
                    }
                    $payment['docs'] = $docs;
                }
                $response['response']['payment']   = \ExternalApi\Model\Utils::extractOrm($payment);       
            }
            if ($this->order['delivery']){
              $response['response']['delivery']  = \ExternalApi\Model\Utils::extractOrm($this->order->getDelivery());
            }
            if ($this->order['use_addr']){
              $response['response']['address']   = \ExternalApi\Model\Utils::extractOrm($this->order->getAddress());
            }
            if ($this->order['warehouse']){
              $response['response']['warehouse'] = \ExternalApi\Model\Utils::extractOrm($this->order->getWarehouse());
            }
            //Если есть файлы привязанные к заказу
            if ($files = $this->order->getFiles()) {
                $this->order->getPropertyIterator()->append([
                    'files' => new \RS\Orm\Type\MixedType([
                        'description' => t('Файлы прикреплённые к заказу'),
                        'appVisible' => true
                    ]),
                ]);
                $order_files = [];
                foreach ($files as $file){
                    $order_files[] = [
                        'title' => $file['name'], 
                        'link'  => $file->getUrl(true)
                    ];
                }
                $this->order['files'] = $order_files;
            }
            $response['response']['user']         = \ExternalApi\Model\Utils::extractOrm($this->order->getUser()); 
            $response['response']['status']       = \ExternalApi\Model\Utils::extractOrm($this->order->getStatus());
           
        }

        return $response;
    }
}