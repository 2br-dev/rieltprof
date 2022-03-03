<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;
use \RS\Orm\Type,
    \RS\Helper\CustomView;

/**
* Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю магазин
*/
class ApiUtils
{
    protected
        $cart; //Объект корзины
        
    /**
    * Возвращает секцию с дополнительными полями купить в один клик из конфига для внешнего API
    * 
    */
    public static function getAdditionalBuyOneClickFieldsSection()
    {
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
        $click_fields_manager->setErrorPrefix('clickfield_');
        $click_fields_manager->setArrayWrapper('clickfields');
        
        //Пройдёмся по полям
        $fields = [];
        foreach ($click_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;    
            }
            $fields[] = $field;
        }
        
        return $fields;
    } 
        
    /**
    * Возвращает секцию с дополнительными полями заказа из конфига для внешнего API
    * 
    */
    public static function getAdditionalOrderFieldsSection()
    {
        $order = new \Shop\Model\Orm\Order();
        $order_fields_manager = $order->getFieldsManager();
        
        //Пройдёмся по полям
        $fields = [];
        foreach ($order_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;    
            }
            $fields[] = $field;
        }
        
        return $fields;
    }
    
    /**
    * Подготавливает секции с комплектация и многомерными комплектациями
    * 
    * @param array $item - массив данных одной записи в корзине
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param \Shop\Model\Orm\CartItem $cartitem - объект объекта корзины
    * @return array
    */
    private function prepareOffersAndMultiOffersSection($item, $product, $cartitem)
    {
        $item['multioffers'] = null;
        $item['multioffers_string'] = ""; //В виде строки
        if ($product->isMultiOffersUse()){ //Если есть многомерные комплектации
            $multioffers_values = @unserialize($cartitem['multioffers']);
            
            foreach ($product['multioffers']['levels'] as $level){
                $multioffer['title']   = !empty($level['title']) ? $level['title'] : $level['prop_title'];
                $multioffer['prop_id'] = $level['prop_id'];
                foreach ($level['values'] as $value){
                    if ($value['val_str'] == $multioffers_values[$level['prop_id']]['value']){
                       $multioffer['value'] = $value['val_str']; 
                    }
                }
                $item['multioffers'][] = $multioffer;
            }
            if ($product->isOffersUse()){ //Если комплектации тоже присутствуют
                foreach ($product['offers']['items'] as $key=>$offer){
                    if ($cartitem['offer'] == $key){
                        $item['model'] = $offer['title'];
                    }
                }
            }
            // Многомерные комплектации строкой
            if (!empty($item['multioffers'])){
                $multioffers_string = [];
                foreach ($item['multioffers'] as $multioffer){
                   $m_value = isset($multioffer['value']) ? $multioffer['value'] : "";
                   $m_title = isset($multioffer['title']) ? $multioffer['title'] : "";
                   $multioffers_string[] = $m_title.": ".$m_value;
                }
                
                $item['multioffers_string'] = implode(", ", $multioffers_string);
            }
        }elseif ($product->isOffersUse()){ //Если есть только комплектации
            foreach ($product['offers']['items'] as $key=>$offer){
                if ($cartitem['offer'] == $key){
                    $item['model'] = $offer['title'];
                }
            }
        }
        return $item;
    }
    
    
    /**
    * Подготавливает секцию с сопутствующими товарами
    * 
    * @param array $item - массив данных одной записи в корзине
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param \Shop\Model\Orm\CartItem $cartitem - объект объекта корзины
    */
    private function prepareSubProducts($item, $product, $cartitem)
    {
        if (!empty($item['sub_products'])){
            $shop_config    = \RS\Config\Loader::byModule('shop');
            $concomitant = $product->getConcomitant();    
            
            $sub_product_data_arr = [];
            //Переберём сопутствующие товары и добавим данные по ним
            foreach ($item['sub_products'] as $id=>&$sub_product_data){
                /**
                * @var \Catalog\Model\Orm\Product $sub_product
                */
                $sub_product = $concomitant[$id]; //Сопутствующий товар
                
                $sub_product_data['title']      = $sub_product['title'];
                $sub_product_data['product_id'] = $item['id'];
                $sub_product_data['image']      = \Catalog\Model\ApiUtils::prepareImagesSection($sub_product->getMainImage());
                $sub_product_data['id']         = $sub_product['id'];
                $sub_product_data['unit']       = $sub_product->getUnit()->stitle;
                
                
                $sub_product_data['allow_concomitant_count_edit'] = false;
                //Если позволено редактировать количество сопутствующих
                if ($shop_config['allow_concomitant_count_edit']){
                   $sub_product_data['allow_concomitant_count_edit'] = true; 
                }
                
                $sub_product_data_arr[] = $sub_product_data; 
            }
            $item['sub_products'] = $sub_product_data_arr;
        }
        return $item['sub_products'];
    }
    
    /**
    * Подготовка сведений о купонах, добавленных в корзине
    * 
    * @param array $cartdata - массив данных по составу корзины
    */
    private function prepareCouponsInfo($cartdata){
        
        $coupons = $this->cart->getCouponItems();
        $cartdata['coupons'] = [];
        if (!empty($coupons)){
            foreach ($coupons as $id=>$item){
                $coupon['id']   = $id;
                $coupon['code'] = $item['coupon']['code'];
                $cartdata['coupons'][] = $coupon;
            }
        }    
        
        return $cartdata;
    }
    
    /**
    * Заполняет подробные данные по товарам в сведения корзины. 
    * Если объект заказа передан, то будут премешаны элеметы заказа  
    * 
    * @param \Shop\Model\Orm\Order $order - объект заказа
    * @return array
    */
    function fillProductItemsData($order = null)
    {
        if (!$order){
            $this->cart = \Shop\Model\Cart::currentCart();    
        }else{
            $this->cart = $order->getCart();
        }
                              
        $cartdata   = $this->cart->getCartData();
           
        if (!empty($cartdata['items'])){
            $catalog_config = \RS\Config\Loader::byModule('catalog');
            
            $items = [];
            $product_items = $this->cart->getProductItems();
            $m = 0;
            //Сведения по товарам
            foreach($cartdata['items'] as $uniq=>$item){
                /**
                * @var \Catalog\Model\Orm\Product $product
                * @var \Shop\Model\Orm\CartItem $cartitem
                */
                $product   = $product_items[$uniq]['product'];
                $cartitem  = $product_items[$uniq]['cartitem'];
                
                //Дополним сведениями по самому товару
                $item['title']        = $product['title'];
                $item['image']        = \Catalog\Model\ApiUtils::prepareImagesSection($product->getMainImage());
                $item['entity_id']    = $cartitem['entity_id'];
                $item['amount']       = $cartitem['amount'];
                $item['amount_error'] = isset($item['amount_error']) ? $item['amount_error'] : "";
                $item['offer']        = $cartitem['offer'];
                $item['model']        = null;
                
                $item = $this->prepareOffersAndMultiOffersSection($item, $product, $cartitem);
                
                if ($catalog_config['use_offer_unit']){ //Если нужно использовать единицы измерения в комплектациях
                    $product->fillOffers();
                    $item['unit'] = $product['offers']['items'][$cartitem['offer']]->getUnit()->stitle;
                }else{
                    $item['unit'] = $product->getUnit()->stitle;
                }
                
                $item['sub_products'] = $this->prepareSubProducts($item, $product, $cartitem);

                $items[$m] = $item;
                $m++;
            }
            $cartdata['items'] = $items;
            
            //Сведения по купонам
              
            $cartdata = $this->prepareCouponsInfo($cartdata);
            
            $taxes = [];
            if (!empty($cartdata['taxes'])){
                
                foreach ($cartdata['taxes'] as $k=>$taxitem){
                    $taxes[] = [
                        'title' => $taxitem['title'],
                        'cost' => $taxitem['cost']
                    ];
                }
            }
            
              
            $cartdata['taxes'] = $taxes;
            $cartdata['total_discount_unformatted'] =  floatval($cartdata['total_discount']);
        }
                                   
        if ($order){  //Если передан заказ       
            $cartdata['user']      = \ExternalApi\Model\Utils::extractOrm($order->getUser());
            $cartdata['only_pickup_points'] = $order['only_pickup_points'];
            $cartdata['use_addr']  = $order['use_addr'];
            if ($order['delivery']){ //Обработаем доставку
                $order_delivery         = $cartdata['delivery'];
                $order_delivery         = \ExternalApi\Model\Utils::extractOrm($order_delivery['object']);
                $order_delivery['cost'] = $cartdata['delivery']['cost'];
                $cartdata['delivery']   = $order_delivery;
                
            }
            if (isset($cartdata['payment_commission']) && $cartdata['payment_commission']){  //Обработаем коммисию за заказ
                $payment_commission             = $cartdata['payment_commission'];
                $payment_commission             = \ExternalApi\Model\Utils::extractOrm($payment_commission['object']);
                $payment_commission['cost']     = $cartdata['payment_commission']['cost'];;
                $cartdata['payment_commission'] = $payment_commission;
            }
            $cartdata['payment']   = \ExternalApi\Model\Utils::extractOrm($order->getPayment());
            $cartdata['warehouse'] = \ExternalApi\Model\Utils::extractOrm($order->getWarehouse());
            $cartdata['address']   = \ExternalApi\Model\Utils::extractOrm($order->getAddress());
        }
        
        return $cartdata;
    }
    
    
    /**
    * Возвращает список доставок по текущему оформляемому заказу из сессии
    * 
    * @param string $token - токен приложения
    * @param \Shop\Model\Orm\Order $order - заказ для которого нужно вернуть доставки
    * @param string $sortn - сортировка элементов
    */
    public static function getOrderDeliveryListSection($token, $order, $sortn)
    {
        $errors        = [];
        $delivery_list = [];
        $warehouses    = [];
        $shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
              
        if (!$shop_config['hide_delivery']){
            
            $user    = ($token) ? $token->getUser() : \RS\Application\Auth::getCurrentUser();
            $my_type = $user['is_company'] ? 'company' : 'user'; 
            
            //Расширим объект, для подачи нужных полей
            $delivery = new \Shop\Model\Orm\Delivery();
            $delivery->getPropertyIterator()->append([
                'extrachange_discount_type' => new \RS\Orm\Type\Integer([
                    'visible' => true
                ]),
                'extra_text' => new \RS\Orm\Type\Varchar([
                    'visible' => true
                ]),
                'cost' => new \RS\Orm\Type\Varchar([
                    'visible' => true
                ]),
                'additional_html' => new \RS\Orm\Type\Varchar([
                    'visible' => true
                ]),
                'mobilesiteapp' => new \RS\Orm\Type\Integer([
                    'visible' => true,
                    'appVisible' => true
                ]),
                'error' => new \RS\Orm\Type\Varchar([
                    'visible' => true
                ]),
            ]);

            $delivery_api = new \Shop\Model\DeliveryApi();
            $delivery_list = $delivery_api->getCheckoutDeliveryList($user, $order);
            
            if (!empty($delivery_list)){
                foreach ($delivery_list as &$delivery){
                    /**
                    * @var \Shop\Model\Orm\Delivery $delivery
                    */
                    $delivery['error']           = $delivery->getTypeObject()->somethingWrong($order);     
                    $delivery['extra_text']      = $order->getDeliveryExtraText($delivery);     
                    $delivery['cost']            = $order->getDeliveryCostText($delivery);     
                    $delivery['additional_html'] = $delivery->getAddittionalHtml($order);
                    $delivery['mobilesiteapp']   = 0; //Флаг, что предназначено для мобильного приложения в виде сайта
                    $delivery['mobilesiteapp_additional_html'] = "";
                    if (in_array('Shop\Model\DeliveryType\InterfaceIonicMobile', class_implements($delivery->getTypeObject()))){ //Добавим HTML для мобильной версии
                       $delivery['mobilesiteapp'] = 1;
                       $delivery['mobilesiteapp_additional_html'] = $delivery->getTypeObject()->getIonicMobileAdditionalHTML($order, $delivery);     
                    }
                }
                $delivery_list = \ExternalApi\Model\Utils::extractOrmList($delivery_list);
            }                                          
            
            $warehouses = \Catalog\Model\WareHouseApi::getPickupWarehousesPoints();
            if (!empty($warehouses)){
                $warehouses = \ExternalApi\Model\Utils::extractOrmList($warehouses);
            }    
        }      
        
        $response['errors']     = $order->getErrors();
        $response['list']       = $delivery_list; 
        $response['warehouses'] = $warehouses; 
        return $response;
    }
    
    /**
    * Возвращает список оплат по текущему оформляемому заказу из сессии
    * 
    * @param string token - токен приложения
    * @param \Shop\Model\OrmOrder order - заказ для которого нужно вернуть доставки
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    public static function getOrderPaymentListSection($token, $order, $sortn)
    {
        $errors   = [];
        $pay_list = [];
        
        $shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
              
        if (!$shop_config['hide_payment']){
            $user    = ($token) ? $token->getUser() : \RS\Application\Auth::getCurrentUser();

            $pay_api = new \Shop\Model\PaymentApi();
            $pay_api->setOrder($sortn);
            $pay_list = $pay_api->getCheckoutPaymentList($user, $order);
            
            $delivery_id = $order['delivery'];   
            foreach ($pay_list as $k=>&$pay_item) {  //Переберём оплаты, чтобы ограничить под выбранную доставку 
               if (is_array($pay_item['delivery']) && !empty($pay_item['delivery']) && !in_array(0, $pay_item['delivery'])) { //Если есть прявязанные доставки
                  if (!in_array($delivery_id, $pay_item['delivery'])) {
                      unset($pay_list[$k]); 
                  }   
               }                 
            } 
            
            $pay_list = \ExternalApi\Model\Utils::extractOrmList($pay_list);                                
            
            //Найдём оплату по умолчанию, если оплата не была задана раннее
            if (!$order['payment']){
                $pay_api->setFilter('default_payment', 1);
                $default_payment = $pay_api->getFirst($order);
                if ($default_payment){
                    foreach ($pay_list as $k=>$pay_item) {      
                        $pay_list[$k]['default'] = 0;  
                        if ($pay_item['id'] == $default_payment['id']){
                            $pay_list[$k]['default'] = 1;    
                        }
                    }
                } 
            }
            
        }
        
        $response['errors'] = $errors;
        $response['list']   = $pay_list;
        return $response;
    }
    
      
}