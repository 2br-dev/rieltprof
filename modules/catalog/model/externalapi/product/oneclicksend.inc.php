<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;

/**
* Отправляет заявку купить в 1 клик по товару
*/
class OneClickSend extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
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
    * Отправляет заявку купить в 1 клик по товару
    * 
    * @param string $name ФИО пользователя
    * @param string $phone телефон пользователя. Например +79XX234XX00
    * @param integer $product_id id товара
    * @param integer $offer_id id комплектации
    * @param array $multioffers телефон пользователя. Например +79XX234XX00
    * @param array $clickfields массив дополнительных полей с данными
    * 
    * @example GET /api/methods/product.oneclicksend?name=Алексей&phone=+79628678430&product_id=1&offer_id=1
    * 
    * GET /api/methods/product.oneclicksend?name=Алексей&phone=+79628678430&product_id=1&offer_id=1&clickfields[email]=email@mail.ru
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
    protected function process($token = null, $name, $phone, $product_id, $offer_id, $multioffers = [], $clickfields = [])
    {  
        $product_api  = new \Catalog\Model\Api();
        $oneclick_api = new \Catalog\Model\OneClickApi();
        $product      = $product_api->getById($product_id); //Получим сам объект товара
        $errors       = [];
        
        if (!$product['id']){
            $errors[] = t("Товар не найден");
        }
        
        if (empty($errors)){
            //Получим дополнительные поля для формы покупки в один
            /**
            * @var \RS\Config\UserFieldsManager
            */
            $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
            $click_fields_manager->setErrorPrefix('clickfield_');
            $click_fields_manager->setArrayWrapper('clickfields');

            $offer_fields = $oneclick_api->prepareProductOfferFields($product, $offer_id, $multioffers);

            $product['offer_fields'] = $offer_fields;

            //Отсылаем письмо или уведомление на телефон если всё в порядке
            if ($oneclick_api->checkFieldsFromPostToSend($click_fields_manager, false)) { //OK
               $oneclick_api->send([$product]);
               $response['response']['success'] = true;
            } else { //Если есть ошибки
               $errors = $oneclick_api->getErrors();
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