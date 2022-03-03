<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

/**
* Возвращает списки для формирования нового адреса при оформлении заказа. Страны, регионы, города.
*/
class GetAddressListsInfo extends \ExternalApi\Model\AbstractMethods\AbstractMethod
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
            self::RIGHT_LOAD => t('Загрузка списков')
        ];
    }
    
   
    /**
    * Возвращает списки для формирования нового адреса при оформлении заказа. Страны, регионы, города.
    * Секции:
    * <b>country</b> - список стран
    * <b>regions</b> - список регионов
    * <b>city</b>    - список городов
    * 
    * @param string $token Авторизационный токен
    * 
    * 
    * 
    * @example GET /api/methods/checkout.getaddresslistsinfo         
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "lists": {
    *           "country" : [
    *              {             
    *                "id": 1,
    *                "title": "Россия"
    *              }
    *           ],
    *           "regions" : [
    *              {
    *                "id": "73",
    *                "title": "Адыгея",
    *                "parent_id": "1"
    *              },
    *              ...
    *           ],
    *           "city" : [
    *              {
    *                 "id": "857",
    *                 "title": "Абаза",
    *                 "parent_id": "47",
    *                 "zipcode": "350000",
    *              },
    *              ...
    *           ],
    *         }
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список объектов и связанные с ним сведения.
    */
    protected function process($token = null, $sections = ['country', 'regions', 'city'])
    {
        $result['response']['lists'] = [];
        $region_api = new \Shop\Model\RegionApi();
        
        if (in_array('country', $sections)){ //Список стран
           $arr = [];
           $countries = $region_api->countryList(); 
           
           $result['response']['lists']['country'] = null;
           if (!empty($countries)){
               foreach ($countries as $id=>$title){
                  $arr[] = [
                    'id' => $id,
                    'title' => $title,
                  ];
               }
               $result['response']['lists']['country'] = $arr;
           }
        }
        
        if (in_array('regions', $sections)){ //Список регионов
           $arr = [];
           $regions = \ExternalApi\Model\Utils::extractOrmList($region_api->regionsList()); 
           
           $result['response']['lists']['regions'] = null;
           if (!empty($regions)){
               foreach ($regions as $region){
                  $arr[] = $region; 
               }
               $result['response']['lists']['regions'] = $arr;
           }
        }
        
        if (in_array('city', $sections)){ //Список городов
           $arr = [];
           $region = new \Shop\Model\Orm\Region();
           $region->getPropertyIterator()->append([
               'zipcode' => new \RS\Orm\Type\Varchar([
                    'maxLength' => 20,
                    'visible' => true,
                    'AppVisible' => true,
                    'cityVisible' => true,
                    'description' => t('Индекс'),
               ]),
           ]);
           $cities = \ExternalApi\Model\Utils::extractOrmList($region_api->citiesList());   
           
           $result['response']['lists']['city'] = null;
           if (!empty($cities)){
               foreach ($cities as $city){
                  $arr[] = $city; 
               }
               $result['response']['lists']['city'] = $arr;
           }
        }
        
        return $result;
    }
}