<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Product;

/**
* Отправляет заявку на заказ товара
*/
class Reserve extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    protected
        $token_require = false;
    
    const
        RIGHT_LOAD = 1;
    
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
    * Отправляет заявку на заказ товара. Должен быть указан, либо телефон, либо E-mail. Если указан токен пользователя, то E-mail и телефон указывать не нужно.
    * 
    * @param string $token Авторизационный token
    * @param string $phone телефон пользователя. Например +79XX234XX00
    * @param string $email E-mail пользователя
    * @param integer $product_id id товара
    * @param integer $offer_id id комплектации
    * @param array $multioffers телефон пользователя. Например +79XX234XX00
    * @param integer $is_notify уведомлять ли пользователя о поступлении товара 1 - да, 0 - нет.
    * 
    * @example POST /api/methods/product.reserve?phone=+79628678430&email=admin@admin.ru&product_id=1&offer_id=1
    * POST /api/methods/product.reserve?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&phone=+79628678430&email=admin@admin.ru&product_id=1&offer_id=1
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : [
    *                           'Имя пользователя не может быть пустым'
    *                       ]
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если заказ успешно создан
    */
    protected function process($token = null, 
                               $phone = null, 
                               $email = null, 
                               $product_id, 
                               $offer_id = null, 
                               $multioffers = [],
                               $is_notify = 1)
    {  
        $errors      = [];
        $product_api = new \Catalog\Model\Api();
        $reserve_api = new \Shop\Model\ReservationApi();
        $product     = $product_api->getById($product_id); //Получим сам объект товара
        
        $user = null;
        if ($token){ //Если токен указан
            $user = $this->token->getUser();
        }
        
        if (!$product['id']){
            $errors[] = t("Товар не найден");
        }
        
        if (empty($errors)){
        
            /**
            * @var \Shop\Model\Orm\Reservation $reserve
            */
            $reserve                = $reserve_api->getElement();
            $reserve['amount']      = 1;
            $reserve['phone']       = $user ? $user['phone'] : $phone;
            $reserve['email']       = $user ? $user['email'] : $email;
            $reserve['product_id']  = $product_id;
            $reserve['offer_id']    = $offer_id ? $offer_id : false;
            $reserve['multioffers'] = $multioffers;
            
            if ($reserve['offer_id']) {
                $offer = new \Catalog\Model\Orm\Offer($reserve['offer_id']);
                $reserve['offer'] = $offer['title'];
            } else {
                $offer = \Catalog\Model\Orm\Offer::loadByWhere([
                                                                'product_id' => $reserve['product_id'],
                                                                'sortn' => 0
                ]);
                $reserve['offer']    = $offer['title'];
                $reserve['offer_id'] = $offer['id'];
            }
           
            //Отсылаем письмо или уведомление на телефон если всё в порядке
            if ($reserve_api->save(null, ['is_notify' => $is_notify])) { //OK
                $response['response']['success'] = true;
            } else { //Если есть ошибки
                $errors = $reserve_api->getErrors();
                $response['response']['success'] = false;
            }
        }
        
        
        if (!empty($errors)){ //Переберём ошибки, чтобы сделать единый вид
           $arr = [];
           foreach ($errors as $k=>$error_info){
              if (is_array($error_info)) {
                  unset($errors[$k]); 
                  foreach ($error_info as $error){
                      $arr[] = $error; 
                  }               
              }else{
                  $arr[] = $error_info; 
              }
           } 
           $errors = $arr;
        }

        $response['response']['errors'] = $errors;

        return $response;
    }
}