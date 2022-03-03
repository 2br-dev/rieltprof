<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

/**
* Возвращает список регионов из справочника по заданному городу
*/
class RegionTools extends \RS\Controller\Front
{
    function actionListByParent()
    {
        $this->api = new \Shop\Model\RegionApi();
        
        $parent = $this->url->request('parent', TYPE_INTEGER);
        $result = [];
        if ($parent) {
            $this->api->setFilter('parent_id', $parent);
            $list = $this->api->getAssocList('id', 'title');
            foreach($list as $key => $value) {
                $result[] = ['key' => $key, 'value' => $value];
            }
        }
        return $this->result->addSection('list', $result);
    }
}