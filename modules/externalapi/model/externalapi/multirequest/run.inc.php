<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Multirequest;
use \ExternalApi\Model\Exception as ApiException;

/**
* Выполняет за один раз сразу несколько 
*/
class Run extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    /**
    * Выполняет за один раз сразу несколько запросов к методам API и возвращает сразу несколько ответов
    * 
    * @param array $requests массив со списком запросов 
    * <pre>
    * [
    *   'query1' => [
    *       'url' => 'http://domain.ru/api/methods/order.get', //Можно указывать отдельно URL метода API
    *       'params' => [                                      //Можно указывать отдельно параметры
    *           'token' => '.....',
    *           'order_id' => '.....',
    *           ...
    *       ]
    *   ],
    *   
    *   'query2' => [
    *       'url' => '/api/methods/payment.getList?token=...&product_id=...',  //Можно указывать параметры прямо в URL  
    *   ],
    *   
    *   'query3' => [
    *       'method' => 'user.get', //Вместо URL можно указывать только метод API
    *       'params' => [           //Параметры 
    *           token => '.....',
    *           user_id = '....',
    *           ...
    *       ]
    *   ]
    *   ...
    * ]
    * </pre>
    * 
    * @example GET /api/methods/multirequest.run
    * ?requests[identificator1][url]=http://domain.ru/api/methods/product.get?product_id=1&requests[identificator2][url]=http://domain.ru/api/methods/order.get?order_id=159
    * Ответ:
    * <pre>
    * {
    *     "identificator1": {
    *         "response": {
    *             "product": {
    *                 "id": "1",
    *                 "title": "Моноблок Acer Aspire Z5763",
    *                 "alias": "Monoblok-Acer-Aspire-Z5763",
    *                 "short_description": "Ноутбуки с оптимальным соотношением цены и возможностей...",
    *                 "description": "<p>Ноутбук (англ. notebook &mdash; блокнот, блокнотный ПК) &mdash; ...",
    *                 "barcode": "PW.SFNE2.033",
    *                 "weight": "0",
    *                 "dateof": "2013-08-06 06:08:05",
    *                 "excost": null,
    *                 "unit": "0",
    *                 "min_order": "0",
    *                 "public": "1",
    *                 "xdir": null,
    *                 "maindir": "5",
    *                 "xspec": null,
    *                 "reservation": "default",
    *                 "brand_id": "4",
    *                 "rating": "0.0",
    *                 "group_id": "",
    *                 "xml_id": null,
    *                 "offer_caption": "",
    *                 "meta_title": "",
    *                 "meta_keywords": "",
    *                 "meta_description": "",
    *                 "tax_ids": "category",
    *                 "image": [
    *                     {
    *                         "title": null,
    *                         "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
    *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                     },
    *                     {
    *                         "title": null,
    *                         "original_url": "http://full.readyscript.local/storage/photo/original/a/rialm6nhk3mtddq.jpg",
    *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/rialm6nhk3mtddq_bbf9344f.jpg",
    *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/rialm6nhk3mtddq_84b18d22.jpg"
    *                     },
    *                     {
    *                         "title": null,
    *                         "original_url": "http://full.readyscript.local/storage/photo/original/a/2v0vwg8beojjql7.jpg",
    *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/2v0vwg8beojjql7_abe0a755.jpg",
    *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/2v0vwg8beojjql7_617fb6df.jpg"
    *                     },
    *                     {
    *                         "title": null,
    *                         "original_url": "http://full.readyscript.local/storage/photo/original/a/bz424bme9a63scc.jpg",
    *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/bz424bme9a63scc_e15457b4.jpg",
    *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/bz424bme9a63scc_5f8e268f.jpg"
    *                     },
    *                     {
    *                         "title": null,
    *                         "original_url": "http://full.readyscript.local/storage/photo/original/a/mtziyzq298z5fej.jpg",
    *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/mtziyzq298z5fej_dca66a61.jpg",
    *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/mtziyzq298z5fej_98a7d774.jpg"
    *                     }
    *                 ],
    *                 "xcost": [
    *                     {
    *                         "product_id": "1",
    *                         "cost_id": "1",
    *                         "cost_val": "50500.00",
    *                         "cost_original_val": "50500.00",
    *                         "cost_original_currency": "1"
    *                     },
    *                     {
    *                         "product_id": "1",
    *                         "cost_id": "2",
    *                         "cost_val": "52120.00",
    *                         "cost_original_val": "52120.00",
    *                         "cost_original_currency": "1"
    *                     },
    *                     {
    *                         "product_id": "1",
    *                         "cost_id": "11",
    *                         "cost_val": "0.00",
    *                         "cost_original_val": "0.00",
    *                         "cost_original_currency": "1"
    *                     }
    *                 ],
    *                 "property_values": {
    *                     "588": {
    *                         "id": "588",
    *                         "title": "Цвет",
    *                         "type": "list",
    *                         "unit": "",
    *                         "parent_id": "12",
    *                         "hidden": "0",
    *                         "no_export": "0",
    *                         "value": "Серебристый",
    *                         "parent_title": "Общие характеристики телефона"
    *                     }
    *                 },
    *                 "recommended": [
    *                     {
    *                         "id": "41",
    *                         "title": "Планшет ViewSonic ViewPad 10",
    *                         "alias": "planshet-viewsonic-viewpad-10",
    *                         "short_description": "Ноутбуки серии специально разрабатывались для игр....",
    *                         "description": "<p>Ноутбук (англ. notebook — блокнот, блокнотный ПК) — портативный персональный компьютер...",
    *                         "barcode": "22257-DS4UTN2",
    *                         "weight": "0",
    *                         "dateof": "2013-08-07 11:02:54",
    *                         "excost": null,
    *                         "unit": "0",
    *                         "min_order": null,
    *                         "public": "1",
    *                         "xdir": null,
    *                         "maindir": "16",
    *                         "xspec": null,
    *                         "reservation": "default",
    *                         "brand_id": "0",
    *                         "rating": "0.0",
    *                         "group_id": null,
    *                         "xml_id": null,
    *                         "offer_caption": "",
    *                         "meta_title": "",
    *                         "meta_keywords": "",
    *                         "meta_description": "",
    *                         "tax_ids": "category",
    *                         "property_values": null
    *                     }
    *                 ],
    *                 "concomitant": [
    *                     {
    *                         "id": "41",
    *                         "title": "Планшет ViewSonic ViewPad 10",
    *                         "alias": "planshet-viewsonic-viewpad-10",
    *                         "short_description": "Ноутбуки серии специально разрабатывались для игр. ....",
    *                         "description": "<p>Ноутбук (англ. notebook — блокнот, блокнотный ПК) ....",
    *                         "barcode": "22257-DS4UTN2",
    *                         "weight": "0",
    *                         "dateof": "2013-08-07 11:02:54",
    *                         "excost": null,
    *                         "unit": "0",
    *                         "min_order": null,
    *                         "public": "1",
    *                         "xdir": null,
    *                         "maindir": "16",
    *                         "xspec": null,
    *                         "reservation": "default",
    *                         "brand_id": "0",
    *                         "rating": "0.0",
    *                         "group_id": null,
    *                         "xml_id": null,
    *                         "offer_caption": "",
    *                         "meta_title": "",
    *                         "meta_keywords": "",
    *                         "meta_description": "",
    *                         "tax_ids": "category",
    *                         "property_values": null
    *                     }
    *                 ]
    *             },
    *             "cost": {
    *                 "1": {
    *                     "id": "1",
    *                     "title": "Розничная",
    *                     "type": "manual"
    *                 },
    *                 "2": {
    *                     "id": "2",
    *                     "title": "Зачеркнутая цена",
    *                     "type": "manual"
    *                 },
    *                 "11": {
    *                     "id": "11",
    *                     "title": "Типовое соглашение с клиентом",
    *                     "type": "manual"
    *                 }
    *             },
    *             "currency": {
    *                 "1": {
    *                     "id": "1",
    *                     "title": "RUB",
    *                     "stitle": "р.",
    *                     "is_base": "1",
    *                     "ratio": "1",
    *                     "public": "1",
    *                     "default": "1"
    *                 }
    *             }
    *         }
    *     },
    *     "identificator2": {
    *         "error": {
    *             "code": 6,
    *             "title": "Передан неверный набор параметров. Не найден параметр token"
    *         }
    *     }
    * }
    * </pre>
    * @throws ApiException
    *
    * @return array Возвращает результаты для всех запросов, объявленных в $requests
    */
    protected function process($requests)
    {
        $result = [];
        $config = \RS\Config\Loader::byModule($this);
        $default_version = !empty($config['default_api_version']) ? $config['default_api_version']: 1;
        $default_lang = \ExternalApi\Model\AbstractMethods\AbstractMethod::DEFAULT_LANGUAGE;

        foreach($requests as $id => $request) {
            //Валидируем значения
            if (!isset($request['url']) && !isset($request['method'])) {
                throw new ApiException(t('В запросе %0 должен присутствовать ключ url или method', [$id]), ApiException::ERROR_WRONG_PARAM_VALUE);
            }
            
            try {
                
                list($method, $params) = $this->parseMethodParam($request);
                
                //Запрещаем рекурсивный вызов метода
                if (strtolower($method) == strtolower($this->getSelfMethodName())) {
                    throw new ApiException(t('Метод %0 не может быть вызван', [$method]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
                
                $version = isset($params['v']) ? $params['v'] : $default_version;
                $lang = isset($params['lang']) ? $params['lang'] : $default_lang;
                
                $api_router = new \ExternalApi\Model\ApiRouter($version, $lang);
                $result[$id] = $api_router->runMethod($method, $params);
                
            } catch(\ExternalApi\Model\Exception $e) {
                $result[$id] = $e->getApiError();
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращает метод и параметры одного запроса
    * 
    * @param array $request - массив сведений о запросе
    * @throws ApiException
    * @return array(string Метод, array Параметры)
    */
    protected function parseMethodParam($request)
    {
        $route = \RS\Router\Manager::obj()->getRoute('externalapi-front-apigate');
                
        $params = [];
        if (isset($request['method'])) {
            $method = $request['method'];
            $params = $request['params'];
        } else {
            $info = parse_url($request['url']);
            
            if (!isset($info['path']) || !$route->match(isset($info['host']) ? $info['host'] : null, $info['path'])) {
                throw new ApiException(t('Невозможно извлечь метод API из URL'), ApiException::ERROR_WRONG_PARAM_VALUE);
            }
            
            $method = $route->match['method'];
            
            if (isset($request['params'])) {
                $params = $request['params'];
            } elseif (isset($info['query'])) {
                parse_str($info['query'], $params);
            }
        }
        return [$method, $params];
    }

}
