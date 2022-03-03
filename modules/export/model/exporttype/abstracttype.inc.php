<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType;

use Catalog\Model\Api;
use Catalog\Model\Api as ProductApi;
use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Xcost;
use Catalog\Model\Orm\Xdir;
use Export\Model\Orm\ExportProfile;
use Catalog\Model\Orm\Product;
use RS\Event\Manager as EventManager;
use RS\Event\Result as EventResult;
use RS\Exception as RSException;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Storage\AbstractStorage;
use RS\Orm\Storage\Stub as StorageStub;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use RS\View\Engine;

/**
 * Абстрактный класс типа экспорта.
 */
abstract class AbstractType extends AbstractObject
{
    const CHARSET = 'utf-8';

    protected $offer_types;
    protected $offer_types_data;
    protected $cacheSelectedProductIds;
    private $export_profile;

    public function _init()
    {
        return $this->getPropertyIterator()->append([
            t('Основные'),
                'export_cost_id' => new Type\Integer([
                    'description' => t('Выгружаемый тип цен'),
                    'list' => [['\Catalog\Model\CostApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                    'default' => CostApi::getDefaultCostId()
                ]),
                'products' => new Type\ArrayList([
                    'description' => t('Список товаров'),
                    'template' => '%export%/form/profile/products.tpl'
                ]),
                'only_available' => new Type\Integer([
                    'description' => t('Выгружать только товары, которые в наличии?'),
                    'checkboxView' => [1, 0],
                ]),
                'consider_warehouses' => new Type\ArrayList([
                    'description' => t('Какие склады учитывать при определении наличия товара?'),
                    'list' => [['\Catalog\Model\WareHouseApi', 'staticSelectList'], [0 => t('- Все -')]],
                    'attr' => [[
                        'multiple' => true,
                        'size' => 10,
                    ]],
                ]),
                'min_cost' => new Type\Integer([
                    'description' => t('Выгружать товары с ценой из диапазона'),
                    'maxLength' => 11,
                    'template' => '%export%/form/profile/cost_range.tpl',
                    'hint' => t('Диапазон цен указывайте в базовой валюте.')
                ]),
                'max_cost' => new Type\Integer([
                    'description' => t('Максисальная цена.'),
                    'maxLength' => 11,
                    'visible' => false
                ]),
                'no_export_offers' => new Type\Integer([
                    'description' => t('Не выгружать комлектации товаров'),
                    'checkboxview' => [1, 0],
                ]),
                'export_photo_originals' => new Type\Integer([
                    'description' => t('Выгружать оригиналы фото (без водяного знака)'),
                    'checkboxview' => [1, 0],
                ]),
                t('Поля данных'),
                'offer_type' => new Type\Varchar([
                    'description' => t('Тип описания'),
                    'ListFromArray' => [$this->getOfferTypeNames()],
                ]),
                'fieldmap' => new Type\MixedType([
                    'description' => t(''),
                    'visible' => true,
                    'template' => '%export%/form/profile/fieldmap.tpl'
                ]),
        ]);
    }

    /**
     * Возвращает название расчетного модуля (типа экспорта)
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает описание типа экспорта. Возможен HTML
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
     *
     * @return string
     */
    abstract public function getShortName();

    /**
     * Возвращает экспортированные данные (XML, CSV, JSON и т.п.)
     *
     * @return string
     */
    abstract public function export();

    /**
     * Возвращает полный путь к файлу, содержащему экспортированные данные
     *
     * @return string
     * @throws RSException
     */
    function getCacheFilePath()
    {
        $profile = $this->getExportProfile();
        $cache_dir = \Setup::$ROOT . \Setup::$STORAGE_DIR . DS . 'export';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, \Setup::$CREATE_DIR_RIGHTS, true);
        }

        return $cache_dir . DS . 'site' . $profile['site_id'] . '_' . $profile['class'] . '_' . $profile['id'] . '.cache';
    }

    /**
     * Если для экспорта нужны какие-то специфические заголовки, то их нужно отправлять в этом методе
     */
    function sendHeaders()
    {
        header("Content-type: text/xml; charset=" . static::CHARSET);
    }

    /**
     * Возвращает объект хранилища
     *
     * @return AbstractStorage
     */
    protected function getStorageInstance()
    {
        return new StorageStub($this);
    }

    /**
     * Возвращает ссылку на файл экспорта
     *
     * @return string
     * @throws RSException
     */
    public function getExportUrl()
    {
        $profile = $this->getExportProfile();
        $router = RouterManager::obj();
        return $router->getUrl('export-front-gate', [
            'site_id' => SiteManager::getSiteId(),
            'export_id' => $profile['alias'] ? $profile['alias'] : $profile['id'],
            'export_type' => $profile['class'],
        ], true);
    }

    /**
     * Возвращает список классов типов описания
     *
     * @return \Export\Model\ExportType\AbstractOfferType[]
     */
    abstract protected function getOfferTypesClasses();

    /**
     * Возвращает массив доступных типов описания товарных предложений
     *
     * @return array
     */
    protected function getOfferTypes()
    {
        if ($this->offer_types === null) {
            $export_type_name = $this->getShortName();
            $offer_types = $this->getOfferTypesClasses();
            foreach ($offer_types as $offer_type) {
                $offer_type->setExportTypeName($export_type_name);
                $this->offer_types[$offer_type->getShortName()] = $offer_type;
            }
        }
        return $this->offer_types;
    }

    /**
     * Возвращает массив доступных типов описания товарных предложений
     * @return array
     */
    public function getOfferTypeNames()
    {
        $result = [];
        foreach ($this->getOfferTypes() as $offer_type) {
            $result[$offer_type->getShortName()] = $offer_type->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает массив данных по всем типам описания
     * @return array
     */
    public function getOfferTypesData()
    {
        if ($this->offer_types_data === null) {
            $this->offer_types_data = [];
            foreach ($this->getOfferTypes() as $offer_type) {
                $this->offer_types_data[$offer_type->getShortName()] = $offer_type->getEspecialTags();
            }
        }
        return $this->offer_types_data;
    }

    /**
     * Возвращает массив данных по всем типам описания в виде JSON
     * @return string
     */
    public function getOfferTypesJson()
    {
        return json_encode($this->getOfferTypesData());
    }

    /**
     * Возвращает массив соответсвия полей (fieldmap) в виде JSON
     * @return string
     */
    public function getFieldMapJson()
    {
        return json_encode($this['fieldmap']);
    }

    /**
     * Возвращает массив идентификаторов выбранных товаров
     *
     * @param ExportProfile $profile
     * @param bool $cache - использовать кеш
     * @return array
     */
    protected function getSelectedProductIds(ExportProfile $profile, $cache = true)
    {
        if ($cache && $this->cacheSelectedProductIds != null) {
            return $this->cacheSelectedProductIds;
        }

        $product_ids = isset($profile['data']['products']['product']) ? $profile['data']['products']['product'] : [];
        $group_ids = isset($profile['data']['products']['group']) ? $profile['data']['products']['group'] : [];

        if (!$product_ids && !$group_ids) {
            //Если не выбрана ни одна группа и ни один товар, это означает, 
            //что экспортировать нужно все товары во всех группах
            $group_ids = [0];
        }

        if (!empty($group_ids)) {
            // Получаем все дочерние группы
            while (true) {
                $subgroups_ids = OrmRequest::make()
                    ->select('id')
                    ->from(new Dir())
                    ->whereIn('parent', $group_ids)
                    ->where('(is_virtual = 0 OR is_virtual IS NULL)')
                    ->exec()
                    ->fetchSelected(null, 'id');
                $old_count = count($group_ids);
                $group_ids = array_unique(array_merge($group_ids, $subgroups_ids));
                if ($old_count == count($group_ids)) break;
            }
            // Получаем ID всех товаров в этих группах
            $ids = OrmRequest::make()
                ->select('X.product_id')
                ->from(new Xdir(), 'X')
                ->join(new Product(), 'P.id = X.product_id', 'P')
                ->whereIn('X.dir_id', $group_ids)
                ->where(['P.no_export' => 0])
                ->exec()
                ->fetchSelected(null, 'product_id');

            // Прибавляем их к "товарам выбранными по одному"
            $product_ids = array_unique(array_merge($product_ids, $ids));
        }
        $this->cacheSelectedProductIds = $product_ids;
        return $this->cacheSelectedProductIds;
    }

    /**
     * Возвращает объект запроса на выборку товаров с учетом опций профиля экспорта
     *
     * @param ExportProfile $profile
     * @return OrmRequest
     */
    protected function getProductsSelectionQuery(ExportProfile $profile)
    {
        $product_ids = $this->getSelectedProductIds($profile);
        $query = OrmRequest::make()
            ->from(new Product, 'P')
            ->where([
                'public' => 1,
                'site_id' => $profile['site_id']
            ]);

        if ($profile->only_available) {
            $query->where('P.num > 0');
        }

        if (!empty($product_ids)) {
            $query->whereIn('P.id', $product_ids);
        }

        // Добавляем ограничение по цене в базовой валюте, если оно указано
        if (!empty($profile['min_cost']) || !empty($profile['max_cost'])) {
            $cost_id = $profile['export_cost_id'] ?: CostApi::getDefaultCostId();

            $current_cost_type = CostApi::getInstance()->getManualType($cost_id);
            $query->join(new Xcost(), "P.id = C.product_id AND C.cost_id='{$current_cost_type}'", 'C');

            //Корректируем цены для фильтра, если цена пользователя - автоматическая
            $costapi = CostApi::getInstance();
            $currencyApi = new CurrencyApi();
            if (!empty($profile['min_cost'])) {
                $cost_from = $costapi->correctCost($profile['min_cost']);
                $cost_from = floor($currencyApi->convertToBase($cost_from));
                $query->where("C.cost_val>='#cost_from'", ['cost_from' => $cost_from]);
            }
            if (!empty($profile['max_cost'])) {
                $cost_to = $costapi->correctCost($profile['max_cost']);
                $cost_to = ceil($currencyApi->convertToBase($cost_to));
                $query->where("C.cost_val<='#cost_to'", ['cost_to' => $cost_to]);
            }
        }

        return $query;
    }

    /**
     * Экспорт Товарных предложений
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @throws \Exception
     */
    protected function exportOffers(ExportProfile $profile, \XMLWriter $writer)
    {
        $query = $this->getProductsSelectionQuery($profile);

        $offset = 0;
        $pageSize = 100;
        $catalogApi = new ProductApi();

        while ($products = $query->limit($offset, $pageSize)->objects()) {

            if ($profile['consider_warehouses'] && !in_array(0, $profile['consider_warehouses'])) {
                $catalogApi->setWarehousesForDynamicNum($profile['consider_warehouses']);
            }
            $products = $catalogApi->addProductsCost($products);
            $products = $catalogApi->addProductsOffers($products);
            $products = $catalogApi->addProductsDirs($products);
            $products = $catalogApi->addProductsProperty($products);
            $products = $catalogApi->addProductsPhotos($products);
            $products = $catalogApi->addProductsMultiOffers($products);

            foreach ($products as $product) {
                if ($product['offers']['use'] && count($product->getOffers()) > 1 && !$profile->no_export_offers) {
                    foreach ($product['offers']['items'] as $offer) {
                        if (!$profile->only_available || $product->getNum($offer['id']) > 0) {
                            $this->exportOneOffer($profile, $writer, $product, $offer['id']);
                        }
                    }
                } else {
                    if (!$profile->only_available || $product->getNum() > 0) {
                        $this->exportOneOffer($profile, $writer, $product, false);
                    }
                }

            }
            $offset += $pageSize;
        }

        $writer->flush();
    }

    /**
     * Экпорт одного товарного предложения
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param mixed $product
     * @param mixed $offer_index
     * @throws RSException
     */
    protected function exportOneOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        if ($profile['only_available'] && $product->getNum($offer_index) <= 0) {
            return;
        }

        if ($offer_index !== false && !count($product['offers'])) {
            throw new RSException(t('Товарные предложения отсутсвуют, но передан аргумент offer_index'));
        }

        $this->offer_types[$profile['data']['offer_type']]->writeOffer($profile, $writer, $product, $offer_index);
        $writer->flush();
    }

    /**
     * Событие, которое вызывается после записи всех твоаров
     *
     * @param string $event_name - уникальная часть итогового имени события
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @return EventResult
     */
    protected function fireAfterAllOffersEvent($event_name, ExportProfile $profile, \XMLWriter $writer)
    {
        $event_name = "export.{$profile['class']}.$event_name";
        $export_params = [
            'profile' => $profile,
            'writer' => $writer,
        ];

        return EventManager::fire($event_name, $export_params);
    }

    /**
     * Устанавливает объект родительского профиля экспорта
     *
     * @param ExportProfile $profile
     * @return self
     */
    public function setExportProfile(ExportProfile $profile)
    {
        $this->export_profile = $profile;
        return $this;
    }

    /**
     * Возвращает объект родительского профиля экспорта
     *
     * @return ExportProfile
     * @throws RSException
     */
    public function getExportProfile()
    {
        if (!$this->export_profile) {
            throw new RSException(t('Не установлен профиль экспорта'));
        }
        return $this->export_profile;
    }

    /**
     * Возвращает true, если профиль экспорта может обмениваться по API
     *
     * @return bool
     */
    public function canExchangeByApi()
    {
        return ($this instanceof ExchangableInterface);
    }

    /**
     * Возвращает путь к шаблону для рендеринга ячейки с действиями над профилем
     *
     * @return string
     */
    public function getExportActionCellTemplate()
    {
        return '%export%/action_cell.tpl';
    }

    /**
     * Возвращает готовый HTML для ячейки с ссылкой на обмен таблицы
     *
     * @param ExportProfile $export
     * @return string
     * @throws \SmartyException
     */
    public function getExportActionCell(ExportProfile $export)
    {
        $view = new Engine();
        $view->assign([
            'export_profile' => $export,
            'export_type' => $this
        ]);

        return $view->fetch($this->getExportActionCellTemplate());
    }
}
