<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает года, за которые есть статистика. Для фильтрации
*/
class SellStatisticYears extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;
        
    
    function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка статистики')
        ];
    }
    
    /**
    * Возвращает года, за которые есть статистика. Для фильтрации
    * 
    * @param string $token Авторизационный токен
    * @param string $order_type Тип заказов, присутствующих на графике. Возможные значения <b>all</b>(Все заказы) или <b>success</b>(Только завершенные)
    *
    * @example GET /api/methods/order.sellstatistic?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ:
    * <pre>{
    *     "response": {
    *         "years": [2011, 2012, 2013]
    *     }
    * }
    * </pre>
    * 
    * @return Возвращает данные для построения графика
    */
    function process($token, $order_type = 'all')
    {
        $order_api = new \Shop\Model\OrderApi();
        $years = $order_api->getOrderYears($order_type);
        
        return [
            'response' => [
                'years' => $years
            ]
        ];
    }
}