<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Vkontakte;

use Catalog\Model\Orm\Offer;
use Export\Model\Api;
use Export\Model\ExportType\ExchangableInterface;
use Export\Model\ExportType\Vkontakte\Utils\VkQuery;
use Export\Model\Fetcher;
use Export\Model\Orm\ExternalProductLink;
use Export\Model\Orm\Vk\VkCategoryLink;
use RS\Config\Loader;
use RS\Db\Adapter;
use RS\Exception;
use Catalog\Model\Orm\Product;
use Export\Model\ExportType\AbstractType;
use Export\Model\Orm\ExportProfile;
use RS\Helper\Log;
use RS\Img\Core;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Router\Manager;

/**
 * Экспорт товаров в ВКонтакте
 */
class Vkontakte extends AbstractType implements ExchangableInterface
{
    const  ERROR_ITEM_NOT_FOUND = 1403;
    const  EDIT_ERROR_DELETED_BY_VK = 0;

    const LOG_LEVEL_NO = 0;
    const LOG_LEVEL_ACTION = 1;
    const LOG_LEVEL_QUERY = 2;

    public $log_filename = '/export_api_profile-%ID%.log';
    private $profile;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var VkQuery
     */
    private $vk_query;

    function _init()
    {
        return parent::_init()
            ->append([
                t('Основные'),
                'group_id' => new Type\Varchar([
                    'description' => t('Идентификатор вашей группы ВК (число)'),
                    'hint' => t('Числовой идентификатор группы можно получить в настройках вашей группы ВК, в разделе Работа с API -> Callback API. Убедитесь, что в Настройка -> Разделы, будет установлен флажок напротив пункта Товары.'),
                    'checker' => ['ChkEmpty', t('Укажите идентификатор группы ВК')]
                ]),
                'client_id' => new Type\Integer([
                    'description' => t('Идентификатор вашего приложения ВК'),
                    'hint' => t('Зарегистрируйте приложение ВК с типом `Веб-сайт` по сылке https://vk.com/editapp?act=create, вам будет присвоен ID'),
                    'checker' => ['ChkEmpty', t('Укажите идентификатор вашего приложения ВК')]
                ]),
                'secret_key' => new Type\Varchar([
                    'description' => t('Защищенный ключ вашего приложения'),
                    'hint' => 'Получите его в настройках вашего приложения ВК',
                    'checker' => ['ChkEmpty', t('Укажите защищенный ключ вашего приложения ВК')]
                ]),
                'access_token' => new Type\Varchar([
                    'description' => t('Токен доступа(Access Token)'),
                    'hint' => 'В этом поле хранится API-ключ доступа Vkontakte. Данное поле будет заполнено, после создания профиля',
                    'attr' => [[
                        'readonly' => true
                    ]]
                ]),
                'log_level' => new Type\Integer([
                    'description' => t('Логировать обмен'),
                    'listFromArray' => [[
                        self::LOG_LEVEL_NO => t('Не логировать'),
                        self::LOG_LEVEL_ACTION => t('Логировать только операции'),
                        self::LOG_LEVEL_QUERY => t('Логировать операции и запросы к VK')
                    ]]
                ]),
                'image_resize_width' => new Type\Integer([
                    'description' => t('Ширина изображений товара для выгрузки'),
                    'checker' => ['chkMinmax', t('Ширина должна быть в диапазоне от 400 до 2000 px'), 400, 2000],
                    'hint' => t('Используется только, если выгружаются не оригиналы изображения'),
                    'default' => '800'
                ]),
                'image_resize_height' => new Type\Integer([
                    'description' => t('Высота изображений товара для выгрузки'),
                    'checker' => ['chkMinmax', t('Высота должна быть в диапазоне от 400 до 2000 px'), 400, 2000],
                    'hint' => t('Используется только, если выгружаются не оригиналы изображения'),
                    'default' => '800'
                ]),
                'export_amount' => new Type\Integer([
                    'description' => t('Количество выгружаемых товаров за 1 запуск планировщика'),
                    'default' => 30
                ]),
                'life_time' => new Type\Integer([
                    'description' => t('Период экспорта'),
                    'listFromArray' => [[
                        0 => t('Никогда'),
                        120 => t('Каждые 2 часа'),
                        240 => t('Каждые 4 часа'),
                        1440 => t('Раз в день'),
                    ]],
                ]),
                '__gate_url__' => new Type\MixedType([
                    'visible' => false
                ])
            ]);
    }

    /**
     * Возвращает название типа экспорта
     *
     * @return string
     */
    public function getTitle()
    {
        return t("ВКонтакте");
    }

    /**
     * Возвращает описание типа экспорта для администратора. Возможен HTML
     *
     * @return string
     */
    public function getDescription()
    {
        return t("Экспорт товаров для ВКонтакте");
    }

    /**
     * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'vkontakte';
    }

    /**
     * Возвращает список классов типов описания
     *
     * @return \Export\Model\ExportType\AbstractOfferType[]
     */
    protected function getOfferTypesClasses()
    {
        return [
            new OfferType\Simple()
        ];
    }

    /**
     * Заглушка для метода. Экспорт в виде XML не поддерживается
     *
     * @return string|void
     * @throws Exception
     */
    public function export()
    {
        throw new Exception('Метод export() невозможно использовать в данном профиле экспорта');
    }

    /**
     * Возвращает access token для доступа к API ВКонтакте
     *
     * @return string
     */
    public function getToken()
    {
        return $this['access_token'];
    }

    /**
     * Редактирует товар в группе ВК
     *
     * @param integer $group_id
     * @param $ext_vk_id
     * @param $product_data
     * @return bool | integer Возвращает:
     *  true - в случае успеха,
     *  false - в случае неудачи, по различным причинам
     *  0 - в случае, если товар был удален на стороне VK
     */
    private function editItem($group_id, $ext_vk_id, $product_data)
    {
        $request_params = [
                'owner_id' => '-'.$group_id,
                'item_id' => $ext_vk_id,
                'access_token' => $this->getToken(),
            ] + $product_data;

        $result = $this->vk_query->query($request_params, 'market.edit');

        //Если товар был удален из VK, то сообщаем об этом
        if ($result === false && $this->vk_query->getLastErrorCode() == self::ERROR_ITEM_NOT_FOUND) {
            return self::EDIT_ERROR_DELETED_BY_VK;
        }

        return $result == 1;
    }

    /**
     * Загружает товар в группу ВК
     *
     * @param integer $group_id - ID группы ВК
     * @param array $product_data - поля товара
     * @return bool | integer Возвращает ID товара ВК или false
     */
    private function addItem($group_id, $product_data)
    {
        $request_params = [
            'owner_id' => '-'.$group_id,
            'access_token' => $this->getToken(),
            ] + $product_data;

        $result = $this->vk_query->query($request_params, 'market.add');

        if ($result['market_item_id']) {
            return $result['market_item_id'];
        }
        return false;
    }


    /**
     * Возвращает ссылку для отправки изображения через POST
     *
     * @param $group_id
     * @param int $main_photo
     * @return bool|string
     */
    private function getMarketUploadServer($group_id, $main_photo = 1)
    {
        $request_params = [
            'group_id' => $group_id,
            'main_photo' => (int)$main_photo,
            'access_token' => $this->getToken(),
        ];

        $result = $this->vk_query->query($request_params,'photos.getMarketUploadServer');

        if (isset($result['upload_url'])) {
            return $result['upload_url'];
        }

        return false;
    }

    /**
     * Сохраняет фотографию на сервере Vkontakte
     *
     * @param integer $group_id - ID Группы ВК
     * @param array $photo - массив с информацией об одном загруженном изображении от uploadPhoto()
     * @param $server - данные от uploadPhoto()
     * @param $hash - данные от uploadPhoto()
     * @param null $crop_data - данные от uploadPhoto()
     * @param null $crop_hash - данные от uploadPhoto()
     * @return array | bool(false)
     */
    private function saveMarketPhoto($group_id, $photo, $server, $hash, $crop_data = null, $crop_hash = null)
    {
        $request_params = [
            'group_id' => $group_id,
            'photo' => $photo,
            'server' => $server,
            'hash' => $hash,
            'access_token' => $this->getToken()
        ];

        if ($crop_data !== null && $crop_hash !== null) {
            $request_params += [
                'crop_data' => $crop_data,
                'crop_hash' => $crop_hash,
            ];
        }

        $result = $this->vk_query->query($request_params, 'photos.saveMarketPhoto');

        return $result;
    }

    /**
     * Загрузка фотографии методом POST
     *
     * @param ExportProfile $profile Профиль экспорта
     * @param string $url URL загрузки фотографии
     * @param string $filepath путь на диске к фото
     * @param bool $main_photo путь на диске к главной фото
     * @return bool|mixed|string
     */
    private function uploadPhoto(ExportProfile $profile, $url, $filepath, $main_photo = false)
    {
        $curl = curl_init($url);
        $curlfile = curl_file_create($filepath,'image/jpg', basename($filepath));

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['photo' => $curlfile]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl);
        curl_close($curl);

        if ($profile['log_level'] == self::LOG_LEVEL_QUERY) {
            $this->log->append(t('--> POST запрос на URL: %url, отправляем изображение: %filepath', ['url' => $url, 'filepath' => $filepath]));
            $this->log->append(t('<-- Получен ответ: %result', ['result' => $result]));
        }

        //Переустанавливаем соединение с базой на случай долгой загрузки изображений, fix: Mysql has gone away
        Adapter::disconnect();
        Adapter::connect();

        if ($result) {
            $result = json_decode($result, true);

            if (!isset($result['error'])) {
                $this->log->append(t('Изображение загружено на сервер VK'));

                if ($main_photo) {
                    $result = $this->saveMarketPhoto($profile['group_id'], $result['photo'], $result['server'], $result['hash'], $result['crop_data'], $result['crop_hash']);
                } else {
                    $result = $this->saveMarketPhoto($profile['group_id'], $result['photo'], $result['server'], $result['hash']);
                }

                return $result;
            }
        }

        $this->log->append(t('Изображение не загружено на сервер VK. Ошибка: %error', ['error' => $result['error']['error_msg']]));

        return false;
    }


    /**
     * Возвращает путь к файлу, в котором будут храниться ID товаров для экспорта
     *
     * @param integer $profile_id - ID профиля
     * @return string
     */
    function getExchangeCacheFilepath($profile_id)
    {
        $filename = \Setup::$ROOT.\Setup::$STORAGE_DIR.'/export/api/api_vk_products_'.$profile_id;
        return $filename;
    }

    /**
     * Возвращает путь к файлу лога
     *
     * @param ExportProfile $profile
     * @return string
     */
    function getLogFilename(ExportProfile $profile)
    {
        return \Setup::$ROOT.\Setup::$LOGS_DIR.str_replace('%ID%', $profile['id'], $this->log_filename);
    }


    /**
     * Выполняет одну итерацию экспорта товаров в ВК
     * Возвращает true, если экспорт был завершен полностью.
     * Возвращает число, если экспорт был выполнен частично. Нужно повторно вызвать данный метод для продолжения.
     *
     * @return bool|int
     * @throws Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    function doExchange()
    {
        //Отключаем принудительно Webp, ВК его не принимает
        Core::switchFormat(Core::FORMAT_WEBP, false);
        $profile = $this->getExportProfile();

        $this->log = Log::file($this->getLogFilename($profile));
        $this->log->enableDate();
        $this->log->setEnable($profile['log_level'] != self::LOG_LEVEL_NO);
        $this->vk_query = new VkQuery(5.95, $profile['log_level'] == self::LOG_LEVEL_QUERY ? $this->log : null);


        $fetcher = new Fetcher($this->getExchangeCacheFilepath($profile['id']));
        if (!$fetcher->issetBuffer()) {
            $query = $this->getProductsSelectionQuery($profile);

            //Здесь содержаться первично отобранные ID товаров, ряд условий будет проверяться дальше
            $ids = $query->exec()->fetchSelected(null, 'id');
            $fetcher->initialize($ids);

            $this->log->clean();
            $this->log->append(t('Выбрано %n товаров для выгрузки. Сформирован буфферный файл.', ['n' => count($ids)]));
        }

        $this->log->append(t('Запущен экспорт по API профиля(%id): %title', [
            'id' => $profile['id'],
            'title' => $profile['title']
        ]));

        $list = $fetcher->fetchList($this['export_amount']);
        $catalogApi = new \Catalog\Model\Api();
        $exported_offers_count = 0;

        $this->log->append(t('Отобрана порция из %n товаров для выгрузки', [
            'n' => count($list)
        ]));

        if ($list) {
            $catalogApi->setFilter('id', $list, 'in');
            $products = $catalogApi->getList();
            if ($profile['consider_warehouses'] && !in_array(0, $profile['consider_warehouses'])) {
                $catalogApi->setWarehousesForDynamicNum($profile['consider_warehouses']);
            }
            $products = $catalogApi->addProductsCost($products);
            $products = $catalogApi->addProductsOffers($products);
            $products = $catalogApi->addProductsDirs($products);
            $products = $catalogApi->addProductsPhotos($products);
            $products = $this->addExternalOfferData($profile, $products);

            foreach($products as $product) {
                $this->log->append(t('<< Начало выгрузки товара(%id): %title', [
                    'id' => $product['id'],
                    'title' => $product['title']
                ]));

                $exported_offers_count += $this->exportOneProduct($profile, $product);

                $this->log->append(t('>> Конец выгрузки товара(%id)', [
                    'id' => $product['id'],
                ]));
            }
        }

        if (count($list) == $this['export_amount']) {
            return $exported_offers_count;
        }

        //Возвращает true, если экспорт полностью завершен
        $this->log->append(t('Завершен цикл выгрузки товаров профиля (%id): %title', [
            'id' => $profile['id'],
            'title' => $profile['title']
        ]));

        return true;
    }

    /**
     * Добавляет дополнительную информацию (ID в ВК и флаг необходимости экспорта)
     *
     * @param ExportProfile $profile Профиль экспорта
     * @param Product[] $products массив товаров
     * @return Product[]
     *
     * @throws Exception
     * @throws \RS\Db\Exception
     */
    private function addExternalOfferData(ExportProfile $profile, $products)
    {
        $ids = [];
        foreach($products as $product) {
            $ids[] = $product['id'];
        }

        if ($ids) {
            $rows = Request::make()
                ->from(new ExternalProductLink())
                ->where([
                    'profile_id' => $profile['id']
                ])
                ->whereIn('product_id', $ids)
                ->exec()->fetchAll();

            $products_ext_data = [];
            foreach($rows as $row) {
                $row['ext_data_array'] = @json_decode($row['ext_data'], true) ?: [];
                $products_ext_data[$row['product_id']][$row['offer_id']] = $row;
            }

            foreach($products as $product) {
                foreach($product['offers']['items'] as $item) {

                    if (isset($products_ext_data[$product['id']][$item['id']])) {

                        //Добавляем информацию об имеющемся внешнем ID и флаге has_changed
                        $item['external_data'] = $products_ext_data[$product['id']][$item['id']];
                    }

                }
            }
        }

        return $products;
    }

    /**
     * Производит экспорт по API одного товара
     *
     * @param ExportProfile $profile
     * @param Product $product
     * @return int
     * @throws Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    private function exportOneProduct(ExportProfile $profile, Product $product)
    {
        $config = Loader::byModule($this);

        $counter = 0;
        foreach(clone $product['offers']['items'] as $offer_index => $offer) {
            //Фильтруем товары по наличию, если выставлено такое условие
            if (!$profile['only_available'] || $product->getNum($offer_index) > 0) {

                //Фильтруем комплектации по наличию изменений в них
                if (!$config['check_product_change']
                    || (!isset($offer['external_data']) || $offer['external_data']['has_changed'] == 1))
                {
                    $res = $this->exportOneProductOffer($profile, $product, $offer_index);

                    if ($res) {
                        $counter += 1;
                    }
                } else {
                    $this->log->append(t('Комплектация (ID:%id) не нуждается в обновлении', ['id' => $offer['id']]));
                }
            } else {
                $this->log->append(t('Комплктации (ID:%id) нет в наличии. Не проходит по фильтру.', ['id' => $offer['id']]));
            }
            if($profile['no_export_offers']) {
                break;
            }
        }

        return $counter;
    }

    /**
     * Возвращает структуру данных, необходимую для запроса в ВК для создания/обновления товара
     *
     * @param ExportProfile $profile Профиль экспорта
     * @param Product $product Товар
     * @param integer $offer_index Номер комплектации
     * @param Offer $offer Комплектация
     * @param array $ext_data Данные по загруженным фотографиям
     *
     * @return array
     *
     * @throws Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    private function getOfferRequestParams(ExportProfile $profile, Product $product, $offer_index, $offer, &$ext_data = [])
    {
        $cost_id = !empty($profile['export_cost_id']) ? $profile['export_cost_id'] : null;

        $request_params = [
            'name' => $this->getProductTitle($product, $offer),
            'description' => $this->getProductDescription($product),
            'price' => $product->getCost($cost_id, $offer_index, false, true),
            'url' => $product->getUrl(true). ($profile['url_params'] ? "?".$profile['url_params'] : "") .( $offer_index ? '#'.$offer_index : '' ),
            'deleted' => (int)!$this->isProductAvailable($product, $offer_index, $offer)
        ];

        $old_price = $product->getOldCost($offer_index, false, true);
        if ($old_price > 0) {
            $request_params['old_price'] = $old_price;
        }

        $request_params['main_photo_id'] = $this->uploadPhotos($profile, $product, $offer_index, true, $ext_data);
        $request_params['photo_ids'] = $this->uploadPhotos($profile, $product, $offer_index, false, $ext_data);

        return $request_params;
    }

    /**
     * Возвращает true, если товар можно купить, иначе false
     *
     * @return integer
     * @throws \RS\Orm\Exception
     * @throws Exception
     */
    private function isProductAvailable(Product $product, $offer_index, $offer)
    {
        $shop_config = Loader::byModule('shop');
        if (!$shop_config || !$shop_config['check_quantity']) {
            return true;
        }

        if ($product->getNum() <= 0) {
            return false;
        }
        return !$product->isOffersUse() || $product->getNum($offer_index) > 0;
    }

    /**
     * Производит экспорт по API одной комплектации товара
     *
     * @param ExportProfile $profile Профиль экспорта
     * @param Product $product Товар
     * @param integer $offer_index Индекс комплектации
     * @return bool
     *
     * @throws Exception
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    private function exportOneProductOffer(ExportProfile $profile, Product $product, $offer_index)
    {
        $offer = $product['offers']['items'][$offer_index];

        $ext_data = [];
        $request_params = [
            'category_id' => $this->getVkCategory($product, $profile)
        ];

        if (!$request_params['category_id']) {
            $this->log->append(t('Основная категория товара `%title` не сопоставлена с категорией Вконтакте. Экспорт товара невозможен.', [
                'title' => $product->getMainDir()->name
            ]));

            return false;
        }

        $request_params += $this->getOfferRequestParams($profile, $product, $offer_index, $offer, $ext_data);

        $hash = md5(serialize($request_params));

        if (!isset($offer['external_data']['hash']) || $offer['external_data']['hash'] != $hash) {
            if (isset($offer['external_data']['ext_id'])) {
                //Обновляем
                $result = $this->editItem(
                    $profile['group_id'],
                    $offer['external_data']['ext_id'],
                    $request_params
                );

                if ($result === self::EDIT_ERROR_DELETED_BY_VK) {
                    //Удаляем link, чтобы при следующей выгрузке комплектация создалась в ВК
                    Request::make()
                        ->delete()
                        ->from(new ExternalProductLink())
                        ->where([
                            'profile_id' => $profile['id'],
                            'product_id' => $product['id'],
                            'offer_id' => $offer['id'],
                            'ext_id' => $offer['external_data']['ext_id']
                        ])
                        ->exec();

                    $this->log->append(t('Товар был удален со стороны VK. При следущем обмене он будет создан заново.'));
                    $vk_id = null;
                } else {
                    $vk_id = $offer['external_data']['ext_id'];
                    $this->log->append(t('Комплектация успешно обновлена (ID:%id)', ['id' => $offer['id']]));
                }

            } else {
                //Создаем товар
                $vk_id = $this->addItem($profile['group_id'], $request_params);

                if ($vk_id) {
                    $this->log->append(t('Комплектация успешно создана в виде товара. Присвоен ID: %id', [
                        'id' => $vk_id
                    ]));
                } else {
                    $this->log->append(t('Комплектация не была создана'));
                }
            }

            if ($vk_id) {
                $link = new ExternalProductLink();
                $link['profile_id'] = $profile['id'];
                $link['product_id'] = $product['id'];
                $link['offer_id'] = $offer['id'];
                $link['ext_id'] = $vk_id;
                $link['ext_data_array'] = $ext_data;
                $link['has_changed'] = ExternalProductLink::HAS_CHANGED_NO;
                $link['hash'] = $hash;
                $link->replace();
            }
        } else {
            $this->log->append(t('Комплектация не изменялась с предыдущей выгрузки. Пропускаем.'));
        }

        return true;
    }


    /**
     * Загружает фотографии в ВК и возвращает их id.
     * Если флаг $is_main_photo = true, то будет загружено только первое изображение товара
     * Если флаг $is_main_photo = false, то будут загружены остальные фото, кроме первого
     *
     * @param ExportProfile $profile Профиль экспорта
     * @param Product $product Товар
     * @param integer $offer_index Индекс комплектации
     * @param bool $is_main_photo Если true, то граужается главное фото
     * @param array $ext_data дополнительные данные
     * @return array|mixed|null
     */
    private function uploadPhotos(ExportProfile $profile, Product $product, $offer_index, $is_main_photo, &$ext_data)
    {
        $offer = $product['offers']['items'][$offer_index];

        $image_ids = [];
        $count = 0;
        foreach($product->getImages() as $image) {

            //Если комплектация сопоставлена с фотографиями, то фильтруем
            if (!empty($offer['photos_arr']) && !in_array($image['id'], $offer['photos_arr'])) {
                continue;
            }

            if ($is_main_photo || $count > 0) {
                if ($profile['export_photo_originals']) {
                    $image_url = $image->getOriginalUrl();
                } else {
                    $image_url = $image->getUrl($profile['image_resize_width'], $profile['image_resize_height'], 'axy', false);
                }

                $this->log->append(t('< Начало загрузки %type изображения (%id) URL:%url', ['id' => $image['id'], 'url' => $image_url, 'type' => $is_main_photo ? t('главного') : '']));

                $image_filename = basename($image_url);
                $image_path = \Setup::$ROOT.$image_url;

                if (isset($offer['external_data']['ext_data_array']['photos'][(int)$is_main_photo][$image_filename])) {
                    $id = $offer['external_data']['ext_data_array']['photos'][(int)$is_main_photo][$image_filename];
                    $image_ids[] = $id;

                    $this->log->append(t('Изображение уже загружалось ранее, ID VK:%id', ['id' => $id]));
                } else {

                    if (!$profile['export_photo_originals']) {
                        //Принудительно запускаем создание миниатюры изображения
                        $image_path = \Setup::$ROOT . $image->getUrl($profile['image_resize_width'], $profile['image_resize_height'], 'axy', false, true);
                    }

                    $uri = $this->getMarketUploadServer($profile['group_id'], $is_main_photo);
                    if ($uri) {
                        $response = $this->uploadPhoto($profile, $uri, $image_path, $is_main_photo);
                        if ($response && isset($response[0]['id'])) {
                            $image_ids[] = $response[0]['id'];
                            $ext_data['photos'][(int)$is_main_photo][$image_filename] = $response[0]['id'];

                            $this->log->append(t('Изображение успешно загружено, ID VK: %id', [
                                'id' => $response[0]['id']
                            ]));
                        } else {
                            $this->log->append(t('Не удалось загрузить изображение'));
                        }
                    } else {
                        $this->log->append(t('Не удалось получить URL для загрузки фото'));
                    }
                }

                $this->log->append(t('> Конец загрузки изображения %id', ['id' => $image['id']]));
            }
            $count++;
            if ($is_main_photo || $count >= 5) break;
        }

        if (!$image_ids) {
            return $is_main_photo ? null : [];
        }

        return $is_main_photo ? $image_ids[0] : $image_ids;
    }

    /**
     * Возвращает имя товара
     *
     * @param Product $product Товар
     * @param Offer $offer Комплектация
     * @return string
     */
    private function getProductTitle(Product $product, Offer $offer)
    {
        $product_title = $product['title'];

        if ($offer['title'] != $product_title) {
            $product_title .= ' '.$offer['title'];
        }

        if (strlen($product_title) > 96) {
            $product_title = \RS\Helper\Tools::teaser($product_title, 96);
        }

        return html_entity_decode($product_title);
    }

    /**
     * Возвращает описание товара
     *
     * @param Product $product
     * @return mixed|\RS\Orm\Type\AbstractType|string
     */
    public function getProductDescription(Product $product)
    {
        if ($product['short_description'] != null) {
            $description = $product['short_description'];
        } else if ($product['description'] != null) {
            $description = $product['description'];
        } else {
            $description = 'No description';
        }

        return html_entity_decode(strip_tags($description));
    }

    /**
     * Возвращает ID категории ВК для товара
     *
     * @param Product $product - Товар
     * @param ExportProfile $profile - Профиль экспорта
     * @param bool $cache - Если true, значит данные будут возвращаться из кэша
     * @return integer|bool(false)
     * @throws Exception
     * @throws \RS\Db\Exception
     */
    private function getVkCategory(Product $product, ExportProfile $profile, $cache = true)
    {
        static $cache_data = [];

        $category_id = $product['maindir'];
        $profile_id = $profile['id'];

        if (!$cache || !isset($cache_data[$profile_id][$category_id])) {

            $dir = $product->getMainDir();
            do {
                $vk_cat_id = Request::make()
                    ->from(new VkCategoryLink())
                    ->where([
                        "profile_id" => $profile_id,
                        "dir_id" => $dir['id']
                    ])
                    ->exec()
                    ->getOneField('vk_cat_id');

                if ($vk_cat_id) break;

                $dir = $dir->getParentDir();
            } while($dir['id'] > 0);

            $cache_data[$profile_id][$category_id] = $vk_cat_id;
        }

        return $cache_data[$profile_id][$category_id];
    }

    /**
     * Возвращает client_id приложения ВК
     *
     * @return string
     */
    public function getAppClientId()
    {
        return $this['client_id'];
    }

    /**
     * Возвращает секретный ключ приложения ВК
     *
     * @return string
     */
    public function getAppSecretKey()
    {
        return $this['secret_key'];
    }

    /**
     * Получаем uri для Open Authorization в VK
     *
     * @param integer $profile_id ID профиля экспорта
     * @return string
     */
    public function getOauthUrl($profile_id)
    {
        $router = Manager::obj();

        $params = [
            'client_id' => $this->getAppClientId(),
            'display' => 'page',
            'scope' => 'market, offline, photos',
            'response_type' => 'code',
            'redirect_uri' => $router->getAdminUrl('SetApi', ['profile_id' => $profile_id], 'export-oauthvk', true),
            'v' => '5.95'
        ];

        return "https://oauth.vk.com/authorize?" . http_build_query($params);
    }

    /**
     * Возвращает путь к шаблону для рендеринга ячейки с действиями.
     *
     * @return string
     */
    public function getExportActionCellTemplate()
    {
        return '%export%/vk/vk_action_cell.tpl';
    }

    /**
     * Возвращает true, если профиль готов к экспорту по API, все необходимые данные заполнены.
     *
     * @return bool
     */
    public function validateDataForExchangeByApi()
    {
        if (!$this->getToken()) {
            return $this->addError(t('У профиля экспорта не получен AccessToken, обмен по API невозможен'));
        }
        return true;
    }

    /**
     * Возвращает ссылку на лог-файл, если таковой есть
     *
     * @return string | null
     */
    public function canSaveLog()
    {
        return true;
    }

    /**
     * Возвращает содержимое лог-файла
     *
     * @return mixed
     * @throws Exception
     */
    public function getLogContent()
    {
        $filepath = $this->getLogFilename($this->getExportProfile());
        if (file_exists($filepath)) {
            return file_get_contents($filepath);
        }

        return '';
    }

    /**
     * Очищает лог файл
     *
     * @return bool
     * @throws Exception
     */
    public function clearLog()
    {
        $filepath = $this->getLogFilename($this->getExportProfile());
        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return false;
    }

    /**
     * Возвращает true, если в настоящее время идет или запланирован обмен по API
     *
     * @return bool
     * @throws Exception
     */
    public function isRunning()
    {
        $api = new Api();
        $profile = $this->getExportProfile();

        return $profile['is_running']
                || $api->isPlannedExchange($profile);
    }

    /**
     * Останавливает омен по API, сбрасывает планировщик, очищает файл очереди, снимает флаг необходимости
     *
     * @return bool
     * @throws Exception
     * @throws \RS\Event\Exception
     */
    public function stopExchange()
    {
        $profile = $this->getExportProfile();

        $export_api = new Api();
        $export_api->endPlane($profile);

        $fetcher = new Fetcher($this->getExchangeCacheFilepath($profile['id']));
        if ($fetcher->issetBuffer()) {
            $fetcher->finish();
        }

        $profile['is_exporting'] = 0;
        $profile->update();

        return true;
    }
}