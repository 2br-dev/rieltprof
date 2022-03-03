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
* Статистический отчет по количеству или суммам заказов за последний месяц
*/
class SellStatisticMonth extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
    * Возвращает статистику по сумме и количеству заказов за последние 30 дней
    * 
    * @param string $token Авторизационный токен
    * @param string $order_type Тип заказов, присутствующих на графике. Возможные значения <b>all</b>(Все заказы) или <b>success</b>(Только завершенные)
    * @param string $y_axis Что выводить на графике? Сумму или количество заказов. Возможные значения <b>summ</b> или <b>num</b>
    * 
    * @example GET /api/methods/order.sellstatisticmonth?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "statistic": [
    *             [
    *                 {
    *                     "x": 1476363151000,
    *                     "y": 0,
    *                     "total_cost": 0,
    *                     "count": 0
    *                 },
    *                 {
    *                     "x": 1476449543000,
    *                     "y": "545.00",
    *                     "total_cost": "545.00",
    *                     "count": "1"
    *                 }
    *             ]
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return Возвращает данные для построения графика
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
        $data = $order_api->ordersByMonth($order_type, $y_axis);
        
        return [
            'response' => [
                'statistic' => $data[0]
            ]
        ];
    }
}
