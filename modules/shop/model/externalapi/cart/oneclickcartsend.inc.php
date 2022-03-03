<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cart;

/**
* Создаёт заказ Покупкой в один клик в корзине
*/
class OneClickCartSend extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false; //Токен не обязателен
    
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
    * Создаёт заказ в один клик из переданных данных
    * 
    * @param string $token Авторизационный токен
    * @param string $name ФИО пользователя
    * @param string $phone телефон пользователя. Например +79XX234XX00
    * @param array $clickfields массив дополнительных полей со сведениями
    * 
    * @example POST /api/methods/cart.oneclickcartsend?name=Алексей&phone=+79628678430
    * 
    * POST /api/methods/cart.oneclickcartsend?name=Алексей&phone=+79628678430&clickfields[email]=email@mail.ru
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Имя пользователя не может быть пустым']
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если заказ успешно создан
    */
    protected function process($token = null, $name, $phone, $clickfields = [])
    {          
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
        $click_fields_manager->setErrorPrefix('clickfield_');
        $click_fields_manager->setArrayWrapper('clickfields');
        
        $errors  = [];
        $api = new \Catalog\Model\OneClickApi();
        if ($api->checkFieldsFromPostToSend($click_fields_manager, false)) { //OK
            $api->send($api->getPreparedProductsFromCart()); //Отправим данные
            //Очистим корзину
            $cart = \Shop\Model\Cart::currentCart();
            $cart->clean();
            $response['response']['success'] = true;
        }else{
            $errors = $api->getErrors();
            $response['response']['success'] = false;
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