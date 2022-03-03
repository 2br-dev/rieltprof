<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;
use Catalog\Model\OfferApi;
use \ExternalApi\Model\Exception as ApiException;
  
/**
* Обновляет указанные остатоки на массиве складов для товара
*/
class SetOfferStock extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{

    const RIGHT_UPDATE = 1;

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
            self::RIGHT_UPDATE => t('Обновление остатков склада')
        ];
    }
    
    /**
    * Возвращает список прав, требуемых для запуска метода API
    * По умолчанию для запуска метода нужны все права, что присутствуют в методе
    * 
    * @return [код1, код2, ...]
    */
    public function getRunRights()
    {
        return [
            self::RIGHT_UPDATE
        ]; //Проверка прав будет непосредственно в теле метода
    }    
    
    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Product();
    }

    
    /**
    * Обновляет указанные остатоки на массиве складов для товара
    * 
    * @param string $token Авторизационный токен
    * @param string $client_id id клиентского приложения
    * @param string $client_secret пароль клиентского приложения
    * @param integer $product_id id товара
    * @param integer $offer_id id комплектации. У товара без комплектации или для нулевой комплектации допускается 0
    * @param array $values новые значения остатков на складах в виде массива [1] => 10.00, [Центральный склад] => 12.00 . Где ключ либо id склада, либо его название, значение это количествоа на складе
    * @param array $regfields_arr поля пользователя из настроек модуля пользователь
    *
    * @example POST|GET /api/methods/product.setOfferStockNum?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&client_id=sadasljcsdacnilsdanhoiuh3214&product_id=1&offer_id=1&value[Основной склад]=100.00
    * 
    * <pre>
    *  {
    *      "response": {
    *            "success" : false, //Или true в случае успеха
    *            "errors" : ['Ошибка'],    
    *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
    *      }
    *   }</pre>
    * @throws ApiException
    * @return array Возращает, пустой массив ошибок, если успешно
    */
    protected function process($token, $client_id, $client_secret, $product_id, $offer_id, $values)
    {
        //Проверим предварительно приложение
        $app = \RS\RemoteApp\Manager::getAppByType($client_id);

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        $product = new \Catalog\Model\Orm\Product($product_id);
        if (!$product['id']){
            throw new ApiException(t('Товар не найден или удален.'));
        }

        if (!is_array($values)){
            throw new ApiException(t('Значение должно быть массивом.'));
        }


        $product->fillOffersStock();
        if ($product->isOffersUse() && $offer_id){ //Если есть комплектации

            $offer = new \Catalog\Model\Orm\Offer($offer_id);

            if (!$offer){
                throw new ApiException(t('Комплектация товара с артикулом %0 не найдена.', [$offer_id]));
            }
        }else{
            $offer = \RS\Orm\Request::make() //Получим нудевую комплектацию
                ->from(new \Catalog\Model\Orm\Offer())
                ->where([
                    'product_id' => $product_id,
                    'sortn' => 0
                ])->object();

            if (!$offer){ //Если по какой-то причине нет комплектации
                $offer = new \Catalog\Model\Orm\Offer();
                $offer['product_id'] = $product_id;
                $offer['sortn'] = 0;
                $offer->insert();
            }
        }

        $stock_num = $offer['stock_num'];

        foreach($values as $warehouse_id=>&$value){
            if (!is_numeric($warehouse_id)){
                $warehouse = \RS\Orm\Request::make()
                    ->from(new \Catalog\Model\Orm\WareHouse())
                    ->where([
                        'title' => $warehouse_id,
                        'site_id' => \RS\Site\Manager::getSiteId(),
                    ])->object();

                if (!$warehouse){
                    throw new ApiException(t('Склад %0 - не найден. Проверте название.', [$warehouse_id]));
                }

                $warehouse_id = $warehouse['id'];
            }else{
                $warehouse = new \Catalog\Model\Orm\WareHouse($warehouse_id);
                if (!$warehouse['id']){
                    throw new ApiException(t('Склад не найден.', [$warehouse_id]));
                }
            }

            $value = (float)$value;
            $stock_num[$warehouse_id] = $value;
        }
        $offer['stock_num'] = $stock_num;

        //Обновим комплектацию
        if ($offer->update()){
            $offersapi = new OfferApi();
            $offersapi->updateProductNum($offer['product_id']);
            $response['response']['success'] = true;
        }else{
            $errors = $product->getErrors();
            $response['response']['success'] = false;
            $response['response']['errors']  = $errors;
        }
        
        return $response;
    }
}