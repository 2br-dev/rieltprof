<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use Main\Model\NoticeSystem\HasMeterInterface;
use Main\Model\NoticeSystem\MeterApi;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Orm\ProductsReturn;
use Shop\Model\Orm\ProductsReturnOrderItem;

class ProductsReturnApi extends EntityList implements HasMeterInterface
{
    const METER_RETURN = 'rs-admin-menu-returns';
    public $return;

    /**
     * ProductsReturnApi constructor.
     */
    function __construct()
    {
        parent::__construct(new ProductsReturn(), [
            'multisite' => true,
            'aliasField' => 'return_num',
            'defaultOrder' => 'dateof DESC',
        ]);
        $this->return = $this->getElement();
    }


    function getMeterApi($user_id = null)
    {
        return new MeterApi($this->obj_instance,
            self::METER_RETURN,
            $this->getSiteContext(),
            $user_id);
    }

    /**
     * Возвращает список товаров на возврат по id заказа
     *
     * @param integer $user_id - id пользователя
     * @return array
     */
    function getReturnItemsByUserId($user_id)
    {
        return OrmRequest::make()
            ->select("OI.*")
            ->from(new ProductsReturnOrderItem(), "OI")
            ->join(new ProductsReturn(), "OI.return_id = R.id", "R")
            ->where([
                'R.user_id' => $user_id,
            ])
            ->objects(null, 'uniq');
    }

    /**
     * Возвращает список товаров на возврат по id заказа
     *
     * @param integer $order_id - id заказа
     * @return array
     */
    function getReturnItemsByOrder($order_id)
    {
        return OrmRequest::make()
            ->select("OI.*")
            ->from(new ProductsReturnOrderItem(), "OI")
            ->join(new ProductsReturn(), "OI.return_id = R.id", "R")
            ->where([
                'R.order_id' => $order_id,
            ])
            ->objects();
    }

    /**
     * Возвращает массив возвратов
     *
     * @param integer $user_id - идентификатор пользователя
     *
     * @return array
     */
    function getReturnsByUserId($user_id)
    {
        $this->setFilter('user_id', $user_id);
        return $this->getList();
    }
}
