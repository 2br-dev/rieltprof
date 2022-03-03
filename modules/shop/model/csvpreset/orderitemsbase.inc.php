<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvPreset;
use \Shop\Model\Orm;

/**
* Набор колонок описывающих связь товара с заказами
*/
class OrderItemsBase extends \RS\Csv\Preset\Base
{
    /**
    * Возвращает объект Orm\Request для стартовой выборки элементов
    * 
    * @return \RS\Orm\Request
    */
    function getSelectRequest()
    {
        if (!$this->select_request) {
            $q = clone \Shop\Model\OrderApi::getSavedRequest('Shop\Controller\Admin\OrderCtrl_list');
            $q->leftjoin(new Orm\OrderItem(), 'I.order_id = A.id', 'I')
                ->where(['type' => 'product']);
            
            $this->select_request = $q;
        }
        return $this->select_request;
    }
}
