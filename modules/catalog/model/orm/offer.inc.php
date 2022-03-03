<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm;

use Catalog\Model\CurrencyApi;
use Catalog\Model\WareHouseApi;
use Photo\Model\Orm\Image;
use RS\Config\Loader as ConfigLoader;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;

/**
 * Комплектация товара. (или товарное предложение)
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $product_id ID товара
 * @property string $title Название
 * @property string $barcode Артикул
 * @property double $weight Вес
 * @property string $pricedata Цена (сериализован)
 * @property array $pricedata_arr Цена
 * @property string $propsdata Характеристики комплектации (сериализован)
 * @property array $propsdata_arr Характеристики комплектации
 * @property array $_propsdata Характеристики комплектации
 * @property float $num Остаток на складе
 * @property float $waiting Ожидание
 * @property float $reserve Зарезервировано
 * @property float $remains Остаток
 * @property string $photos Фотографии комплектаций
 * @property array $photos_arr Связанные фото
 * @property integer $sortn Порядковый номер
 * @property integer $unit Единица измерения
 * @property string $position 
 * @property integer $processed Флаг обработанной во время импорта комплектации
 * @property string $xml_id Идентификатор товара в системе 1C
 * @property string $import_hash Хэш данных импорта
 * @property string $sku Штрихкод
 * --\--
 */
class Offer extends OrmObject
{
    protected static
        $table = "product_offer";

    public
        $first_sortn = 0; //Сортировочный индекс, который следует присваивать первой добавляемой комплектации

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'product_id' => new Type\Integer([
                'description' => t('ID товара'),
                'index' => true,
                'visible' => false,
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название'),
                'maxLength' => 300,
                'mainVisible' => false,
            ]),
            'barcode' => new Type\Varchar([
                'description' => t('Артикул'),
                'maxLength' => 50,
                'index' => true,
                'allowEmpty' => false,
                'mainVisible' => false,
            ]),
            'weight' => new Type\Real([
                'description' => t('Вес'),
                'default' => 0
            ]),
            'pricedata' => new Type\TinyText([
                'description' => t('Цена (сериализован)'),
                'visible' => false,
            ]),
            'pricedata_arr' => new Type\ArrayList([
                'description' => t('Цена'),
                'template' => '%catalog%/form/offer/price_data.tpl',
                'mainVisible' => false,
            ]),
            'propsdata' => new Type\TinyText([
                'description' => t('Характеристики комплектации (сериализован)'),
                'visible' => false,
            ]),
            'propsdata_arr' => new Type\ArrayList([
                'description' => t('Характеристики комплектации'),
                'visible' => false,
            ]),
            '_propsdata' => new Type\ArrayList([
                'description' => t('Характеристики комплектации'),
                'template' => '%catalog%/form/offer/props_data.tpl',
                'mainVisible' => false,
            ]),
            'num' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'default' => 0,
                'description' => t('Остаток на складе'),
                'visible' => false,
            ]),
            'waiting' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'visible' => false,
                'appVisible' => true,
                'mevisible' => true,
                'description' => t('Ожидание'),
            ]),
            'reserve' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'visible' => false,
                'appVisible' => true,
                'mevisible' => true,
                'description' => t('Зарезервировано'),
            ]),
            'remains' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'visible' => false,
                'appVisible' => true,
                'mevisible' => true,
                'description' => t('Остаток'),
            ]),
            'stock_num' => new Type\MixedType([
                'description' => t('Остатки на складах'),
                'template' => '%catalog%/form/offer/stock_num.tpl',
                'visible' => true,
                'mainVisible' => false,
                'getWarehousesList' => function () {
                    return WareHouseApi::getWarehousesList();
                },
            ]),
            'photos' => new Type\Varchar([
                'maxLength' => 1000,
                'description' => t('Фотографии комплектаций'),
                'visible' => false,
            ]),
            'photos_arr' => new Type\ArrayList([
                'description' => t('Связанные фото'),
                'template' => '%catalog%/form/offer/photos.tpl',
                'mainVisible' => false,
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядковый номер'),
                'visible' => false,
            ]),
            'unit' => new Type\Integer([
                'description' => t('Единица измерения'),
                'default' => 0,
                'List' => [['\Catalog\Model\UnitApi', 'selectList']],
            ]),
            'position' => new Type\Varchar([
                'description' => t(''),
                'runtime' => true,
                'template' => '%catalog%/form/offer/position.tpl',
                'visible' => false
            ]),
            'processed' => new Type\Integer([
                'description' => t('Флаг обработанной во время импорта комплектации'),
                'maxLength' => '2',
                'visible' => false,
            ]),
            'xml_id' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Идентификатор товара в системе 1C'),
                'visible' => false,
            ]),
            'import_hash' => new Type\Varchar([
                'maxLength' => '32',
                'description' => t('Хэш данных импорта'),
                'visible' => false
            ]),
            'sku' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Штрихкод'),
                'mainVisible' => false,
            ]),
        ]);

        $this->addIndex(['site_id', 'xml_id'], self::INDEX_UNIQUE);
    }

    /**
     * Вызывается после загрузки объекта
     * @return void
     */
    function afterObjectLoad()
    {
        // Развернем фото комплектаций и Если photos_arr не задан
        if (!empty($this['photos']) && !$this->isModified('photos_arr')) {
            $photos = @unserialize($this['photos']);
            if (is_array($photos) && count($photos) == 1 && !isset($photos[0])) {
                $photos = [];
            }
            $this['photos_arr'] = $photos ? (array)$photos : [];
        }

        // Развернем данные по ценам
        if (!empty($this['pricedata'])) {
            $this['pricedata_arr'] = @unserialize($this['pricedata']) ?: [];
        } else {
            if (empty($this['pricedata_arr'])) {
                $this['pricedata_arr'] = [];
            }
        }

        //Развернем данные по характеристикам
        if (!empty($this['propsdata'])) {
            $this['propsdata_arr'] = @unserialize($this['propsdata']) ?: [];
        }

        //Если это нулевая комплектация, то записываем в аркинул null, чтобы брался артикул товара
        if ($this['sortn'] === "0") {
            $this['barcode'] = null;
        }

        // Приведение типов
        $this['num'] = (float)$this['num'];
    }

    /**
     * Функция срабатывает перед записью
     *
     * @param string $flag - строка означающая тип действия insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if (!$this->dont_reset_hash) {
            $this['import_hash'] = null; // при любом изменении - сбрасываем хэш
        }
        if ($this['xml_id'] == '') unset($this['xml_id']);

        //Поле "photos_named_arr" - виртуальное, используется для импорта CSV, при указании у комплектаций фотографий привязанных к ней.
        if (isset($this['photos_named_arr']) && count($this['photos_named_arr'])) {
            $arr = [];
            foreach ($this['photos_named_arr'] as $filename) { //Переберём и найдём истенные id-шники

                $photo = OrmRequest::make()
                    ->from(new Image())
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'type' => 'catalog',
                        'filename' => trim($filename),
                        'linkid' => $this['product_id'],
                    ])
                    ->object();
                if ($photo) {
                    $arr[] = $photo['id'];
                }

            }
            $this['photos_arr'] = $arr;
        }

        //Преобразуем свойства из виртуального свойства _propsdata
        if ($this->isModified('_propsdata')) {
            $this['propsdata_arr'] = $this->convertPropsData($this['_propsdata']);
        }

        //Сериализуем необходимые поля
        if ($this->isModified('photos_arr')) {
            $this['photos'] = serialize($this['photos_arr']);
        }

        //Если value не установлено - установим 0                                                   
        $this_pricedata_arr = $this['pricedata_arr'];
        if (isset($this_pricedata_arr['oneprice'])) {
            if (!isset($this_pricedata_arr['oneprice']['value'])) {
                $this_pricedata_arr['oneprice']['value'] = 0;
            }
        }
        if (isset($this_pricedata_arr['price'])) {
            foreach ($this_pricedata_arr['price'] as &$price) {
                if (!isset($price['value'])) {
                    $price['value'] = 0;
                }
            }
        }
        $this['pricedata_arr'] = $this_pricedata_arr;
        if ($this['pricedata_arr'] == null) {
            $this['pricedata_arr'] = [];
        }
        if ($this->isModified('pricedata_arr')) {
            $pricedata_arr = $this['pricedata_arr'];
            if (empty($pricedata_arr['oneprice']['use'])) {
                //Удаляем секцию oneprice, если цены заданы индивидуально
                unset($pricedata_arr['oneprice']);
                $this['pricedata_arr'] = $pricedata_arr;
            }
            $this['pricedata_arr'] = $this->convertValues($this['pricedata_arr']);
            $this['pricedata'] = serialize($this['pricedata_arr'] ?: []);
        }
        if ($this->isModified('propsdata_arr')) {
            $this['propsdata'] = serialize($this['propsdata_arr'] ?: []);
        }

        //Обновим сортировку у вновь созданной комплектации
        if ($flag != self::UPDATE_FLAG && !$this->isModified('sortn')) {
            $q = OrmRequest::make()
                ->select('MAX(sortn)+1 as next_sort')
                ->from($this)
                ->where([
                    'product_id' => (int)$this['product_id']
                ]);

            if ($this['xml_id']) { //Если это подкомплектация
                if (mb_strpos($this['xml_id'], "#") !== false) { //Если это подкомплектация
                    $this['sortn'] = $q->exec()->getOneField('next_sort', ($this->cml_207_no_offer_params !== null) ? 0 : 1);
                } else { //Если основная комплектация
                    $this['sortn'] = 0;
                }
            } else {
                $this['sortn'] = $q->exec()->getOneField('next_sort', $this->first_sortn);
            }

        }

        //Обновим общий остаток комплектации
        if ($this->isModified('stock_num')) {
            $cnt = 0;
            foreach ($this['stock_num'] as $warehouse_id => $stock_num) {
                $cnt += (float)$stock_num;
            }
            $this['num'] = $cnt;
        }
    }

    /**
     * Возвращает true, если это главная комплектация
     *
     * @return bool
     */
    function isMain()
    {
        return $this['sortn'] == 0;
    }

    /**
     * Функция срабатывает после записи комплектации
     *
     * @param string $flag - строка означающая тип действия insert или update
     */
    function afterWrite($flag)
    {
        //Обновим общий остаток комплектации
        if ($this->isModified('stock_num')) {
            //Очистим остатки по складам
            OrmRequest::make()
                ->delete()
                ->from(new Xstock())
                ->where([
                    'offer_id' => $this['id'],
                    'product_id' => $this['product_id'],
                ])
                ->exec();

            foreach ($this['stock_num'] as $warehouse_id => $stock_num) {
                //Добавим остатки по складам  
                $offer_stock = new Xstock();
                $offer_stock['product_id'] = $this['product_id'];
                $offer_stock['offer_id'] = $this['id'];
                $offer_stock['warehouse_id'] = $warehouse_id;
                $offer_stock['stock'] = $stock_num;
                $offer_stock->insert(false, ['stock'], ['product_id', 'offer_id', 'warehouse_id']);
            }
        }
    }

    /**
     * Конвертирует формат сведений о характеристиках комплектации
     *
     * @param array $_propsdata ['key' => [ключ1, ключ2,...],  'value' => [значение1, значение2, ...]]
     * @return array ['ключ1' => 'значение1', 'ключ2' => 'значение2',...]
     */
    function convertPropsData($_propsdata)
    {
        $props_data_arr = [];
        if (!empty($_propsdata)) {
            foreach ($_propsdata['key'] as $n => $val) {
                if ($val !== '') {
                    $props_data_arr[$val] = $_propsdata['val'][$n];
                }
            }
        }
        return $props_data_arr;
    }

    /**
     * Конвертирует валюты в комплектациях
     *
     * @param array $pricedata секция pricedata из offers
     * @return array возвращает тот же с массив, только с добавленной секцией value
     */
    function convertValues(array $pricedata)
    {
        if (!$pricedata) return $pricedata;

        if (!empty($pricedata['oneprice'])) {
            //Задана одна цена на все типы цен
            $pricedata['oneprice']['value'] = @$pricedata['oneprice']['original_value'];
            if (isset($pricedata['oneprice']['unit'])) {
                if ($pricedata['oneprice']['unit'] != '%') {
                    $source_curr = Currency::loadSingle($pricedata['oneprice']['unit']);
                    if ($source_curr['id']) {
                        $pricedata['oneprice']['value'] = CurrencyApi::convertToBase($pricedata['oneprice']['original_value'], $source_curr);
                    }
                }
            }
        } else {
            //Для каждой цены задано персональное значение
            if (!empty($pricedata['price'])) {
                foreach ($pricedata['price'] as $cost_id => &$data) {
                    $data['value'] = @$data['original_value'];
                    if (isset($data['unit'])) {
                        if ($data['unit'] != '%') {
                            $source_curr = Currency::loadSingle($data['unit']);
                            if ($source_curr['id']) {
                                $data['value'] = CurrencyApi::convertToBase($data['value'], $source_curr);
                            }
                        }
                    }
                }
            }
        }
        return $pricedata;
    }

    /**
     * Загружает остатки по складам для комплектации
     *
     * @return float[]
     */
    function fillStockNum()
    {
        if ($this['product_id']) {
            $this['stock_num'] = OrmRequest::make()
                ->from(new Xstock)
                ->where([
                    'product_id' => $this['product_id'],
                    'offer_id' => $this['id']
                ])->exec()->fetchSelected('warehouse_id', 'stock');
        } else {
            //Не загружаем остатки для новых товаров
            $this['stock_num'] = [];
        }

        return $this['stock_num'];
    }

    function getStocks()
    {
        if ($this['product_id'] && $this['id']) {
            $stocks = OrmRequest::make()
                ->from(new Xstock)
                ->where([
                    'product_id' => $this['product_id'],
                    'offer_id' => $this['id']
                ])->exec()->fetchSelected('warehouse_id');
        } else {
            $stocks = [];
        }

        return $stocks;
    }

    /**
     * Возвращает JSON с параметрами комплектаций
     *
     * @return string
     */
    function getPropertiesJson()
    {
        $result = [];
        if (is_array($this['propsdata_arr'])) {
            foreach ($this['propsdata_arr'] as $key => $value) {
                $result[] = [$key, $value];
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает JSON с остатками на складах для данной комплектации
     * @return array
     */
    function getStickJson()
    {
        return json_encode($this['sticks'] ?: []);
    }

    /**
     * Получает массив из ID фото комплектации
     *
     * @return string
     */
    function getPhotosJson()
    {
        return json_encode((array)$this['photos_arr']);
    }

    /**
     * Получает id главной(первой отмеченной) фотографии у товара или false
     *
     */
    function getMainPhotoId()
    {
        if (!empty($this['photos_arr'])) {
            return $this['photos_arr'][0];
        }
        return false;
    }

    /**
     * Получает ID фото комплектации через разделитель
     *
     * @param string $glue - символ склейки
     * @return string
     */
    function getImplodePhotos($glue = ',')
    {
        return implode($glue, (array)$this['photos_arr']);
    }

    /**
     * Возвращает объект единицы измерения, в котором измеряется данный продукт
     *
     * @param string $property - имя свойства объекта Unit. Используется для быстрого обращения
     * @return Unit
     */
    function getUnit($property = null)
    {
        $unit_id = $this['unit'] ?: ConfigLoader::byModule($this)->default_unit;
        $unit = new Unit($unit_id);
        return ($property === null) ? $unit : $unit[$property];
    }

    /**
     * Возвращает объект товара, которому принадлежит комплектация
     *
     * @return Product
     */
    function getProduct()
    {
        return new Product($this['product_id']);
    }

    /**
     * Возвращает следующий по порядку артикул для комплектации
     *
     * @param string $prefix
     * @return string
     */
    function setNextBarcode($prefix = '')
    {
        $next = OrmRequest::make()
            ->from($this)
            ->where([
                'product_id' => $this['product_id']
            ])
            ->count() + 1;

        $this['barcode'] = $prefix . $next;
        return $this['barcode'];
    }

    /**
     * Удаляет комплектацию
     *
     * @return bool
     */
    function delete()
    {
        if ($result = parent::delete()) {
            Request::make()
                ->delete()
                ->from(Xstock::_getTable())
                ->where([
                    'offer_id' => $this['id']
                ])->exec();
        }

        return $result;
    }
}
