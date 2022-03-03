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
use RS\View\Engine as ViewEngine;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Region;
use Shop\Model\RegionApi;
use Shop\Model\SelectedAddress;

/**
 * Смена выбранного города
 */
class SelectedAddressChange extends Front
{
    /** @var Region[] */
    protected $marked;
    /** @var Region[] */
    protected $marked_countries;
    /** @var Region[] */
    protected $marked_regions;
    /** @var Region[] */
    protected $marked_cities;

    public $new_region_id;
    public $new_region_title;
    public $referrer;
    /** @var RegionApi */
    public $region_api;

    /**
     * Функция, вызывающяся сразу после конструктора
     * в случае успешной инициализации ничего не должна возвращать (null),
     * в случае ошибки должна вернуть текст ошибки, который будет возвращен при вызове _exec();
     */
    function init()
    {
        $this->new_region_id = $this->url->request('region_id', TYPE_INTEGER);
        $this->new_region_title = $this->url->request('region_title', TYPE_STRING);
        $this->referrer = $this->url->request('referrer', TYPE_STRING, $this->url->server('HTTP_REFERER'));
        /** @var RegionApi */
        $this->region_api = new RegionApi();
    }

    public function actionIndex()
    {
        $selected_address = SelectedAddress::getInstance();

        if ($this->new_region_id || $this->new_region_title) {
            $filter = [];
            if ($this->new_region_id) {
                $filter['|id'] = $this->new_region_id;
            }
            if ($this->new_region_title) {
                $filter['|title:%like%'] = $this->new_region_title;
            }

            /** @var Region $region */
            $region = $this->region_api->resetQueryObject()
                ->setFilter([$filter])
                ->setOrder('id = #0 desc', [$this->new_region_id])
                ->queryObj()->object();

            if (!empty($region['id'])) {
                $selected_address->setAddressFromRegion($region);
                if ($this->url->isPost()) {
                    return $this->result->setSuccess(true)->setAjaxRedirect($this->referrer);
                } else {
                    Application::getInstance()->redirect($this->referrer);
                }
            }
        }

        $this->view->assign([
            'selected_address' => $selected_address,
        ]);
        return $this->result->setTemplate('address/selected_address_change.tpl');
    }

    /**
     * Поиск города для автокомлита
     *
     * @return false|string
     */
    public function actionRegionAutocomplete()
    {
        $this->wrapOutput(false);
        $query = $this->url->request('term', TYPE_STRING);

        $region_api = new RegionApi();
        $region_api->setFilter([
            'title:%like%' => $query,
            'is_city' => 1,
        ]);
        $region_api->setOrder('INSTR(title, "#0")', [$query]);

        /** @var Region[] $region_list */
        $region_list = $region_api->getList(1, 5);
        $result = [];
        foreach ($region_list as $city) {
            $region = $city->getParent();
            $country = $region->getParent();

            $result[] = [
                'label' => $city['title'] . ', ' . $region['title'],
                'address_data' => [
                    'city' => $city['title'],
                    'city_id' => $city['id'],
                    'region' => $region['title'],
                    'region_id' => $region['id'],
                    'country' => $country['title'],
                    'country_id' => $country['id'],
                ],
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function actionGetAddressByRegion()
    {
        $this->wrapOutput(false);
        $region_id = $this->url->request('region_id', TYPE_INTEGER, null);

        if (!$region_id) {
            return $this->result->setSuccess(false)->addSection('error', t('Не передан id региона'));
        }

        $region = new Region($region_id);

        if (!$region['id']) {
            return $this->result->setSuccess(false)->addSection('error', t('Указанный регион не найден'));
        }

        $address = Address::createFromRegion($region);

        $result = [
            'country' => $address->getCountry()['title'],
            'country_id' => $address->getCountry()['id'],
            'region' => $address->getRegion()['title'],
            'region_id' => $address->getRegion()['id'],
            'city' => $address->getCity()['title'],
            'city_id' => $address->getCity()['id'],
        ];

        return $this->result->setSuccess(true)->addSection('address', $result);
    }

    public function actionGetRegionsByParent()
    {
        $this->wrapOutput(false);
        $parent_id = $this->url->request('parent_id', TYPE_INTEGER, 0);

        $view = new ViewEngine();
        $view->assign([
            'region_list' => $this->getRegionsByParent($parent_id),
        ]);
        return $this->result->setSuccess(true)->addSection('regionBlock', $view->fetch('%shop%/address/selected_address_change_regions.tpl'));
    }

    /**
     * Возвращает список дочерних регионов
     *
     * @param int $parent_id
     * @return Region[]
     */
    public function getRegionsByParent(int $parent_id): array
    {
        $region_api = new RegionApi();
        $region_api->setFilter(['parent_id' => $parent_id]);
        return $region_api->getList();
    }

    /**
     * Возвращает структурированное дерево городов
     *
     *  @return array
     */
    public function getMarkedCitiesTree(): array
    {
        $marked_cities_tree = [];
        $marked_regions = $this->getMarkedRegions();
        $marked_countries = $this->getMarkedCountries();
        foreach ($this->getMarkedCities() as $city) {
            $region = $marked_regions[$city['parent_id']];
            $country = $marked_countries[$region['parent_id']];
            $marked_cities_tree[$country['id']][$region['id']][$city['id']] = $city;
        }
        return $marked_cities_tree;
    }

    /**
     * Возвращает выделенные города
     *
     * @return Region[]
     */
    public function getMarkedCities()
    {
        if ($this->marked_cities === null) {
            $this->sortMarked();
        }
        return $this->marked_cities;
    }

    /**
     * Возвращает выделенные регионы
     *
     * @return Region[]
     */
    public function getMarkedRegions()
    {
        if ($this->marked_regions === null) {
            $this->sortMarked();
        }
        return $this->marked_regions;
    }

    /**
     * Возвращает выделенные страны
     *
     * @return Region[]
     */
    public function getMarkedCountries()
    {
        if ($this->marked_countries === null) {
            $this->sortMarked();
        }
        return $this->marked_countries;
    }

    /**
     * Сортирует выделенные регионы
     *
     * @return void
     */
    protected function sortMarked(): void
    {
        $this->marked_countries = [];
        $this->marked_regions = [];
        $this->marked_cities = [];
        foreach ($this->getMarked() as $region) {
            if ($region['is_city']) {
                $this->marked_cities[$region['id']] = $region;
            } elseif ($region['parent_id'] == 0) {
                $this->marked_countries[$region['id']] = $region;
            } else {
                $this->marked_regions[$region['id']] = $region;
            }
        }
    }

    /**
     * Возвращает список всех выделенных регионов
     *
     * @return Region[]
     */
    public function getMarked()
    {
        if ($this->marked === null) {
            $this->marked = [];
            $config = ConfigLoader::byModule($this);
            $region_api = new RegionApi();
            if ($config['regions_marked_when_change_selected_address']) {
                $marked_regions_ids = $region_api->getParentIds($config['regions_marked_when_change_selected_address']);
                $region_api->setFilter(['id:in' => implode(',', $marked_regions_ids)]);
                /** @var Region[] $marked */
                $this->marked = $region_api->getList();
            }
        }
        return $this->marked;
    }
}
