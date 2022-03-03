<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Catalog\Model\Orm\Property\Item as PropertyItem;
use RS\Cache\Manager as CacheManager;
use RS\View\Engine as ViewEngine;

/**
 * Класс содержит функции по работе с виртуальными категориями (сео фильтрами)
 */
class VirtualDir
{
    const INVALIDATE_TAG = 'virtual_dir';

    private $dir;

    function __construct(Orm\Dir $dir = null)
    {
        $this->dir = $dir;
    }

    /**
     * Возвращает форму одного свойства
     *
     * @param $prop_id
     * @param array $property_data
     * @return bool|string
     * @throws \SmartyException
     */
    public function getPropertyFilterForm($prop_id, $property_data = null)
    {
        $prop = new PropertyItem($prop_id);
        if (!$prop['id']) {
            return false;
        }

        $view = new ViewEngine();
        $view->assign([
            'property' => $prop,
            'data' => $property_data
        ]);

        return $view->fetch('%catalog%/form/dir/virtual_property.tpl');
    }

    /**
     * Возвращает отобранные ID товаров, которые должны отображаться в данной категории
     *
     * @param array $add_dir_ids - список ID категорий, товары из которых необходимо добавить к выборке
     * @param bool $cache - Если true, то будет задействован механизм кэширования
     * @return int[]
     */
    function getFilteredProductIds($add_dir_ids, $cache = true)
    {
        if ($cache) {
            return CacheManager::obj()
                ->watchTables(new Orm\Product())
                ->tags(self::INVALIDATE_TAG)
                ->request([$this, __FUNCTION__], $add_dir_ids, false);
        } else {
            $product_api = new Api();
            $q = $product_api->queryObj();

            $dirs = $add_dir_ids;
            if (!empty($this->dir['virtual_data_arr']['dirs'])) {
                //Добавляем в выборку товары, непосредственно привязанные к категории
                if (!in_array(0, $this->dir['virtual_data_arr']['dirs'])) {
                    $dirs = array_merge($this->dir['virtual_data_arr']['dirs'], (array)$add_dir_ids);
                } else {
                    $dirs = [];
                }
            }

            if (!empty($this->dir['virtual_data_arr']['days_from_create'])) {
                $days = $this->dir['virtual_data_arr']['days_from_create'];
                $filter_date = date('Y-m-d H:i:s', strtotime("-$days days"));
                $product_api->setFilter('dateof', $filter_date, '>');
            }

            if ($dirs) {
                $product_api->setFilter('dir', $dirs, 'in');
            }

            if (!empty($this->dir['virtual_data_arr']['brands'])) {
                if (!in_array(0, $this->dir['virtual_data_arr']['brands'])) {
                    $product_api->setFilter('brand_id', $this->dir['virtual_data_arr']['brands'], 'in');
                }
            }

            if (!empty($this->dir['virtual_data_arr']['properties'])) {
                $prop_api = new PropertyApi();
                $prop_api->getFilteredQuery($this->dir['virtual_data_arr']['properties'], 'A', $q);
            }

            $q->select = 'A.id';
            $ids = $q->exec()->fetchSelected('id', 'id');

            if (!$ids) { //Если не отобран ни один товар
                $ids = [0];
            }

            return $ids;
        }
    }
}
