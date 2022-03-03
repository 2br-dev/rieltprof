<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Application\Application;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Region;
use Shop\Model\RegionApi;
use Shop\Model\SelectedAddress;

/**
 * Смена выбранного города
 */
class SelectPvz extends Front
{
    public $delivery;
    public $city_id;
    /** @var DeliveryApi */
    public $delivery_api;

    public function init()
    {
        $this->delivery = $this->url->request('delivery', TYPE_ARRAY);
        $this->city_id = $this->url->request('city_id', TYPE_INTEGER);
        $this->delivery_api = new DeliveryApi();
    }

    public function actionIndex()
    {
        $this->wrapOutput(false);

        $city = new Region($this->city_id);
        $address = Address::createFromRegion($city);
        $this->delivery_api->setFilter([
            'id:in' => implode(',', $this->delivery),
        ]);
        /** @var Delivery[] $delivery_list */
        $delivery_list = $this->delivery_api->getList();
        /** @var Pvz[][] $delivery_pvz_list */
        $delivery_pvz_list = [];
        foreach ($delivery_list as $delivery) {
            if ($list = $delivery->getTypeObject()->getPvzByAddress($address)) {
                $delivery_pvz_list[$delivery['id']] = $list;
            }
        }

        $pvz_json = [];
        foreach ($delivery_pvz_list as $delivery_id => $delivery_pvz) {
            foreach ($delivery_pvz as $k => $pvz) {
                $pvz_json[$delivery_id][] = $pvz->asArray();
            }
        }

        $this->view->assign([
            'delivery_pvz_list' => $delivery_pvz_list,
            'pvz_json' => json_encode($pvz_json, JSON_UNESCAPED_UNICODE),
        ]);
        return $this->result->setTemplate('delivery/select_pvz.tpl');
    }
}
