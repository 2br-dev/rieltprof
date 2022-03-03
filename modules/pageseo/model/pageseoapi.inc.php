<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Model;

class PageSeoApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \PageSeo\Model\Orm\PageSeo,
        [
            'multisite' => true
        ]);
    }
    
    function pageSeoList($page, $page_size)
    {
        $list = $this->getList($page, $page_size);
        foreach($list as $item) {
            $route = $item->getRoute();
            $item['_id'] = $item['route_id'];
            $item['description'] = $route->getDescription();
            $item['routeview'] = $route->getPatternsView();
        }
        return $list;
    }
    
    public static function getPageSeo($route_id)
    {
        $api = new self();
        $api->setFilter('route_id', $route_id);
        return $api->getFirst();
    }
    
}

