<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Log\LogImportYml;
use Catalog\Model\Orm\Product;
use Photo\Model\Orm\Image;
use Photo\Model\PhotoApi;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use Catalog\Config\File as CatalogConfig;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception;
use RS\File\Tools as FileTools;
use RS\File\Uploader as FileUploader;
use RS\Helper\Log as RSLog;
use RS\Helper\Transliteration;
use RS\Module\AbstractModel\BaseModel;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Предоставляет возможности для импорта данных из YML файлов
 */
class ImportYmlApi extends BaseModel
{
    const LOG_FILE = '/logs/import_yml.log';
    const YML_ID_PREFIX = "yml_";
    const DELETE_LIMIT = 100;
    const OFFER_ID = "offer_id";
    const VENDOR_CODE = "vendor_code";

    protected $site_id;
    protected $cost_id = 0;
    protected $old_cost_id = 0;
    protected $yml_api;
    protected $timeout;
    protected $config;
    protected $allow_ext = ['yml', 'xml'];
    protected $tmp_data_file = 'data.tmp';
    protected $log;
    protected $yml_name = 'tmp.yml';
    protected $yml_folder;
    protected $yml_folder_rel = '/storage/tmp/importyml';
    protected $start_time;
    protected $params_fields = [
        'delivery' => 'Доставка',
        'local_deliver_cost' => 'Стоимость доставки',
        'typePrefix' => 'Тип',
        'vendorCode' => 'Код производителя',
        'model' => 'Модель',
        'manufacturer_warranty' => 'Гарантия производителя',
        'country_of_origin' => 'Страна-производитель',
        'author' => 'Автор',
        'publisher' => 'Издатель',
        'series' => 'Серия',
        'year' => 'Год',
        'ISBN' => 'ISBN',
        'volume' => 'Объем',
        'part' => 'Часть',
        'language' => 'Язык',
        'binding' => 'Переплет',
        'page_extent' => 'Количество страниц',
        'downloadable' => 'Возможность скачать',
        'performed_by' => 'Исполнитель',
        'performance_type' => 'Тип представления',
        'storage' => 'Носитель',
        'format' => 'Формат',
        'recording_length' => 'Длительность записи',
        'artist' => 'Артист',
        'media' => 'Носитель медиа',
        'starring' => 'В главных ролях',
        'country' => 'Страна',
        'director' => 'Директор',
        'originalName' => 'Оригинальное название',
        'worldRegion' => 'Регион мира',
        'region' => 'Регион',
        'days' => 'Дни',
        'title' => 'Название',
        'dataTour' => 'Дата тура',
        'hotel_stars' => 'Количество звезд',
        'room' => 'Комнаты',
        'meal' => 'Питание',
        'included' => 'Включено',
        'transport' => 'Транспорт',
        'place' => 'Место проведения',
        'hall' => 'Зал',
        'hall_part' => 'Расположение',
        'date' => 'Дата',
        'is_premiere' => 'Премьера',
        'is_kids' => 'Для детей',
        'adult' => 'Для взрослых',
        'pickup' => 'Самовывоз',
        'store' => 'Точка продаж',
        'sales_notes' => 'Ценовые предложения'
    ];

    public $storage;
    public $xmlIds;
    public $cachecheck = [];

    public function __construct()
    {
        $this->yml_folder = \Setup::$PATH . $this->yml_folder_rel;
        $this->log = LogImportYml::getInstance();
        $this->config = ConfigLoader::byModule($this);
        $this->setTimeout($this->config['import_yml_timeout']);
        $this->setSiteId(SiteManager::getSiteId());
        $this->setCostId($this->config->import_yml_cost_id ? $this->config->import_yml_cost_id : CostApi::getDefaultCostId());
        $this->setOldCostId($this->config->old_cost);

        $this->start_time = microtime(true);
        $this->loadLocalStorage();
    }

    /**
     * @param $cost_id
     */
    public function setCostId($cost_id)
    {
        $this->cost_id = $cost_id;
    }

    /**
     * @return integer
     */
    public function getCostId()
    {
        return $this->cost_id;
    }

    /**
     * Устанавливает зачеркнутую цену
     *
     * @param integer $cost_id - ID цены
     */
    public function setOldCostId($cost_id)
    {
        $this->old_cost_id = $cost_id;
    }

    /**
     * Возвращает ID зачеркнутой цены
     *
     * @return integer
     */
    public function getOldCostId()
    {
        return $this->old_cost_id;
    }

    /**
     * Устанавливает сайт, для которого будет происходить импорт данных
     *
     * @param integer $site_id - ID сайта
     * @return void
     */
    public function setSiteId($site_id)
    {
        $this->site_id = $site_id;
    }

    /**
     * Устанавливает время работы одного шага импорта
     *
     * @param integer $sec - количество секунд. Если 0 - то время шага не контроллируется
     * @return void
     */
    public function setTimeout($sec)
    {
        $this->timeout = $sec;
    }

    /**
     * Загружает временные данные текущего импорта
     *
     * @return array
     */
    public function loadLocalStorage()
    {
        if (!isset($this->storage)) {
            $filename = $this->yml_folder . '/' . $this->tmp_data_file;
            if (file_exists($filename)) {
                $this->storage = unserialize(file_get_contents($filename));
            }
            if (!isset($this->storage)) {
                $this->storage = [];
            }
        }
        return $this->storage;
    }

    /**
     * Сохраняет подготовленную информацию $value под ключем $key
     *
     * @param mixed $key
     * @param mixed $value
     * @return self
     */
    public function saveLocalKey($key, $value = null)
    {
        if (!isset($this->storage)) $this->storage = $this->loadLocalStorage();

        if ($key === null) {
            $this->storage = [];
        } elseif (is_array($key)) {
            $this->storage = array_merge($this->storage, $key);
        } else {
            $this->storage[$key] = $value;
        }
        $this->flushLocalStorage();

        return $this;
    }

    public function flushLocalStorage()
    {
        file_put_contents($this->yml_folder . '/' . $this->tmp_data_file, serialize($this->storage));
    }

    /**
     * Загружает YML файл в XMLReader из URL
     *
     * @param string $url - URL файла
     * @return bool
     */
    public function uploadFileFromUrl($url)
    {
        $save_path = $this->yml_folder . '/' . $this->yml_name;
        $yml = file_get_contents($url);
        if ($yml) {
            FileTools::makePath($this->yml_folder);
            file_put_contents($save_path, $yml);
        } else {
            return $this->addError(t('Не удалось загрузить файл'));
        }
        return true;
    }

    /**
     * Загружает данные из YML файла в XMLReader
     *
     * @param array $file - файл в формате YML
     * @return boolean
     */
    public function uploadFile($file)
    {
        FileTools::makePath($this->yml_folder);
        FileTools::deleteFolder($this->yml_folder, false);

        $uploader = new FileUploader($this->allow_ext, $this->yml_folder_rel);
        $uploader->setRightChecker([$this, 'checkWriteRights']);

        if (!$uploader->setUploadFilename($this->yml_name)->uploadFile($file)) {
            return $this->addError($uploader->getErrorsStr());
        }

        return true;
    }

    /**
     * Выполняет один шаг импорта
     *
     * @param array $step_data Массив с параметрами импорта:
     * [
     *   'upload_image' => bool, //Загружать изображения или нет
     *   'step' => integer, //номер текущего шага
     *   'offset' => integer //количество обработанных раннее элементов в шаге
     * ]
     *
     * @return array | bool Если возвращает array, то это означает что необходимо выполнить следующий шаг импорта с данными параметрами
     * Если возвращает false, значит во время импорта произошла ошибка
     * Если возвращает true, значит импорт завершен
     */
    public function process($step_data)
    {
        $this->getXmlIds();
        $steps = $this->getSteps($step_data);

        $current_step_data = $steps[$step_data['step']];
        $method = $current_step_data['method'];

        $result = $this->$method($step_data);
        if (is_array($result)) {
            //Шаг просит повторного выполнения
            if (isset($current_step_data['make_callback'])) {
                $result += call_user_func($current_step_data['make_callback']);
            }
            return $result + [
                'upload_images' => $step_data['upload_images'],
                'step' => $step_data['step']
                ];

        } elseif ($result) {
            //Шаг успешно выполнен, переходим к следующему или завершаем
            if ($step_data['step'] == count($steps) - 1) {
                return $this->finishProcess();
            } else {
                $next_step = $step_data['step'] + 1;
                $result = [
                    'upload_images' => $step_data['upload_images'],
                    'step' => $next_step,
                    'offset' => 0,
                ];
                if (isset($steps[$next_step]['make_callback'])) {
                    $result += call_user_func($steps[$next_step]['make_callback']);
                }
                return $result;
            }
        }

        //Произошла ошибка
        return false;
    }

    /**
     * Возвращает список шагов, которые будут выполнены во время импорта
     *
     * @param array $step_data - параметры импорта
     * @return array
     */
    public function getSteps($step_data)
    {
        $_this = $this;
        $steps = [
            [
                'title' => t('Подготовка к импорту'),
                'successTitle' => t('Подготовка к импорту завершена'),
                'method' => 'stepStart'
            ],
            [
                'title' => t('Импорт валют'),
                'successTitle' => t('Валюты импортированы'),
                'method' => 'stepCurrency'
            ],
            [
                'title' => t('Импорт категорий'),
                'successTitle' => t('Категории импортированы'),
                'method' => 'stepCategory',
                'make_callback' => function () use ($_this) {
                    return [
                        'total' => $_this->storage['yml_info']['category_count']
                    ];
                }
            ],
            [
                'title' => t('Импорт товаров'),
                'successTitle' => t('Товары импортированы'),
                'method' => 'stepProduct',
                'make_callback' => function () use ($_this) {
                    return [
                        'total' => count($_this->storage['yml_info']['products_xml_ids'])
                    ];
                }
            ],
            [
                'title' => t('Импорт рекомендуемых товаров'),
                'successTitle' => t('Рекомендуемые товары импортированы'),
                'method' => 'stepRecommended',
                'make_callback' => function () use ($_this) {
                    return [
                        'total' => count($_this->storage['yml_info']['products_xml_ids'])
                    ];
                }
            ],
        ];

        if (!empty($step_data['upload_images'])) {
            $steps = array_merge($steps, [
                [
                    'title' => t('Импорт изображений'),
                    'successTitle' => t('Изображения импортированы'),
                    'method' => 'stepImages',
                    'make_callback' => function () use ($_this) {
                        return [
                            'total' => $_this->storage['yml_info']['photo_count']
                        ];
                    }
                ]
            ]);
        }

        if (in_array($this->config['catalog_element_action'], [CatalogConfig::ACTION_REMOVE, CatalogConfig::ACTION_DEACTIVATE, CatalogConfig::ACTION_CLEAR_STOCKS])) {
            $after_import_products_step = [
                'method' => 'afterimportproducts',
            ];
            switch ($this->config['catalog_element_action']) {
                case CatalogConfig::ACTION_REMOVE:
                    $after_import_products_step['title'] = t('Удаление товаров');
                    $after_import_products_step['successTitle'] = t('Товары отсутствующие в файле удалены');
                    break;
                case CatalogConfig::ACTION_DEACTIVATE:
                    $after_import_products_step['title'] = t('Деактивация товаров');
                    $after_import_products_step['successTitle'] = t('Товары отсутствующие в файле деактивированы');
                    break;
                case CatalogConfig::ACTION_CLEAR_STOCKS:
                    $after_import_products_step['title'] = t('Обнуление остатков');
                    $after_import_products_step['successTitle'] = t('Остатки у товаров,отсутствующих в файле, обнулены');
                    break;
            }
            $steps = array_merge($steps, [$after_import_products_step]);
        }

        if (in_array($this->config['catalog_section_action'], [CatalogConfig::ACTION_REMOVE, CatalogConfig::ACTION_DEACTIVATE])) {
            $after_import_dirs_step = [
                'method' => 'afterimportdirs',
            ];
            switch ($this->config['catalog_section_action']) {
                case CatalogConfig::ACTION_REMOVE:
                    $after_import_products_step['title'] = t('Удаление категорий');
                    $after_import_products_step['successTitle'] = t('Категории отсутствующие в файле удалены');
                    break;
                case CatalogConfig::ACTION_DEACTIVATE:
                    $after_import_products_step['title'] = t('Деактивация категорий');
                    $after_import_products_step['successTitle'] = t('Категории отсутствующие в файле деактивированы');
                    break;
            }
            $steps = array_merge($steps, [$after_import_dirs_step]);
        }

        return $steps;
    }

    /**
     * Подготовка к импорту
     *
     * @param array $step_data - параметры импорта
     * @return bool
     */
    private function stepStart($step_data)
    {
        OrmRequest::make()
            ->update(new Orm\Product())
            ->set(['processed' => null])
            ->exec();

        //Очистка временного хранилища
        $this->saveLocalKey(null);
        $this->saveLocalKey('yml_info', $this->prepareImport());
        $this->saveLocalKey([
            'statistic' => [
                'inserted_categories' => 0,
                'updated_categories' => 0,
                'inserted_offers' => 0,
                'updated_offers' => 0,
                'inserted_photos' => 0,
                'already_exists_photos' => 0,
                'not_downloaded_photos' => 0,
                'deactivated_categories' => 0,
                'removed_categories' => 0,
                'deactivated_products' => 0,
                'removed_products' => 0,
                'cs_products' => 0
            ]
        ]);
        $this->log->write(t('Начало импорта'), LogImportYml::LEVEL_MAIN);

        return true;
    }

    /**
     * Заверщает процесс импорта
     *
     * @return bool
     */
    private function finishProcess()
    {
        $this->log->write(t('Конец импорта'), LogImportYml::LEVEL_MAIN);
        $this->cleanTemporaryDir();

        return true;
    }

    /**
     * Импортирует валюты
     *
     * @param array $step_data - параметры импорта
     * @return bool
     */
    private function stepCurrency($step_data)
    {
        $this->log->write(t("Шаг импорта валют - начало"), LogImportYml::LEVEL_OBJECT);
        $reader = $this->loadReader('currencies');
        if ($reader->readOuterXml()) {
            $currencies = new \SimpleXMLElement($reader->readOuterXml());

            foreach ($currencies as $curr_xml) {
                $currency_data = [
                    'site_id' => $this->site_id,
                    'title' => (string)$curr_xml->attributes()->id,
                ];
                $currency = Orm\Currency::loadByWhere($currency_data);

                //если нет валюты, добавляем ее
                if (!$currency['id']) {
                    $this->log->write(t("Создание валюты") . " \"{$currency_data['title']}\"", LogImportYml::LEVEL_OBJECT);
                    $currency_data['percent'] = $curr_xml->attributes()->plus ? (string)$curr_xml->attributes()->plus : 0;
                    $currency_data['ratio'] = is_numeric(str_replace(',', '.', $curr_xml->attributes()->rate)) ? str_replace(',', '.', $curr_xml->attributes()->rate) : 1;

                    $currency->getFromArray($currency_data)->insert(true);
                };
            }

            $reader->close();
        }
        $this->log->write(t("Шаг импорта валют - конец"), LogImportYml::LEVEL_OBJECT);
        return true;
    }

    /**
     * Импортирует категории товаров
     *
     * @param array $step_data - параметры импорта
     * @return array | bool
     */
    private function stepCategory($step_data)
    {
        $this->log->write(t("Шаг импорта категорий - начало"), LogImportYml::LEVEL_OBJECT);
        //id категорий
        $exists_dir = OrmRequest::make()
            ->from(new Orm\Dir())
            ->where(['site_id' => $this->site_id])
            ->where('xml_id LIKE \'' . self::YML_ID_PREFIX . '%\'')
            ->exec()->fetchSelected('xml_id', 'id');
        //config с id родительвкой категории для импорта         
        $config = $this->config;

        $reader = $this->loadReader('category');
        $offset = $step_data['offset'];

        $dirs = [];
        do {
            $category = new \SimpleXMLElement($reader->readOuterXML());
            $dirs[] = [
                'parent' => (int)$category->attributes()->parentId,
                'id' => (int)$category->attributes()->id,
                'name' => (string)$category[0],
            ];

        } while ($reader->next('category'));
        array_multisort($dirs);

        $cache = [];
        $count = 0;
        $testcount = 1;
        do {
            $count = $count + 1;
            foreach ($dirs as $key => $onedir) {

                if (($onedir['parent'] == '0') or isset($cache[$onedir['parent'] - 1])) {
                    $cache[$onedir['id'] - 1] = $onedir;
                    unset($dirs[$key]);
                    $testcount = $testcount + 1;
                }

            }
        } while ($count < $testcount + 10);
        unset($dirs);
        foreach ($cache as $item) {
            $dirs[] = $item;
        }
        do {
            $xml_id = self::YML_ID_PREFIX . $dirs[$offset]['id'];
            $uniq_postfix = hexdec(substr(md5($dirs[$offset]['id']), 0, 4));
            $dir = Orm\Dir::loadByWhere([
                'xml_id' => $xml_id,
                'site_id' => SiteManager::getSiteId(),
            ]);

            $dir['no_update_levels'] = true;
            //создание новой категории
            if (!isset($dir['id'])) {
                $this->log->write(t("Создание категории") . " \"{$dirs[$offset]['name']}\"", LogImportYml::LEVEL_OBJECT);
                $parent_dir = $dirs[$offset]['parent'] ? $exists_dir[self::YML_ID_PREFIX . $dirs[$offset]['parent']] : $config['yuml_import_setting'];

                $dir['parent'] = $parent_dir;
                $dir['public'] = 1;
                $dir['level'] = 0;
                $dir['weight'] = 0;
                $dir['processed'] = 1;
                $dir['xml_id'] = $xml_id;
                $dir['site_id'] = $this->site_id;
                $dir['meta_title'] = $dir['meta_keywords'] = $dir['meta_description'] = $dir['description'] = '';
                $dir['name'] = $dirs[$offset]['name'];
                $dir['alias'] = Transliteration::str2url($dirs[$offset]['name']) . "-" . $uniq_postfix;
                $dir->insert(true, ['alias', 'parent'], ['name', 'xml_id', 'site_id']);
                $exists_dir[$xml_id] = $dir['id'];
                $this->storage['statistic']['inserted_categories']++;
            } else {
                $this->log->write(t("Обновление категории") . " \"{$dirs[$offset]['name']}\"", LogImportYml::LEVEL_OBJECT);
                $dir['processed'] = 1;
                $dir->update();
                $this->storage['statistic']['updated_categories']++;
            }

            $offset++;
        } while (!($timeout = $this->checkTimeout()) && isset($dirs[$offset]));
        $reader->close();

        $this->flushLocalStorage();

        if ($timeout) {
            return [
                'offset' => $offset,
                'percent' => round($offset / $this->storage['yml_info']['category_count'] * 100)
            ];
        }

        Dirapi::updateLevels();
        $this->log->write(t("Шаг импорта категорий - конец"), LogImportYml::LEVEL_OBJECT);
        return true;
    }

    public function check($dirs, $id)
    {
        if (!isset($this->cachecheck[$id])) {
            $ids = array_column($dirs, 'id');
            $this->cachecheck[$id] = array_search($id, $ids);
        }
        return $this->cachecheck[$id];
    }

    /**
     * Импортирует товары
     *
     * @param array $step_data - параметры импорта
     * @return array | bool
     */
    private function stepProduct($step_data)
    {
        $this->log->write(t("Шаг импорта товаров - начало"), LogImportYml::LEVEL_OBJECT);
        //Загружаем справочники
        if ($step_data['offset'] == 0) {
            $this->saveLocalKey('list', $this->loadLists());
        }

        $reader = $this->loadReader('offer');
        $offset = 0;

        do {
            if (++$offset < $step_data['offset']) continue;

            $product = $this->importProduct($reader);

            if ($product->getLocalParameter('duplicate_updated')) {
                $this->storage['statistic']['updated_offers']++;
            } else {
                $this->storage['statistic']['inserted_offers']++;
            }

        } while (!($timeout = $this->checkTimeout()) && $reader->next('offer'));
        $reader->close();

        $this->flushLocalStorage();

        if ($timeout) {
            return [
                'offset' => $offset,
                'percent' => round($offset / count($this->storage['yml_info']['products_xml_ids']) * 100)
            ];
        }

        //Обновляем счетчики у категорий после импорта всех товаров
        Dirapi::updateCounts();

        $this->log->write(t("Шаг импорта товаров - конец"), LogImportYml::LEVEL_OBJECT);
        return true;
    }

    /**
     * Импортирует рекомендуемые товары
     *
     * @param array $step_data - параметры импорта
     * @return array | bool
     */
    private function stepRecommended($step_data)
    {
        $this->log->write(t("Шаг импорта рекомендованных - начало"), LogImportYml::LEVEL_OBJECT_DETAIL);
        if ($step_data['offset'] == 0) {
            //Создаем справочник xml_id => id
            $this->storage['list']['products'] = $this->loadProductsList();
            $this->saveLocalKey('list', $this->storage['list']);
        }

        $reader = $this->loadReader('offer');
        $product = new Orm\Product();
        $offset = 0;

        do {
            if (++$offset < $step_data['offset']) continue;

            $offer_xml = new \SimpleXMLElement($reader->readOuterXML());

            if ((string)$offer_xml->rec) {
                $xml_id = self::YML_ID_PREFIX . strval($offer_xml->attributes()->id);
                $product_id = $this->getIdByXmlId($xml_id);
                $recommended_xml = explode(',', (string)$offer_xml->rec);
                $recommended_arr = [];
                foreach ($recommended_xml as $offer_id) {
                    $recommended_arr['product'][] = $this->getIdByXmlId(self::YML_ID_PREFIX . $offer_id);
                }

                if (!empty($recommended_arr)) {
                    OrmRequest::make()
                        ->update($product)
                        ->set([
                            'recommended' => serialize($recommended_arr)
                        ])
                        ->where([
                            'id' => $product_id,
                            'site_id' => $this->site_id
                        ])->exec();
                }
            }

        } while (!($timeout = $this->checkTimeout()) && $reader->next('offer'));
        $reader->close();

        if ($timeout) {
            return [
                'offset' => $offset,
                'percent' => round($offset / count($this->storage['yml_info']['products_xml_ids']) * 100)
            ];
        }
        $this->log->write(t("Шаг импорта рекомендованных - конец"), LogImportYml::LEVEL_OBJECT_DETAIL);
        return true;
    }


    public function getIdByXmlId($xml_id)
    {
        static $cacheid = [];

        if (!isset($cacheid[$xml_id])) {


            $res = OrmRequest::make()
                ->select('id')
                ->from(new Orm\Product)
                ->where([
                    'xml_id' => $xml_id,
                    'site_id' => SiteManager::getSiteId(),
                ])
                ->exec()->getOneField('id');

            $cacheid[$xml_id] = $res;
        }

        return $cacheid[$xml_id];
    }

    /**
     * Импортирует изображения
     *
     * @param array $step_data - параметры импорта
     * @return array | bool
     */
    private function stepImages($step_data)
    {
        $this->log->write(t("Шаг импорта фотографий - начало"), LogImportYml::LEVEL_OBJECT_DETAIL);
        $reader = $this->loadReader('offer');
        $photoapi = new PhotoApi();
        $offset = 0;

        do {
            $offer_xml = new \SimpleXMLElement($reader->readOuterXML());
            if ((bool)$offer_xml->picture) {
                $offer_id = strval($offer_xml->attributes()->id);
                $xml_id = $this->getProductIdentificationId($reader);

                foreach ($offer_xml->picture as $picture) {
                    if (++$offset < $step_data['offset']) continue;

                    $product_id = $this->getIdByXmlId($xml_id);

                    //Проверяем, присутствует ли данное фото у товара
                    $image = OrmRequest::make()
                        ->from(new Image())
                        ->where([
                            'site_id' => $this->site_id,
                            'extra' => $xml_id,
                            'filename' => basename($picture),
                            'linkid' => $product_id
                        ])
                        ->object();

                    if (!$image) {
                        if ($photoapi->addFromUrl($picture, 'catalog', $product_id, true, $xml_id, false)) {
                            $this->storage['statistic']['inserted_photos']++;
                            $this->log->write(t('Загружено фото') . " \"$picture\"", LogImportYml::LEVEL_OBJECT_DETAIL);
                        } else {
                            $this->storage['statistic']['not_downloaded_photos']++;
                            $this->log->write(t('Не удалось загрузить фото') . " \"$picture\"", LogImportYml::LEVEL_OBJECT_DETAIL);
                            $photoapi->cleanErrors();
                        }
                    } else {
                        $this->storage['statistic']['already_exists_photos']++;
                    }
                }
            }

        } while (!($timeout = $this->checkTimeout()) && $reader->next('offer'));
        $reader->close();

        $this->flushLocalStorage();

        if ($timeout) {
            return [
                'offset' => $offset,
                'percent' => round($offset / $this->storage['yml_info']['photo_count'] * 100)
            ];
        }

        $this->log->write(t("Шаг импорта фотографий - конец"), LogImportYml::LEVEL_OBJECT_DETAIL);
        return true;
    }


    /**
     * Возвращает справочники данных
     * @return array
     */
    private function loadLists()
    {
        $list = [];
        $common_where = ['site_id' => $this->site_id];
        //Загружаем категории        
        $list['categories'] = OrmRequest::make()
            ->from(new Orm\Dir())
            ->where($common_where)
            ->exec()
            ->fetchSelected('xml_id', 'id');

        //Загружаем цены
        $list['costs'] = OrmRequest::make()
            ->from(new Orm\Typecost())
            ->where($common_where)
            ->exec()
            ->fetchSelected('title', 'id');

        //Загружаем бренды
        $list['brands'] = OrmRequest::make()
            ->from(new Orm\Brand())
            ->where($common_where)
            ->exec()
            ->fetchSelected('title', 'id');

        //Загружаем валюты
        $list['currencies'] = OrmRequest::make()
            ->from(new Orm\Currency())
            ->where($common_where)
            ->exec()
            ->fetchSelected('title', 'id');

        return $list;
    }

    /**
     * Возвращает справочник, содержащий связь xml_id => id
     *
     * @return array
     */
    private function loadProductsList()
    {
        $xml_ids = [];
        foreach ($this->storage['yml_info']['products_xml_ids'] as $xml_id) {
            $xml_ids[] = self::YML_ID_PREFIX . $xml_id;
        }

        $products = OrmRequest::make()
            ->select('xml_id, id')
            ->from(new Orm\Product())
            ->whereIn('xml_id', $xml_ids)
            ->where([
                'site_id' => $this->site_id
            ])
            ->exec()->fetchSelected('xml_id', 'id');

        return $products;
    }


    /**
     * Загружает даннные из файла в reader
     *
     * @param string $seek_to_element - до какого элемента опустить курсор
     * @return \XMLReader
     * @throws Exception
     */
    private function loadReader($seek_to_element = null)
    {
        $reader = new \XMLReader();
        if (!$reader->open($this->yml_folder . '/' . $this->yml_name)) {
            throw new Exception(t('Не удается открыть YML файл') . " '" . $this->yml_name . "'", 0);
        }

        if ($seek_to_element) {
            while ($reader->read() && ($reader->name != $seek_to_element)) {
            }
        }

        return $reader;
    }

    /**
     * Формирует массив id товаров и категорий
     *
     * @return array
     */
    private function prepareImport()
    {
        $product_xml_ids = [];
        $category_count = 0;
        $photo_count = 0;

        $reader = $this->loadReader();
        while ($reader->read()) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                if ($reader->name == 'category') {
                    $category_count++;
                } elseif ($reader->name == 'offer') {
                    $product_xml_ids[] = (int)$reader->getAttribute('id');
                } elseif ($reader->name == 'picture') {
                    $photo_count++;
                }
            }
        }
        $reader->close();

        return [
            'products_xml_ids' => $product_xml_ids,
            'category_count' => $category_count,
            'photo_count' => $photo_count
        ];
    }


    /**
     * Проверка времени выполнения скрипта, при превышении сохраняет состояние
     *
     * @return bool
     */
    private function checkTimeout()
    {
        return ($this->timeout && time() >= $this->start_time + $this->timeout);
    }

    /**
     * Проверяет права на запись
     *
     * @return bool|string
     */
    public function checkWriteRights()
    {
        return Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE);
    }

    /**
     * Возвращает поле, по которому происходит идентификация продукта при импорте из YML
     *
     * @param \XMLReader $reader
     * @return string
     */
    public function getProductIdentificationId($reader)
    {
        $offer_xml = new \SimpleXMLElement($reader->readOuterXml());
        $field = self::YML_ID_PREFIX . strval($offer_xml->attributes()->id);

        if ($this->config['use_vendorcode'] == self::VENDOR_CODE) {
            $field = (string)$offer_xml->vendorCode;
        }

        return $field;
    }

    /**
     * Обновляет продукты
     *
     * @param \XMLReader $reader
     * @return Orm\Product
     */
    private function importProduct($reader)
    {
        $offer_xml = new \SimpleXMLElement($reader->readOuterXml());
        $product = new Orm\Product();
        $product->setFlag(Product::FLAG_DONT_UPDATE_SEARCH_INDEX);
        $product->setFlag(Product::FLAG_DONT_UPDATE_DIR_COUNTER);

        $xml_id = $this->getProductIdentificationId($reader);
        $title = '';
        if ((string)$offer_xml->name) {
            $title = (string)$offer_xml->name;
        } elseif ((string)$offer_xml->model) {
            $title = (string)$offer_xml->model;
        }
        $original_title = $title;

        if ($this->config['use_htmlentity']) {
            $title = htmlspecialchars($title);
        }

        $this->log->write(t('Импорт товара') . " $xml_id \"$title\"", LogImportYml::LEVEL_OBJECT);

        $strr = ["\n", " "];
        // Импортируем цены в таблицу product_x_cost
        $excost_array = [];
        if (isset($offer_xml->price)) {
            if ($offer_xml->currencyId) {
                $coc = $this->storage['list']['currencies'][str_replace($strr, "", (string)$offer_xml->currencyId)];
            } else {
                static $currencyCache;
                if (empty($currencyCache)) {
                    $curapi = new CurrencyApi();
                    $DC = $curapi->getDefaultCurrency();
                    $currencyCache = $DC->id;
                    $coc = $currencyCache;       //cost_original_currency
                } else {
                    $coc = $currencyCache;
                }
            }

            $price = $this->config['increase_cost'] ? (string)$offer_xml->price * (1 + ($this->config['increase_cost'] / 100)) : (string)$offer_xml->price;
            $excost_array[$this->getCostId()] = [
                'cost_original_val' => CostApi::roundCost($price),
                'cost_original_currency' => $coc
            ];
            if ($offer_xml->oldprice && $this->getOldCostId()) {
                $old_price = $this->config['increase_cost'] ? (string)$offer_xml->oldprice * (1 + ($this->config['increase_cost'] / 100)) : (string)$offer_xml->oldprice;
                $excost_array[$this->getOldCostId()] = [
                    'cost_original_val' => CostApi::roundCost($old_price),
                    'cost_original_currency' => $coc
                ];
            }
            $product['excost'] = $excost_array;
        }

        $dir_xml_id = self::YML_ID_PREFIX . (int)$offer_xml->categoryId;

        //Если товар не связан с категорией, создаем для него категорию
        if ((int)$offer_xml->categoryId == 0 && !isset($this->storage['list']['categories'][$dir_xml_id])) {
            $dir = new Orm\Dir();
            $dir['name'] = t('Без категории');
            $dir['parent'] = 0;
            $dir['xml_id'] = $dir_xml_id;
            $dir->insert();
            $this->storage['list']['categories'][$dir_xml_id] = $dir['id'];
        }
        if (isset($this->storage['list']['categories'][$dir_xml_id])) {
            $dir_id = $this->storage['list']['categories'][$dir_xml_id];
        } else {
            $dir_id = $this->config['yuml_import_setting'];
        }
        //Добавление бренда
        if ((string)$offer_xml->vendor) {
            //Создается бренд, если его не существует
            $vendor_title = !$this->config['use_htmlentity'] ? (string)$offer_xml->vendor : htmlspecialchars((string)$offer_xml->vendor);
            if (!isset($this->storage['list']['brands'][$vendor_title])) {
                $this->log->write(t("Создание бренда \"$vendor_title\""), LogImportYml::LEVEL_OBJECT_DETAIL);
                $vendor = new Orm\Brand();
                $vendor['title'] = $vendor_title;
                $vendor['site_id'] = $this->site_id;
                $vendor->insert();

                $this->storage['list']['brands'][$vendor_title] = $vendor['id'];
            }
            $product['brand_id'] = $this->storage['list']['brands'][$vendor_title];

        }

        $product['xml_id'] = $xml_id;

        //запоминание публичности
        $product['public'] = (string)$offer_xml->attributes()->available == 'false' ? 0 : 1;
        if($this->config->save_product_public) {
            $product_new = Product::loadByWhere(['xml_id' => $xml_id, 'site_id' => $this->site_id]);
            if($product_new['id']){
                $product['public'] = $product_new->public;
            }
        }

        $product['title'] = $title;
        $product['description'] = !$this->config['use_htmlentity'] ? (string)$offer_xml->description : htmlspecialchars((string)$offer_xml->description);
        //Запоминание категорий
        $product['xdir'] = [$dir_id];
        $product['maindir'] = (int)$dir_id;
        if($this->config->save_product_dir)
        {
            $product_new = Product::loadByWhere(['xml_id' => $xml_id, 'site_id' => $this->site_id]);
            if($product_new['id']){
                $product['xdir'] = $product_new->xdir;
                $product['maindir'] = $product_new->maindir;
            }
        }
        $product['barcode'] = !$this->config['use_htmlentity'] ? (string)$offer_xml->vendorCode : htmlspecialchars((string)$offer_xml->vendorCode);
        $product['site_id'] = $this->site_id;
        $product['processed'] = 1;
        $uniq_postfix = hexdec(substr(md5($xml_id), 0, 4));
        $product['alias'] = Transliteration::str2url($original_title, true, 140) . "-" . $uniq_postfix;

        EventManager::fire('importyml.product.after', [
            'product' => $product,
            'offer_xml' => $offer_xml,
            'api' => $this,
        ]);

        $on_duplicate_update_fields = ['xml_id', 'title', 'description', 'public', 'maindir', 'barcode', 'processed', 'brand_id'];
        $on_duplicate_update_fields = array_diff($on_duplicate_update_fields, (array)$this->config->dont_update_fields);
        $product->insert(false, $on_duplicate_update_fields, ['site_id', 'xml_id']);
        $this->updateProductParams($offer_xml, $product['id']);

        return $product;
    }

    /**
     * Обновляет характеристики продуктов
     *
     * @param \SimpleXMLElement $offer_xml
     * @param int $product_id
     * @return void
     */
    private function updateProductParams(\SimpleXMLElement $offer_xml, $product_id)
    {
        //Удаляем свойства продукта
        OrmRequest::make()
            ->delete()
            ->from(new Orm\Property\Link)
            ->where([
                'site_id' => $this->site_id,
                'product_id' => $product_id,
            ])
            ->where('xml_id LIKE \'' . self::YML_ID_PREFIX . '%\'')
            ->exec();

        foreach ($offer_xml->param as $param) {
            $xml_id = $this->genXmlId((string)$param->attributes()->name);
            if ($checkproperty = Orm\Property\Item::loadByWhere([
                'xml_id' => $xml_id,
                'site_id' => SiteManager::getSiteId()])
            ) {
                if ($checkproperty->type == 'list') {
                    if ($itemvalue = Orm\Property\ItemValue::loadByWhere([
                        'prop_id' => $checkproperty->id,
                        'site_id' => $this->site_id,
                        'value' => (string)$param[0]])
                    ) {
                        $this->newPLink($checkproperty['id'], 'val_list_id', $itemvalue['id'], $product_id, $this->genXmlId($xml_id . (string)$param[0]));
                    } else {
                        $itemvalue = new Orm\Property\ItemValue();
                        $itemvalue['prop_id'] = $checkproperty->id;
                        $itemvalue['value'] = (string)$param[0];
                        $itemvalue['site_id'] = $this->site_id;
                        $itemvalue->insert();
                        $this->newPLink($checkproperty['id'], 'val_list_id', $itemvalue['id'], $product_id, $this->genXmlId($xml_id . (string)$param[0]));
                    }
                } else {
                    $product_property = $this->newPItem('string', (string)$param->attributes()->name, $xml_id);
                    $this->newPLink($product_property['id'], 'val_str', (string)$param[0], $product_id, $this->genXmlId($xml_id . (string)$param[0]));
                }
            } else {
                $product_property = $this->newPItem('string', (string)$param->attributes()->name, $xml_id);
                $this->newPLink($product_property['id'], 'val_str', (string)$param[0], $product_id, $this->genXmlId($xml_id . (string)$param[0]));
            }
        }
        //Сохраняются свойства из тегов не в property
        foreach ($offer_xml as $node) {
            if (!($node_name = $this->getParamsField($node->getName())) || empty($node[0])) continue;

            $xml_id = $this->genXmlId($node_name);
            if ($node->getName() == 'dataTour' && is_array($node[0])) {
                $node_val = join(', ', $node[0]);
            } else {
                $node_val = ((string)$node[0] == 'true') ? t('да') : (((string)$node[0] == 'false') ? t('нет') : (string)$node[0]);
            }
            $product_property = $this->newPItem('string', $node_name, $xml_id);
            $this->newPLink($product_property['id'], 'val_str', $node_val, $product_id, $this->genXmlId($xml_id . $node_val));
        }
    }

    /**
     * Создает характеристику
     *
     * @param string $type тип характеристики
     * @param string $title название характеристики
     * @param string $xml_id xml идентификатор характеристики
     * @return \Catalog\Model\Orm\Property\Item объект характеристики
     */
    public function newPItem($type, $title, $xml_id)
    {
        $this->log->write(t('Создание характеристики') . " \"$title\" ($type)", LogImportYml::LEVEL_OBJECT_DETAIL);
        $product_property = new Orm\Property\Item();
        $product_property['site_id'] = $this->site_id;
        $product_property['type'] = $type;
        $product_property['title'] = $title;
        $product_property['xml_id'] = $xml_id;
        $product_property->insert(false, ['title', 'type'], ['xml_id', 'site_id']);
        return $product_property;
    }

    /**
     * Создает связь характеристики с товаром
     *
     * @param string $pp_id id характеристики
     * @param string $val_type тип значения характеристики
     * @param string $val значение или id значения(если списковая)
     * @param string $p_id id товара
     * @param string $xml_id xml идентификатор
     * @return  \Catalog\Model\Orm\Property\Link объект связи характеристики с товаром
     */
    public function newPLink($pp_id, $val_type, $val, $p_id, $xml_id)
    {
        $product_property_link = new Orm\Property\Link();
        $product_property_link['site_id'] = $this->site_id;
        $product_property_link['prop_id'] = $pp_id;
        $product_property_link['product_id'] = $p_id;
        $product_property_link[$val_type] = $val;
        $product_property_link['xml_id'] = $xml_id;
        $product_property_link->insert(false, [$val_type], ['xml_id', 'site_id', 'product_id']);
        return $product_property_link;
    }


    /**
     * Возвращает список полей товара, обновление которых можно отключить из настроек модуля
     *
     * @param bool $exclude_exceptions - Исключая поля, указанные как "не обновлять" в настройках модуля
     * @return string[]
     */
    public static function getUpdatableProductFields($exclude_exceptions = false)
    {
        $fields = [];
        $fields['title'] = t('Наименование');
        $fields['barcode'] = t('Артикул');
        $fields['description'] = t('Описание');
        $fields['maindir'] = t('Связь с категорией');

        if ($exclude_exceptions) {
            $dont_update_fields = (array)ConfigLoader::byModule('catalog')->dont_update_fields;
            return array_diff_key($fields, array_flip($dont_update_fields));
        }

        return $fields;
    }

    /**
     * Генерирует xml_id
     *
     * @param string $str
     * @return string
     */
    public function genXmlId($str)
    {
        return self::YML_ID_PREFIX . crc32((string)$str);
    }

    /**
     * Очищает временную директорию
     *
     * @return void
     */
    public function cleanTemporaryDir()
    {
        @unlink($this->yml_folder . '/' . $this->yml_name);
        @unlink($this->yml_folder . '/' . $this->tmp_data_file);
    }

    /**
     * Возвращает сопоставленную тегу характеристику
     *
     * @param string $param - наименование тега
     * @return string | false
     */
    private function getParamsField($param)
    {
        return (isset($this->params_fields[$param])) ? t($this->params_fields[$param]) : false;
    }

    /**
     * Возвращает массив со статистическими данными об импорте
     * @return array
     */
    public function getStatistic()
    {
        return $this->storage['statistic'];
    }

    public function getXmlIds()
    {
        $this->xmlIds = OrmRequest::make()
            ->from(new Orm\Dir())
            ->where([
                'site_id' => SiteManager::getSiteId()
            ])->exec()->fetchSelected('xml_id', 'id');
    }

    public function afterimportproducts()
    {
        $config = $this->config;

        $ids = OrmRequest::make()
            ->select('id')
            ->from(new Orm\Product())
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'processed' => 1,
            ])
            ->exec()
            ->fetchSelected(null, 'id');

        $api = new Api();

        $api->updateProductSearchIndex($ids);


        // Что делать с элементами, отсутствующими в файле импорта
        switch ($config['catalog_element_action']) {
            case CatalogConfig::ACTION_CLEAR_STOCKS: //Обнулять остаток
                $countpcs = 0;
                // Получаем id товаров, не участвовавших в импорте
                $ids = OrmRequest::make()
                    ->select('id')
                    ->from(new Orm\Product())
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'processed' => null,
                    ])
                    ->exec()
                    ->fetchSelected(null, 'id');

                // Если есть товары, не участвовавшие в импорте - удалим линки остатков, кэш остатков у комплектаций и товаров
                if (!empty($ids)) {
                    OrmRequest::make()
                        ->delete()
                        ->from(new Orm\Xstock())
                        ->whereIn('product_id', $ids)
                        ->exec();
                    OrmRequest::make()
                        ->update(new Orm\Offer())
                        ->set(['num' => 0])
                        ->whereIn('product_id', $ids)
                        ->exec();
                    OrmRequest::make()
                        ->update(new Orm\Product())
                        ->set(['num' => 0])
                        ->whereIn('id', $ids)
                        ->exec();
                }

                $countpcs = $countpcs + count($ids);
                $this->storage['statistic']['cs_products'] = $countpcs;
                $this->flushLocalStorage();
                break;
            case CatalogConfig::ACTION_REMOVE: // Удалять
                $countpr = 0;
                $apiCatalog = new Api();

                while (true) {
                    $ids = OrmRequest::make()
                        ->select('id')
                        ->from(new Orm\Product())
                        ->where([
                            'site_id' => SiteManager::getSiteId(),
                            'processed' => null,
                        ])
                        ->limit(self::DELETE_LIMIT)
                        ->exec()
                        ->fetchSelected(null, 'id');

                    // Если не осталось больше товаров для удаления
                    if (empty($ids)) {
                        break;
                    }
                    $apiCatalog->multiDelete($ids);
                    $countpr = $countpr + count($ids);
                    $this->storage['statistic']['removed_products'] = $countpr;
                    $this->flushLocalStorage();

                }
                break;
            case CatalogConfig::ACTION_DEACTIVATE: // Деактивировать
                $affected = OrmRequest::make()
                    ->update(new Orm\Product())
                    ->set(['public' => 0])
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'processed' => null,
                    ])
                    ->exec()->affectedRows();
                $this->storage['statistic']['deactivated_products'] = $affected;
                $this->flushLocalStorage();
                break;
        }
        return true;
    }

    public function afterimportdirs()
    {
        $config = $this->config;
        switch ($config['catalog_section_action']) {
            case CatalogConfig::ACTION_REMOVE:
                $countdirr = 0;
                while (true) {
                    $dir = Orm\Dir::loadByWhere([
                        'site_id' => SiteManager::getSiteId(),
                        'is_spec_dir' => 'N', // Удалению подлежат только категориии, не являющиеся "спец-категориями"
                        'processed' => null,
                    ]);

                    // Если не осталось больше объектов для удаления
                    if (!$dir['id']) {
                        break;
                    }

                    $dir->delete();
                    $countdirr = $countdirr + 1;
                    $this->storage['statistic']['removed_categories'] = $countdirr;
                    $this->flushLocalStorage();

                }
                break;
            case CatalogConfig::ACTION_DEACTIVATE:
                $affected = OrmRequest::make()
                    ->update(new Orm\Dir())
                    ->set(['public' => 0])
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'processed' => null,
                    ])
                    ->exec()->affectedRows();

                $this->storage['statistic']['deactivated_categories'] = $affected;
                $this->flushLocalStorage();
                break;
        }
        return true;
    }
}
