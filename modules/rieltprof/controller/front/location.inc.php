<?php

namespace rieltprof\Controller\Front;

use RS\Controller\Front;

/**
 * Фронт контроллер
 */
class Location extends Front
{
    function actionIndex()
    {
        $location_api = new \Rieltprof\Model\LocationApi();
        $location = [];
        $regions = $location_api->getRegions();
        foreach ($regions as $key => $region){
            $location[$key]['id'] = $region['id'];
            $location[$key]['title'] = $region['title'];
            $location_api->clearFilter();
            $cities = $location_api->getCitiesByRegionId($region['id']);
            $location[$key]['cities'] = $cities;
        }
        $this->view->assign('location', $location);
        return $this->result->setTemplate('%rieltprof%/location/location_select.tpl');
    }
}
