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
* Возвращает средний чек
*/
class StatisticAvgOrderSum extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка данных')
        ];
    }
    
    /**
    * Возвращает средний чек пользователя, высчитываемый из суммы всех заказов, деленных на количество заказов.
    * В отчет попадают только завершенные заказы
    * 
    * @param string $token Авторизационный токен
    * @param string $period Отчетный период. Может быть month, year, all
    * 
    * @example GET /api/methods/order.statisticAvgOrderSumm?token=fd3c6bb1408059c86e1e7cdfc7a3e661542a6bd3&period=month
    * 
    * <pre>{
    *     "response": {
    *         "date_from": "2016-09-14",
    *         "date_to": "2016-10-14",
    *         "average": 0
    *     }
    * }</pre>
    * 
    * @return возвращает массив с тремя элементами
    */
    public function process($token, $period)
    {
        if (!in_array($period, ['month', 'year', 'all'])) {
            throw new ApiException(t('Неверное значение параметра period, допустимы значения: month, year, all'));
        }
        
        switch($period) {
            case 'month': 
                $date_from = date('Y-m-d', strtotime('-1 month'));
                $date_to = date('Y-m-d');
                break;
            case 'year':
                $date_from = date('Y-m-d', strtotime('-1 year'));
                $date_to = date('Y-m-d');
                break;
            case 'all':
                $date_from = null;
                $date_to = null;
                break;            
        }
        
        $config = \RS\Config\Loader::byModule(__CLASS__);
        
        $req = \RS\Orm\Request::make();
        $req->from(new \Shop\Model\Orm\Order);
        $req->where([
            'site_id' => \RS\Site\Manager::getSiteId()
        ]);
        
        if ($date_from) {
            $req->where("dateof >= '#date_from' AND dateof <= '#date_to'", [
                'date_from' => $date_from,
                'date_to' => $date_to.' 23:59:59'
            ]);
        }
        
        $status_ids = \Shop\Model\UserStatusApi::getStatusesIdByType(\Shop\Model\Orm\UserStatus::STATUS_SUCCESS);
        if (!$status_ids) {
            throw new ApiException(t('Не найдены success статусы'));
        }
        $req->whereIn('status',  $status_ids);
        
        
        $req->select('AVG(totalcost) as average');
        $row = $req->exec()->fetchRow();
        $result = [
            'response' => [
                'date_from' => $date_from,
                'date_to' => $date_to,
                'average' => round($row['average'], 2),
                'currency' => \Catalog\Model\CurrencyApi::getBaseCurrency()->stitle
            ]
        ];
        
        return $result;
    }
}
