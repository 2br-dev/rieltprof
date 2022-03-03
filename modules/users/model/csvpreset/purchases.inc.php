<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Users\Model\CsvPreset;

use RS\Csv\Preset\AbstractPreset;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Users\Model\Orm\User;

class Purchases extends AbstractPreset
{
    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns(): array
    {
        return [
            $this->id.'-orders_count' => [
                'key' => 'orders_count',
                'title' => t('Количество заказов'),
            ],
            $this->id.'-orders_sum' => [
                'key' => 'orders_sum',
                'title' => t('Сумма покупок'),
            ],
            $this->id.'-last_order_date' => [
                'key' => 'last_order_date',
                'title' => t('Дата последнего заказа'),
            ],
        ];
    }

    /**
     * Получает данные для колонки
     *
     * @param integer $n - номер колонки
     * @return array
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    function getColumnsData($n)
    {
        /** @var User $user */
        $user = $this->schema->rows[$n];

        $orders_data = (new OrmRequest())
            ->select('totalcost, dateof')
            ->from(new Order())
            ->where([
                'user_id' => $user['id'],
                'site_id' => SiteManager::getSiteId(),
            ])
            ->orderby('dateof desc')
            ->exec()->fetchAll();

        $orders_count = count($orders_data);
        $last_order_date = ($orders_count) ? reset($orders_data)['dateof'] : '';
        $orders_sum = 0;
        foreach ($orders_data as $order) {
            $orders_sum += $order['totalcost'];
        }

        $result = [
            $this->id.'-orders_count' => $orders_count,
            $this->id.'-orders_sum' => $orders_sum,
            $this->id.'-last_order_date' => $last_order_date,
        ];

        return $result;
    }

    /**
     * Импортирует одну строку данных
     * Пустой метод, т.к. в импорте не участвует, только в экспорте
     *
     * @return void
     */
    function importColumnsData()
    {}
}
