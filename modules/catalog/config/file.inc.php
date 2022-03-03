<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Config;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\CsvPreset\Files;
use Catalog\Model\Dirapi;
use Catalog\Model\Inventory\InventoryTools;
use Catalog\Model\OfferApi;
use Catalog\Model\ProductDimensions;
use Files\Model\FilesType\CatalogProduct;
use RS\Config\UserFieldsManager;
use RS\Exception as RSException;
use RS\Module\Exception as ModuleException;
use RS\Module\Manager as ModuleManager;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
* @defgroup Catalog Catalog(Каталог товаров)
* Модуль предоставляет возможность управляь списками товаров, валют, характеристик, единиц измерения, типов цен.
*/

/**
 * Конфигурационный файл модуля Каталог товаров
 */
class File extends ConfigObject
{
    const
        ACTION_NOTHING      = "nothing",
        ACTION_CLEAR_STOCKS = "clear_stocks",
        ACTION_DEACTIVATE   = "deactivate",
        ACTION_REMOVE       = "remove";

    protected $before_state;

    /**
     * @return void
     * @throws RSException
     */
    public function _init()
    {
        $property_iterator  = parent::_init()->append([
            'default_cost' => new Type\Integer([
                'description' => t('Цена по умолчанию'),
                'list' => [['\Catalog\Model\Costapi', 'staticSelectList']]
            ]),
            'old_cost' => new Type\Integer([
                'description' => t('Старая(зачеркнутая) цена'),
                'list' => [['\Catalog\Model\CostApi', 'staticSelectList'], [0 => t('- Не выбрано -')]],
            ]),
            'hide_unobtainable_goods' => new Type\Varchar([
                'description' => t('Скрывать товары с нулевым остатком'),
                'listfromarray' => [[
                    'Y' => t('Да'),
                    'N' => t('Нет')]
                ],
                'attr' => [['size' => 1]]
            ]),
            'list_page_size' => new Type\Integer([
                'description' => t('Количество товаров на одной странице списка')
            ]),
            'items_on_page' => new Type\Varchar([
                'description' => t('Количество товаров на странице категории. Укажите через запятую, если нужно предоставить выбор'),
                'hint' => t('Например: 12,48,96')
            ]),
            'list_default_order' => new Type\Varchar([
                'description' => t('Сортировка по умолчанию на странице со списком товаров'),
                'hint' => t('Удалите куки сайта, если Вы поменяли данный параметр, чтобы увидеть результат'),
                'listFromArray' => [[
                    'sortn' => t('По сортировочному весу'),
                    'dateof' => t('По дате'),
                    'rating' => t('По рейтингу'),
                    'cost' => t('По цене'),
                    'title' => t('По наименованию товара'),
                    'num' => t('По наличию'),
                    'barcode' => t('По артикулу')
                ]]
            ]),
            'list_default_order_direction' => new Type\Varchar([
                'description' => t('Направление сортировки по умолчанию на странице со списком товаров'),
                'hint' => t('Удалите куки сайта, если Вы поменяли данный параметр, чтобы увидеть результат'),
                'listFromArray' => [[
                    'desc' => t('По убыванию'),
                    'asc' => t('По возрастанию')
                ]]
            ]),
            'list_order_instok_first' => new Type\Integer([
                'description' => t('Отображать в начале товары в наличии'),
                'checkboxView' => [1, 0],
                'hint' => t('Опция не будет работать в случае, если включена опция `Ограничить остатки товара только остатками на складах выбранного филиала`'),
                'default' => 0
            ]),
            'list_default_view_as' => new Type\Varchar([
                'description' => t('Отображать по умолчанию товары в каталоге'),
                'listFromArray' => [[
                    'blocks' => t('В виде блоков'),
                    'table' => t('В виде таблицы')
                ]]
            ]),
            'default_unit' => new Type\Integer([
                'description' => t('Единица измерения по-умолчанию'),
                'default' => t('грамм'),
                'List' => [['\Catalog\Model\UnitApi', 'selectList']]
            ]),
            'concat_dir_meta' => new Type\Integer([
                'description' => t('Дописывать мета теги категорий к товару'),
                'hint' => t('Данная опция имеет значение, когда мета данные не заданы у товара.'),
                'checkboxView' => [1, 0]
            ]),
            'auto_barcode' => new Type\Varchar([
                'description' => t('Синтаксис для автогенерации артикула в новом товаре'),
                'maxLength'   => 60,
                'hint' => t('n - след. номер товара<br/>цифра - количество цифр')
            ]),
            'disable_search_index' => new Type\Varchar([
                'description' => t('Отключить добавление товаров ко внутреннему поисковому индексу'),
                'checkboxview' => [1, 0],
                'hint' => t('Данный флаг следует устанавливать только при использовании сторонних поисковых сервисов на сайте')
            ]),
            'price_round' => new Type\Decimal([
                'description' => t('Округлять цены при внутренних пересчётах до'),
                'hint' => t('Дробная часть указывается через точку<br/>
                            Округление происходит <b>в большую сторону</b>,<br/>
                            результат округления кратен значению:<br/>
                            <b>1</b> - округлять до целых (13,5678 = 14)<br/>
                            <b>0.1</b> - до десятых (13,5678 = 13,6)<br/>
                            <b>10</b> - до десятков (13,5678 = 20)<br/>
                            <b>5</b> - до кратного пяти (13,5678 = 15).<br/><br/>
                            После изменения данной настройки небходимо "пересчитать цены" (пересохранить любую валюту)<br/><br/>
                            Округление используется при:<br>
                            - Мультиредактировании цен у комплектаций, если валюта рядом с ценой не соответствует валюте по умолчанию;<br/>
                            - Мультиредактировании цен у товаров по формуле;<br/>
                            - При пересчете курсов валют.
                            - Определении цены комплектации<br/>
                ', [], 'Описание поля `Округлять цены при внутренних пересчётах до`'),
                'allowEmpty' => false,
                'default' => '0.01'
            ]),
            'cbr_link' => new Type\Varchar([
                'description' => t('Альтернативный адрес XML API ЦБ РФ'),
                'maxLength'   => 255,
                'hint' => t('Это url с которого будет получена информация для получения курсов валют.<br/> 
                По умолчанию, если поле пустое - используется внутренняя константа с адресом.<br/>
                http://www.cbr.ru/scripts/XML_daily.asp')
            ]),
            'cbr_auto_update_interval' => new Type\Integer([
                'description' => t('Как часто обновлять курсы валют'),
                'listFromArray' => [[
                    '0' => t('Никогда'),
                    '1440' => t('Раз в сутки'),
                    '720' => t('Каждые 12 часов'),
                    '360' => t('Каждые 6 часов'),
                    '180' => t('Каждые 3 часа')
                ]],
                'default' => 1440
            ]),
            'cbr_percent_update' => new Type\Integer([
                'description' => t('Количество процентов, на которое должен отличатся прошлый курс валюты для обновления'),
                'maxLength'   => 11,
                'default' => 0,
                'hint' => t('Если 0, то процент не учитывается')
            ]),
            'use_offer_unit' => new Type\Integer([
                'description' => t('Использовать единицы измерения у комлектаций'),
                'maxLength'   => 1,
                'checkboxview' => [1, 0]
            ]),
            'import_photos_timeout' => new Type\Integer([
                'description' => t('Время выполнения одного шага импорта фотографий, сек.')
            ]),
            'use_seo_filters' => new Type\Integer([
                'description' => t('Включить ЧПУ фильтры?'),
                'hint' => t('Фильтры будут выглядеть как /catalog/category-name/brand_sumsung/sostav_hlopok-poliester/'),
                'checkboxview' => [1, 0],
            ]),
            'show_all_products' => new Type\Integer([
                'description' => t('Показывать все товары по маршруту /catalog/all/?'),
                'checkboxview' => [1, 0],
            ]),
            'price_like_slider' => new Type\Integer([
                'description' => t('Показывать фильтр по цене в виде слайдера?'),
                'checkboxview' => [1, 0],
            ]),
            'search_fields' => new Type\ArrayList([
                'description' => t('Поля, которые должны войти в поисковый индекс товара (помимо названия).'),
                'hint' => t('После изменения, переиндексируйте товары (ссылка справа)'),
                'Attr' => [['size' => 5, 'multiple' => 'multiple', 'class' => 'multiselect']],
                'ListFromArray' => [[
                    'properties' => t('Характеристики'),
                    'barcode' => t('Артикул'),
                    'brand' => t('Бренд'),
                    'short_description' => t('Краткое описание'),
                    'meta_keywords' => t('Мета ключевые слова'),
                    'ofbarcodes' => t('Артикулы комплектаций')
                ]],
                'CheckboxListView' => true,
                'runtime' => false,
            ]),
            'not_public_category_404' => new Type\Integer([
                'description' => t('Отдавать 404 ответ сервера у скрытых категорий?'),
                'checkboxview' => [1, 0],
            ]),
            'not_public_product_404' => new Type\Integer([
                'description' => t('Отдавать 404 ответ сервера у скрытых товаров?'),
                'checkboxview' => [1, 0],
            ]),
            'not_public_property_dir_404' => new Type\Integer([
                'description' => t('Отдавать 404 ответ сервера при попытке поиска по скрытым фильтрам?'),
                'checkboxview' => [1, 0],
                'default' => 1,
            ]),
            'link_property_to_offer_amount' => new Type\Integer([
                'description' => t('Учитывать остатки комплектаций товаров в фильтрах при использовании многомерных комплектаций'),
                'hint' => t('Значения характеристик товара будут отображаться в фильтре в зависимости от наличия комплектации с идентичной характеристикой.'),
                'checkboxView' => [1, 0]
            ]),
            'dependent_filters' => new Type\Integer([
                'description' => t('Включить зависимые фильры'),
                'hint' => t('Beta-версия зависимых фильтров. Работает только со списковыми характеристиками.'),
                'checkboxview' => [1, 0],
            ]),
            t('Купить в один клик'),
                '__clickfields__' => new Type\UserTemplate('%catalog%/form/config/userfield.tpl'),
                'clickfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля'),
                    'runtime' => false,
                    'visible' => false
                ]),
                'buyinoneclick' => new Type\Integer([
                    'description' => t('Включить отображение?'),
                    'checkboxview' => [1, 0],
                ]),
                'dont_buy_when_null' => new Type\Integer([
                    'description' => t('Не разрешать покупки в 1 клик, если товаров недостаточно на складе'),
                    'checkboxview' => [1, 0],
                ]),
                'oneclick_name_required' => new Type\Integer([
                    'description' => t('Поле "Ваше имя" является обязательным?'),
                    'checkboxview' => [1, 0],
                ]),
            t('Обмен данными в CSV'),
                'csv_id_fields' => new Type\ArrayList([
                    'runtime' => false,
                    'description' => t('Поля для идентификации товара при импорте (удерживая CTRL можно выбрать несколько полей)'),
                    'hint' => t('Во время импорта данных из CSV файла, система сперва будет обновлять товары, у которых будет совпадение значений по указанным здесь колонкам. В противном случае будет создаваться новый товар'),
                    'list' => [['\Catalog\Model\CsvSchema\Product','getPossibleIdFields']],
                    'size' => 7,
                    'attr' => [['multiple' => true]]
                ]),
                'csv_offer_product_search_field' => new Type\Varchar([
                    'runtime' => false,
                    'description' => t('Поле идентификации товара во время импорта CSV комплектаций'),
                    'hint' => t('Данные в колонке Товар у CSV файла комплектаций будет сравниваться с указанным здесь полем товара для связи'),
                    'list' => [['\Catalog\Model\CsvSchema\Offer','getPossibleProductFields']],
                ]),
                'csv_offer_search_field' => new Type\Varchar([
                    'description' => t('Поле идентификации комплектации'),
                    'list' => [['\Catalog\Model\CsvSchema\Offer','getPossibleOffersFields']],
                ]),
                'csv_dont_delete_stocks' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Не удалять остатки на складах, созданные на сайте'),
                    'checkboxView' => [1, 0],
                ]),
                'csv_dont_delete_costs' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Не удалять значения цен, созданные на сайте'),
                    'checkboxView' => [1, 0],
                ]),
                'csv_file_upload_type' => new Type\Integer([
                    'description' => t('Что делать, если импортируется файл с таким же названием'),
                    'listFromArray' => [[
                        Files::FILE_UPLOAD_TYPE_BOTH => 'Ничего',
                        Files::FILE_UPLOAD_TYPE_NEW => 'Оставить новый',
                        Files::FILE_UPLOAD_TYPE_OLD => 'Оставить старый',
                    ]],
                    'default' => Files::FILE_UPLOAD_TYPE_BOTH,
                ]),
                'csv_file_upload_access' => new Type\Integer([
                    'description' => t('Уровень доступа к файлу по умолчанию'),
                    'list' => [['\Files\Model\FilesType\CatalogProduct', 'getAccessTypes']],
                    'default' => CatalogProduct::ACCESS_TYPE_HIDDEN,
                ]),
            t('Бренды'),
                'brand_products_specdir' => new Type\Integer([
                    'description' => t('Спецкатегория, из которой выводить товары на странице бренда'),
                    'tree' => [['\Catalog\Model\DirApi', 'staticSpecTreeList'], 0, [0 => t('- Не выбрано -')]]
                ]),
                'brand_products_cnt' => new Type\Integer([
                    'description' => t('Кол-во товаров из спец. категории<br/> на странице бренда:'),
                ]),
                'brand_products_hide_unobtainable' => new Type\Integer([
                    'description' => t('Отображать только товары в наличии на странице бренда'),
                    'checkboxview' => [1, 0],
                ]),
            t('Склады'),
                'warehouse_sticks' => new Type\Varchar([
                    'description' => t('Градация наличия товара на складах'),
                    'hint' => t('Перечислите через запятую, количество товара,<br/> 
                    которое будет соответствовать 1, 2, 3-м и т.д. "деленям"<br/> наличия данного товара на складе')
                ]),
            t('Складской учет'),
                'inventory_control_enable' => new Type\Integer([
                    'visible' => false,
                    'description' => t('Включить складской учет'),
                ]),
                'ic_enable_button' => new Type\Integer([
                    'checkboxView' => [1, 0],
                    'default' => 0,
                    'template' => '%catalog%/form/inventory/enable_button.tpl',
                    'description' => t('Складской учет'),
                ]),
                'provider_user_group' => new Type\Varchar([
                    'description' => t('Группа, пользователи которой считаются поставщиками'),
                    'list' => [['\Users\Model\GroupApi','staticSelectList'], [0 => t('Не выбрано')]],
                ]),
                'csv_id_fields_ic' => new Type\Varchar([
                    'runtime' => false,
                    'description' => t('Поле для идентификации товара при импорте csv'),
                    'list' => [['\Catalog\Model\Inventory\InventoryTools','getPossibleIdFields']],
                    'default'  => 'barcode',
                ]),
            t('Настройки импорта YML'),
                'yuml_import_setting' => new Type\Varchar([
                    'description' => t('Корневая категория импорта'),
                    'hint' => t('Корневая категория для новых категорий'),
                    'tree' => [['\Catalog\Model\Dirapi', 'staticTreeList'], 0, [0 => t('- Корень каталога -')]]
                ]),
                'import_yml_timeout' => new Type\Integer([
                    'description' => t('Время выполнения импорта продуктов из yml, сек.'),
                    'default' => 26
                ]),
                'import_yml_cost_id' => new Type\Integer([
                    'description' => t('Тип цен, в который будет записываться цена товаров во время импорта продуктов из yml'),
                    'list' => [['\Catalog\Model\Costapi', 'staticSelectList'], [0 => t('Не выбрано')]]
                ]),
                'catalog_element_action' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Что делать с товарами, отсутствующими в файле импорта'),
                    'listfromarray' => [[
                        self::ACTION_NOTHING      => t('Ничего'),
                        self::ACTION_CLEAR_STOCKS => t('Обнулять остаток'),
                        self::ACTION_DEACTIVATE   => t('Деактивировать'),
                        self::ACTION_REMOVE       => t('Удалить')
                    ]
                    ],
                ]),
                'catalog_section_action' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Что делать с категориями, отсутствующими в файле импорта'),
                    'listfromarray' => [[
                        self::ACTION_NOTHING    => t('Ничего'),
                        self::ACTION_DEACTIVATE => t('Деактивировать'),
                        self::ACTION_REMOVE     => t('Удалить')
                    ]
                    ],
                ]),
                'save_product_public' => new Type\Integer([
                    'description' => t('Сохранять публичность товаров'),
                    'default' => 1,
                    'CheckboxView' => [1, 0],
                ]),
                'save_product_dir' => new Type\Integer([
                    'description' => t('Сохранять связь с категорией'),
                    'default' => 1,
                    'CheckboxView' => [1, 0],
                ]),
                'dont_update_fields' => new Type\ArrayList([
                    'description' => t('Поля товара, которые не следует обновлять'),
                    'Attr' => [['size' => 5,'multiple' => 'multiple', 'class' => 'multiselect']],
                    'List' => [['\Catalog\Model\Importymlapi', 'getUpdatableProductFields']],
                    'CheckboxListView' => true,
                    'runtime' => false,
                ]),
                'use_htmlentity' => new Type\Integer([
                    'description' => t('Не использовать htmlentity'),
                    'default' => 0,
                    'CheckboxView' => [1, 0],
                ]),
                'increase_cost' => new Type\Integer([
                    'description' => t('Увеличить цену на %'),
                    'default' => 0,
                ]),
                'use_vendorcode' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Какое поле использовать для идентификации товара при импорте'),
                    'ListFromArray' => [[
                        'offer_id' => t('ID товара'),
                        'vendor_code' => t('vendorCode'),
                    ]
                    ],
                    'default' => 'offer_id',
                ]),
            t('Параметры товаров'),
                'default_product_meta_title' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Заголовок товаров по умолчанию'),
                ]),
                'default_product_meta_keywords' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Ключевые слова товаров по умолчанию'),
                ]),
                'default_product_meta_description' => new Type\Varchar([
                    'maxLength' => '1000',
                    'viewAsTextarea' => true,
                    'description' => t('Описание товаров по умолчанию'),
                ]),
                'default_weight' => new Type\Real([
                    'description' => t('Вес одного товара по-умолчанию'),
                    'hint' => t('Данное значение можно переустановить в настройках категории или у самого товара')
                ]),
                'weight_unit' => new Type\Varchar([
                    'description' => t('Единица измерения веса товаров'),
                    'list' => [['\Catalog\Model\Api', 'getWeightUnitsTitles']]
                ]),
                'property_product_length' => new Type\Integer([
                    'description' => t('Характеристика "Длина товара"'),
                    'list' => [['Catalog\Model\PropertyApi', 'staticSelectList']],
                ]),
                'default_product_length' => new  Type\Decimal([
                    'description' => t('Длина товара по умолчанию'),
                ]),
                'property_product_width' => new Type\Integer([
                    'description' => t('Характеристика "Ширина товара"'),
                    'list' => [['Catalog\Model\PropertyApi', 'staticSelectList']],
                ]),
                'default_product_width' => new  Type\Decimal([
                    'description' => t('Ширина товара по умолчанию'),
                ]),
                'property_product_height' => new Type\Integer([
                    'description' => t('Характеристика "Высота товара"'),
                    'list' => [['Catalog\Model\PropertyApi', 'staticSelectList']],
                ]),
                'default_product_height' => new  Type\Decimal([
                    'description' => t('Высота товара по умолчанию'),
                ]),
                'dimensions_unit' => new Type\Enum(array_keys(ProductDimensions::handbookDimensionsUnits()), [
                    'description' => t('Единица измерения габаритов товаров'),
                    'listFromArray' => [ProductDimensions::handbookDimensionsUnits()],
                ]),
        ]);

        if (ModuleManager::staticModuleExists('affiliate') && ModuleManager::staticModuleEnabled('affiliate')) {
            $property_iterator->append([
                t('Склады'),
                    'affiliate_stock_restriction' => new Type\Integer([
                        'description' => t('Ограничить остатки товара только остатками на складах выбранного филиала'),
                        'hint' => t('Актуально только для Мегамаркета. Данная функция может замедлить выдачу товаров в списках'),
                        'checkboxView' => [1, 0],
                    ]),
            ]);
        }
    }

    /**
     * Возвращает список действий для панели конфига
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCleanProperty', [], 'catalog-tools'),
                    'title' => t('Удалить несвязанные характеристики'),
                    'description' => t('Удаляет характеристики и группы, которые не задействованы в товарах или категориях'),
                    'confirm' => t('Вы действительно хотите удалить несвязанные характеристики?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCleanOffers', [], 'catalog-tools'),
                    'title' => t('Удалить несвязанные комплектации'),
                    'description' => t('Удалит несвязанные комплектации, которые могли остаться в базе после отмены создания товара'),
                    'confirm' => t('Вы действительно хотите удалить несвязанные комплектации?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCleanRelatedProducts', [], 'catalog-tools'),
                    'title' => t('Удалить несвязанные сопутствующие и рекомендованные товары'),
                    'description' => t('Удалит несвязанные сопутствующие и рекомендованные товары, которые могли остаться в базе после удаления товаров'),
                    'confirm' => t('Вы действительно хотите удалить несвязанные сопутствующие и рекомендованные товары?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCheckAliases', [], 'catalog-tools'),
                    'title' => t('Добавить ЧПУ имена товарам и категориям'),
                    'description' => t('Добавит символьный идентификатор (методом транслитерации) товарам и категориям, у которых он отсутствует.'),
                    'confirm' => t('Вы действительно хотите добавить ЧПУ имена товарам и категориям?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCheckBrandsAliases', [], 'catalog-tools'),
                    'title' => t('Добавить ЧПУ имена брендам'),
                    'description' => t('Добавит символьный идентификатор (методом транслитерации) брендам, у которых он отсутствует.'),
                    'confirm' => t('Вы действительно хотите добавить ЧПУ имена брендам?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCheckPropertyAliases', [], 'catalog-tools'),
                    'title' => t('Добавить ЧПУ имена характеристикам и их значениям'),
                    'description' => t('Добавит символьный идентификатор (методом транслитерации) характеристикам и их значениям, у которых он отсутствует.'),
                    'confirm' => t('Вы действительно хотите добавить ЧПУ имена характеристикам и их значениям?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCreateMainOffers', [], 'catalog-tools'),
                    'title' => t('Создать основную комплектацию у всех товаров'),
                    'description' => t('Создаст основную комплекптцию у всех товаров, у которых она отсутствует'),
                    'confirm' => t('Вы действительно хотите создать основные комплектации для всех товаров?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxReIndexProducts', [], 'catalog-tools'),
                    'title' => t('Переиндексировать товары'),
                    'description' => t('Построит заново поисковый индекс по товарам'),
                    'confirm' => t('Вы действительно хотите переиндексировать все товары?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCleanImportHash', [], 'catalog-tools'),
                    'title' => t('Сбросить хеши импрота'),
                    'description' => t('Удаляет данные, использующиеся для ускорения повторного импорта'),
                    'confirm' => t('Вы действительно хотите удалить все хешированные данные импорта?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('openArchiveWindow', ['mode' => InventoryTools::$do_archive], 'catalog-inventoryctrl', true),
                    'title' => t('Заархивировать товары складского учета'),
                    'description' => t('Архивирует таблицу с данными документов складского учета. Увеличивает скорость работы складского учета'),
                    'class' => 'crud-add',
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('openArchiveWindow', ['mode' => InventoryTools::$do_unarchive], 'catalog-inventoryctrl', true),
                    'title' => t('Разархивировать товары складского учета'),
                    'description' => t('Восстановит архивные документы'),
                    'class' => 'crud-add',
                ],
            ]
            ];
    }

    /**
     * Действия перед записью
     *
     * @param string $flag - insert или update
     * @return void
     */
    public function beforeWrite($flag)
    {
        parent::beforeWrite($flag);
        
        $this->before_state = new self();
        $this->before_state->load();
    }

    /**
     * Действия после записи
     *
     * @param string $flag - insert или update
     * @return void
     */
    public function afterWrite($flag)
    {
        parent::afterWrite($flag);
        
        if ($this['hide_unobtainable_goods'] != $this->before_state['hide_unobtainable_goods']) {
            Dirapi::updateCounts(); //Обновляем счетчики у категорий
        }
        
        if ($this['link_property_to_offer_amount'] && !$this->before_state['link_property_to_offer_amount']) {
            $offer_api = new OfferApi();
            $offer_api->updateLinkedPropertiesForAllProducts();
        }
    }
    
    /**
    * Возвращает объект, отвечающий за работу с пользовательскими полями.
    * 
    * @return \RS\Config\UserFieldsManager
    */
    public function getClickFieldsManager()
    {
        return new UserFieldsManager($this['clickfields'], null, 'clickfields');
    }

    /**
    * Возвращает сокращённое обозначение текущей единицы измерения веса
    *
    * @return string
    */
    public function getShortWeightUnit()
    {
        $units_list = ProductApi::getWeightUnits();
        if (isset($units_list[$this['weight_unit']])) {
            return $units_list[$this['weight_unit']]['short_title'];
        }
        return $units_list[ProductApi::WEIGHT_UNIT_G]['short_title'];
    }
}
