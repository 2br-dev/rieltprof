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
* Статистический отчет по суммам заказов
*/
class SellStatistic extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
    * Возвращает статистику по сумме и количеству заказов в разрезе годов и месяцев
    * 
    * @param string $token Авторизационный токен
    * @param string $order_type Тип заказа. Возможные значения: <b>all</b> и <b>success</b>
    * @param string $y_axis Что выводить на графике? Сумму или количество заказов. Возможные значения <b>summ</b> или <b>num</b>
    * 
    * @example GET /api/methods/order.sellstatistic?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ:
    * <pre>
    * {    
    *     "response": {
    *         "statistic": [
    *             {
    *                 "label": "2013",
    *                 "data": [
    *                     {
    *                         "x": 1451610000000,
    *                         "y": 0,
    *                         "pointDate": 1356998400000,
    *                         "total_cost": 0,
    *                         "count": 0
    *                     },
    *                     {
    *                         "x": 1454288400000,
    *                         "y": 0,
    *                         "pointDate": 1359676800000,
    *                         "total_cost": 0,
    *                         "count": 0
    *                     }
    *                 ]
    *             },
    *             {
    *                 "label": "2014",
    *                 "data": [
    *                     {
    *                         "x": 1451610000000,
    *                         "y": 0,
    *                         "pointDate": 1388534400000,
    *                         "total_cost": 0,
    *                         "count": 0
    *                     },
    *                     {
    *                         "x": 1454288400000,
    *                         "y": 0,
    *                         "pointDate": 1391212800000,
    *                         "total_cost": 0,
    *                         "count": 0
    *                     }
    *                 ]
    *             }
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает данные для построения графика
    * statistic[0].label - год
    * statistic[0].data - данные одной точки
    * statistic[0].data.x - координаты X на графике timestamp в миллисекундах
    * statistic[0].data.y - сумма или количество заказов
    * statistic[0].data.pointDate - дата на 1 число каждого месяца
    * statistic[0].data.total_cost - сумма заказов
    * statistic[0].data.count - количество заказов
    *
    *
    */
    function process($token, $order_type = 'all', $y_axis = 'summ')
    {
        if (!in_array($order_type, ['all', 'success'])) {
            throw new ApiException(t('Недопустимое значение параметра order_type'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        if (!in_array($y_axis, ['summ', 'num'])) {
            throw new ApiException(t('Недопустимое значение параметра y_axis'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }        
        
        $order_api = new \Shop\Model\OrderApi();
        $data = array_values($order_api->ordersByYears($order_type, $y_axis));
        
        return [
            'response' => [
                'statistic' => $data
            ]
        ];
    }
}
