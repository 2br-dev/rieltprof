<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvSchema;

use Catalog\Model\CsvPreset\SimplePriceStockBase;
use RS\Csv\AbstractSchema;
use Catalog\Model\Api as ProductApi;
use Catalog\Model\CsvPreset as CatalogPreset;
use Catalog\Model\Orm;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Схема импорта/экспорта в CSV файл для обновления цен и остатков
 */
class SimplePriceStockUpdate extends AbstractSchema
{
    protected $reset_base_query = true;

    public function __construct()
    {
        $request = OrmRequest::make()->from(new Orm\Offer())->where('product_id > 0');

        parent::__construct(
            new SimplePriceStockBase([
                'idField' => 'title',
                'ormObject' => new Orm\Offer(),
                'ormProductObject' => new Orm\Product(),
                'ormUnsetFields' => ['pricedata', 'title'],
                'fields' => [
                    'title', 'barcode'
                ],
                'multisite' => true,
                'selectOrder' => 'product_id ASC, sortn ASC',
                'selectRequest' => $request,
                'searchFields' => ['barcode'],
                'beforeRowImport' => function ($_this) { // Игнорируем если не указан артикул
                    return !empty($_this->row['barcode']);
                },
                'afterRowImport' => [$this, 'afterBaseRowImport']
            ]), [
            new CatalogPreset\StockCost([
                'linkPresetId' => 0,
                'linkIdField' => 'id',
                'sortnField' => 'sortn',
                'linkIdFieldProduct' => 'product_id',
                'arrayProductField' => 'excost', //Поле для обновления цены в основной комплектации в товаре
                'arrayOfferField' => 'pricedata_arr', //Поле для обновления цены всех комплектаций, кроме нулевой
            ]),
            new CatalogPreset\OfferStock([
                'linkPresetId' => 0,
                'linkOfferIdField' => 'id',
                'linkForeignField' => 'offer_id'
            ])
        ],
            [
                'afterImport' => [$this, 'afterImport'],
                'fieldScope' => [
                    'title' => self::FIELDSCOPE_EXPORT
                ]
            ]
        );
    }

    /**
     * Возвращает запрос для базовой выборки комплектаций (Экспорт)
     *
     * @return \RS\Orm\Request
     */
    public function getBaseQuery()
    {
        if (!$this->query) {
            $this->query = $this->base_preset->getSelectRequest();
        }

        //Если есть запрос с выборкой в сессии
        if ($this->reset_base_query && $savedRequest = ProductApi::getSavedRequest('Catalog\Controller\Admin\Ctrl_list')) {
            /**
             * @var \RS\ORM\Request
             */
            $q = clone $savedRequest;
            $q->select = "OFFERS.*, A.title as product_title, A.barcode as product_barcode";
            $q->limit(null)
                ->orderby('OFFERS.product_id ASC, OFFERS.sortn ASC')
                ->leftjoin(new Orm\Offer(), 'OFFERS.product_id = A.id', 'OFFERS')
                ->where('OFFERS.product_id>0')
                ->setReturnClass(new Orm\Offer());

            $this->query = $q;
        }

        return $this->query;
    }

    /**
     * Устанавливает флаг сброса базового запроса
     *
     * @param bool $value
     */
    public function setResetBaseQuery($value)
    {
        $this->reset_base_query = $value;
    }

    /**
     * Обработчик, выполняющийся после импорта набора (которые уложились по
     * времени в 1 шаг) данных
     *
     * @param Catalog\Model\CsvSchema\Offer
     * @throws \RS\Db\Exception
     */
    public function afterImport()
    {
        //Производим пересчет общих остатков у товаров
        $offer = new Orm\Offer();
        OrmRequest::make()
            ->update()
            ->from(new Orm\Product(), 'P')
            ->set("P.num = (SELECT SUM(num) FROM {$offer->_getTable()} O WHERE O.product_id = P.id)")
            ->exec();
    }

    /**
     * Выполняется после импорта одной строки у основного пресета
     *
     * @param SimplePriceStockBase $preset - текущий пресет
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    public function afterBaseRowImport($preset)
    {
        /** @var Orm\Product $product */
        //Подгрузим объект комплектации, чтобы иметь полные сведения
        $offer = $preset->loadObject();

        if ($offer) {
            //Обновляем сведения о ценах основной комплектации у товара
            if ($preset->row['barcode'] && $offer && !$offer['sortn'] && isset($preset->row['excost'])) {
                $product = new Orm\Product($offer['product_id']);
                $product->fillCost(); //Подгрузим цены

                //Добавим к существующим ценам свою или перезапишем
                $product_excost = $product['excost'];
                foreach ($preset->row['excost'] as $cost_id => $info) {
                    $product_excost[$cost_id] = $info;
                }
                $product['excost'] = $product_excost;
                $product->setFlag(Orm\Product::FLAG_DONT_UPDATE_SEARCH_INDEX);
                $product->setFlag(Orm\Product::FLAG_DONT_UPDATE_DIR_COUNTER);
                $product->update();
            }
        } else { //Если комплектации нет найденой, то попробуем найти товар напрямую
            $product = OrmRequest::make()
                ->from(new Orm\Product())
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'barcode' => $preset->row['barcode'],
                ])->object();
            if ($product) {
                $product->fillCost(); //Подгрузим цены

                //Добавим к существующим ценам свою или перезапишем
                $product_excost = $product['excost'];
                if (isset($preset->row['excost'])) {
                    foreach ($preset->row['excost'] as $cost_id => $info) {
                        $product_excost[$cost_id] = $info;
                    }
                }
                $product['excost'] = $product_excost;
                $product->setFlag(Orm\Product::FLAG_DONT_UPDATE_SEARCH_INDEX);
                $product->setFlag(Orm\Product::FLAG_DONT_UPDATE_DIR_COUNTER);
                $product->update();
            }
        }
    }
}
