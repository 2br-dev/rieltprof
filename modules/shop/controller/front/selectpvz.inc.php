<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Application\Application;
use RS\Controller\Front;
use RS\Router\Manager;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Region;

/**
 * Смена выбранного города
 */
class SelectPvz extends Front
{
    public $delivery;
    public $city_id;
    public $city;
    public $region_id;
    public $region;
    public $country_id;
    public $country;
    public $admin_zone;
    /** @var DeliveryApi */
    public $delivery_api;

    public function init()
    {
        $this->delivery = $this->url->request('delivery', TYPE_ARRAY);
        $this->city_id = $this->url->request('city_id', TYPE_INTEGER);
        $this->city = $this->url->request('city', TYPE_INTEGER);
        $this->region_id = $this->url->request('region_id', TYPE_INTEGER);
        $this->region = $this->url->request('region', TYPE_INTEGER);
        $this->country_id = $this->url->request('country_id', TYPE_INTEGER);
        $this->country = $this->url->request('country', TYPE_INTEGER);
        $this->admin_zone = $this->url->request('admin_zone', TYPE_INTEGER, 0);
        $this->delivery_api = new DeliveryApi();
    }

    public function actionIndex()
    {
        $this->wrapOutput(false);

        if ($this->city_id) {
            $city = new Region($this->city_id);
            $address = Address::createFromRegion($city);
        } elseif ($this->region_id) {
            $region = new Region($this->region_id);
            $address = Address::createFromRegion($region);
            $address['city'] = $this->city;
        } elseif ($this->country_id) {
            $country = new Region($this->country_id);
            $address = Address::createFromRegion($country);
            $address['city'] = $this->city;
            $address['region'] = $this->region;
        } else {
            $address = new Address();
            $address['city'] = $this->city;
            $address['region'] = $this->region;
            $address['country'] = $this->country;
        }

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
                $pvz->setNote(str_replace('"', "`", $pvz->getNote()));
                $pvz_json[$delivery_id][] = $pvz->asArray();
            }
        }

        $this->view->assign([
            'delivery_pvz_list' => $delivery_pvz_list,
            'pvz_json' => json_encode($pvz_json, JSON_UNESCAPED_UNICODE),
        ]);

        $template = ($this->admin_zone) ? 'admin/select_pvz.tpl' : 'delivery/select_pvz.tpl';
        return $this->result->setTemplate($template);
    }
}
