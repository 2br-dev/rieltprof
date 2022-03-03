<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;
use \ExternalApi\Model\Exception as ApiException;
  
/**
* Обновляет сведения о пользователе, перезаписывает значения полей
*/
class SetCost extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
            self::RIGHT_UPDATE => t('Обновление цены')
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
    * Обновляет указанную цену товара
    * 
    * @param string $token Авторизационный токен
    * @param string $client_id id клиентского приложения
    * @param string $client_secret пароль клиентского приложения
    * @param integer $product_id id товара
    * @param string $cost_id id цены или её название как на сайте. Например: 'Закупочная цена'.
    * @param float $value новое значение цены. Например 12.05
    * @param string $currency валюта (трехсимвольный идентификатор RUB, EUR и т.д..). Указать как на сайте. Если не указано, то будет указана цена по умолчанию
    * @param array $regfields_arr поля пользователя из настроек модуля пользователь
    *
    * @example POST|GET /api/methods/product.setCost?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&client_id=sadasljcsdacnilsdanhoiuh3214&product_id=1&cost_id=Зачернутая цена&value=100.00&currency=RUB
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
    protected function process($token, $client_id, $client_secret, $product_id, $cost_id, $value = 0.00, $currency = null)
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

        if (!is_numeric($cost_id)){
            $cost = \RS\Orm\Request::make()
                            ->from(new \Catalog\Model\Orm\Typecost())
                            ->where([
                                'title' => $cost_id,
                                'site_id' => \RS\Site\Manager::getSiteId(),
                            ])->object();

            if (!$cost){
                throw new ApiException(t('Цена %0 - не найдена. Проверте название.', [$cost_id]));
            }

            $cost_id = $cost['id'];
        }else{
            $cost = new \Catalog\Model\Orm\Typecost($cost_id);
            if (!$cost['id']){
                throw new ApiException(t('Цена не найдена.', [$cost_id]));
            }
        }

        $value = (float)$value;
        if (!$currency){
            $current_currency = \Catalog\Model\CurrencyApi::getBaseCurrency();
            $currency = $current_currency['id'];
        }else{
            $current_currency = \Catalog\Model\Orm\Currency::loadByWhere([
                'site_id' => \RS\Site\Manager::getSiteId(),
                'title' => $currency
            ]);
            if (!$current_currency['id']){
                $current_currency = \Catalog\Model\CurrencyApi::getBaseCurrency();
            }
            $currency = $current_currency['id'];
        }

        $product->fillCost();
        $excost = $product['excost'];
        $excost[$cost_id]['cost_original_val'] = $value;
        $excost[$cost_id]['cost_original_currency'] = $currency;
        $product['excost'] = $excost;
        $product['edit_from_api'] = true;


        //Обновим товар
        if ($product->update()){
            $response['response']['success'] = true;
        }else{
            $errors = $product->getErrors();
            $response['response']['success'] = false;
            $response['response']['errors']  = $errors;
        }
        
        return $response;
    }
}