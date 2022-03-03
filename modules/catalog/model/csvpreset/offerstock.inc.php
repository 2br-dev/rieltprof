<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

use Catalog\Model\OfferApi;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\Orm\Xstock;
use Catalog\Model\WareHouseApi;
use RS\Config\Loader as ConfigLoader;
use RS\Csv\Preset\AbstractPreset;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

class OfferStock extends AbstractPreset
{
    protected static $warehouses;

    protected $link_preset_id;
    protected $link_id_field;
    protected $link_foreign_field;
    protected $link_offer_id_field;
    protected $array_field = 'stock_num';
    protected $ormObject;
    protected $offer_api;
    protected $warehouse_api;

    public function __construct($options)
    {
        $defaults = [
            'ormObject' => new Xstock(),
        ];

        $this->offer_api = new OfferApi();
        $this->warehouse_api = new WareHouseApi();
        parent::__construct($options + $defaults);
        $this->loadWarehouses(); //Загрузим склады
    }

    /**
     * Загружает склады
     *
     * @return void
     */
    public function loadWarehouses()
    {
        if (!isset(self::$warehouses)) {
            self::$warehouses = OrmRequest::make()
                ->from(new WareHouse())
                ->where([
                    'site_id' => SiteManager::getSiteId()
                ])
                ->objects(null, 'id');
        }
    }

    /**
     * Устанавливает ORM объект для работы
     *
     * @param \RS\Orm\AbstractObject $orm_object - ORM объект
     */
    public function setOrmObject(AbstractObject $orm_object)
    {
        $this->ormObject = $orm_object;
    }


    /**
     * Определяет foreign key другого объекта
     *
     * @param string $field
     * @return void
     */
    public function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
    }

    /**
     * Устанавливает номер пресета, к которому линкуется текущий пресет
     *
     * @param integer $n - номер пресета
     * @return void
     */
    public function setLinkPresetId($n)
    {
        $this->link_preset_id = $n;
    }

    /**
     * Определяет foreign key объекта комплектаций
     *
     * @param string $field
     * @return void
     */
    public function setLinkOfferIdField($field)
    {
        $this->link_offer_id_field = $field;
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = [];

        $config = ConfigLoader::byModule($this);
        if (!$config['inventory_control_enable'] || $this->schema->getAction() == 'export') {
            if (!empty(self::$warehouses)) {
                foreach (self::$warehouses as $warehouse_id => $warehouse) {
                    $columns[$this->id . '-offerstock_' . $warehouse_id] = [
                        'key' => 'offerprice_' . $warehouse_id,
                        'warehouse_id' => $warehouse_id,
                        'title' => t('Остаток по складу "') . $warehouse['title'] . '"'
                    ];
                }
            }
        }

        return $columns;
    }

    /**
     * Возвращает набор колонок с данными для одной строки
     *
     * @param mixed $n
     * @return array
     */
    public function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_offer_id_field];
        $values_array = [];

        if (isset($this->rows[$id])) {

            foreach ($this->rows[$id] as $offer_stock) {
                $values_array[$this->id . '-offerstock_' . $offer_stock['warehouse_id']] = $offer_stock['stock'];
            }
        }

        return $values_array;
    }

    /**
     * Импортирует данные одной строки текущего пресета в базу
     */
    public function importColumnsData()
    {
        if (isset($this->row)) {
            $config = ConfigLoader::byModule($this);
            $result_array = [];
            if ($config['csv_dont_delete_stocks']) {
                /** @var \Catalog\Model\CsvPreset\SimplePriceStockBase $base_preset */
                $base_preset = $this->schema->getPreset($this->link_preset_id);
                /** @var Offer $offer */
                if ($offer = $base_preset->loadObject()) {
                    $result_array = $offer->fillStockNum();
                }
            }

            foreach ($this->row as $column_title => $value) {
                $warehouse_info = explode("_", $column_title);
                $warehouse_id = $warehouse_info[1]; //id склада

                if ($value !== '') {
                    $result_array[$warehouse_id] = trim($value);
                }
            }
            if ($result_array) {
                $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $result_array;
            }
        }
    }

    /**
     * Загружает связанные данные
     *
     * @return void
     */
    public function loadData()
    {
        $this->row = [];
        if ($this->schema->ids) {
            $this->rows = OrmRequest::make()
                ->from($this->ormObject)
                ->whereIn($this->link_foreign_field, $this->schema->ids)
                ->objects(null, $this->link_foreign_field, true);
        }
    }
}
