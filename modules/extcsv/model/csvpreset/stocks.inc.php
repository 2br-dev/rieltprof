<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Model\CsvPreset;

use Catalog\Model\CsvPreset\OfferStock as CatalogPresetOfferStock;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Orm\Request as OrmRequest;

/**
 * Пресет выгружает/загружает остатки основной комплектации по складам
 */
class Stocks extends CatalogPresetOfferStock
{
    /**
     * Импортирует данные одной строки текущего пресета в базу (от родителя отличается только тем, куда записывается $result_array)
     */
    function importColumnsData()
    {
        if (isset($this->row)) {
            $catalog_config = ConfigLoader::byModule('catalog');
            $result_array = [];
            if ($catalog_config['csv_dont_delete_stocks']) {
                /** @var \Catalog\Model\CsvPreset\SimplePriceStockBase $base_preset */
                $base_preset = $this->schema->getPreset($this->link_preset_id);
                /** @var Product $product */
                if ($product = $base_preset->loadObject()) {
                    $product->fillOffersStock();
                    if (isset($product['offers']['items'])) {
                        $result_array = $product->getMainOffer()['stock_num'];
                    }
                }
            }

            foreach ($this->row as $column_title => $value) {
                $warehouse_info = explode("_", $column_title);
                $warehouse_id = $warehouse_info[1];        //title склада

                if ($value !== '') {
                    $result_array[$warehouse_id] = trim($value);
                }
            }
            if ($result_array) {
                $this->schema->getPreset($this->link_preset_id)->row['offers']['main']['stock_num'] = $result_array;
            }
        }
    }

    /**
     * Загружает связанные данные
     *
     * @return void
     */
    function loadData()
    {
        $this->row = [];
        if ($this->schema->ids) {
            $this->rows = OrmRequest::make()
                ->from($this->ormObject, 'A')
                ->join(new Offer(), 'A.offer_id = O.id', 'O')
                ->whereIn('A.' . $this->link_foreign_field, $this->schema->ids)
                ->where(['O.sortn' => 0])
                ->objects(null, $this->link_foreign_field, true);
        }
    }
}
