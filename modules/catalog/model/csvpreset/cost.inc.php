<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm;
use RS\Config\Loader as ConfigLoader;
use RS\Csv\Preset\AbstractPreset;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;

/**
 * Набор колонок описывающих связь товара с ценами
 */
class Cost extends AbstractPreset
{
    protected static $type_cost = [];
    protected static $type_cost_by_title = [];
    protected static $default_currency = null; //Валюта по умолчанию
    protected static $currencies = [];
    protected static $currencies_by_title = [];

    protected $delimiter = ';';
    protected $id_field;
    protected $link_preset_id;
    protected $link_id_field;
    protected $manylink_orm;
    protected $orm_object;

    public $array_field = 'excost';
    public $manylink_foreign_id_field = 'cost_id';
    public $manylink_id_field = 'product_id';

    function __construct($options)
    {
        $this->manylink_orm = new Orm\Xcost();
        $this->orm_object = new Orm\Typecost();
        $this->id_field = 'id';
        $this->link_id_field = 'id';
        parent::__construct($options);

        $this->link_preset_id = 0;
        $this->loadCurrencies();
    }

    /**
     * Подгрузка сведений о валютах и ценах присутствующих в системе
     *
     */
    function loadCurrencies()
    {
        $api = new CurrencyApi();
        $api->setOrder('`default` DESC');
        $list = $api->getList();
        foreach ($list as $cost) {
            self::$currencies[$cost['id']] = $cost['title'];
        }
        self::$currencies_by_title = array_flip(self::$currencies);
        //Валюта по умолчанию
        self::$default_currency = current(self::$currencies);

        $type_api = new CostApi();
        $list = $type_api->getList();
        foreach ($list as $typecost) {
            self::$type_cost[$typecost['id']] = $typecost['title'];
        }
        self::$type_cost_by_title = array_flip(self::$type_cost);
    }

    /**
     * Устанавливает объект, связанный с данным набором колонок
     *
     * @param mixed $orm_object
     */
    function setOrmObject(AbstractObject $orm_object)
    {
        $this->orm_object = $orm_object;
    }

    /**
     * Загружает связанные данные
     *
     * @return void
     */
    function loadData()
    {
        $ids = [];
        foreach ($this->schema->rows as $row) {
            $ids[] = $row[$this->link_id_field];
        }
        $this->row = [];
        if ($ids) {
            $this->row = OrmRequest::make()
                ->from($this->manylink_orm, 'X')
                ->whereIn($this->manylink_id_field, $ids)
                ->objects(null, $this->manylink_id_field, true);
        }
    }


    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_id_field];

        $values_array = [];
        if (isset($this->row[$id])) {
            foreach ($this->row[$id] as $n => $item) {
                $currency = isset(self::$currencies[$item['cost_original_currency']]) ? ' ' . self::$currencies[$item['cost_original_currency']] : '';
                $values_array[$this->id . '-costlistname_' . $item['cost_id']] = str_replace(".", ",", $item['cost_original_val']);
                $values_array[$this->id . '-costlistcurrency_' . $item['cost_id']] = $currency;
            }
        }
        return $values_array;
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns()
    {
        $columns = [];
        if (!empty(self::$type_cost)) {
            foreach (self::$type_cost as $cost_id => $cost_title) {
                $columns[$this->id . '-costlistname_' . $cost_id] = [
                    'key' => 'costname_' . $cost_id,
                    'title' => t('Цена') . '_' . $cost_title
                ];
                $columns[$this->id . '-costlistcurrency_' . $cost_id] = [
                    'key' => 'costlistcurrency_' . $cost_id,
                    'title' => t('Цена') . '_' . $cost_title . '_' . t('Валюта')
                ];
            }
        }

        return $columns;
    }

    /**
     * Импортирует одну строку данных
     *
     * @return void
     */
    function importColumnsData()
    {
        if (isset($this->row)) {
            $config = ConfigLoader::byModule($this);
            $excost = [];
            if ($config['csv_dont_delete_costs']) {
                /** @var \Catalog\Model\CsvPreset\SimplePriceStockBase $base_preset */
                $base_preset = $this->schema->getPreset($this->link_preset_id);
                /** @var Orm\Product $product */
                if ($product = $base_preset->loadObject()) {
                    $product->fillCost();
                    $excost = $product['excost'];
                }
            }

            foreach ($this->row as $key_info => $item) {
                $item = trim($item);                  //Значение ячейки
                $key_info = explode("_", trim($key_info)); //Получим информацию из поля
                $cost_id = $key_info[1];

                switch ($key_info[0]) {  //Пройдёмся по типу поля
                    case "costname": //Название цены
                        $value = str_replace([",", " "], [".", ""], $item);
                        $excost[$cost_id]['cost_original_val'] = $value;
                        break;
                    case "costlistcurrency": //Валюта цены
                        if (!isset(self::$currencies_by_title[$item])) { //Если валюты такой нет
                            $currency_id = 0;
                        } else {  //Если есть такая валюта
                            $currency_id = self::$currencies_by_title[$item];
                        }
                        $excost[$cost_id]['cost_original_currency'] = $currency_id;
                        break;
                }
            }

            $product_excost = false;
            if (isset($this->schema->getPreset($this->link_preset_id)->row[$this->array_field])) {
                $product_excost = $this->schema->getPreset($this->link_preset_id)->row[$this->array_field];
                foreach ($excost as $cost_id => $info) {
                    $product_excost[$cost_id] = $info;
                }
            }

            //Проверим заданы ли значения валют, если нет то берём ту что поумолчанию
            foreach ($excost as $k => $excost_row) {
                if (isset($excost_row['cost_original_val']) && !isset($excost_row['cost_original_currency'])) {
                    $default_currency = CurrencyApi::getDefaultCurrency();
                    $excost[$k]['cost_original_currency'] = $default_currency['id'];
                }
            }

            $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $product_excost ? $product_excost : $excost;
        }
    }
}
