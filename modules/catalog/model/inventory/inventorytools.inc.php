<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Inventory;

use \RS\Module\AbstractModel\BaseModel;
use \Catalog\Model\Orm\Inventory\DocumentProductsArchive;
use \Catalog\Model\Orm\Inventory\DocumentProducts;
use \Catalog\Model\Orm\Inventory\Inventorization;
use \Catalog\Model\Orm\Inventory\StartNum;
use \Catalog\Model\Orm\Inventory\Document;
use \Catalog\Model\Orm\Product;
use \Catalog\Model\Orm\Offer;
use \Catalog\Model\InventoryManager;

/**
 * Класс содержит функции для работы со складским учетом
 */
class InventoryTools extends BaseModel
{
    protected
        $step_data_file,
        $excluded = [];

    static
        $step_data_file_name = 'step_data.txt',
        $do_archive = "archive",
        $do_unarchive = "unarchive",
        $session_product_ids = 'session_product_ids';


    function __construct()
    {
        $this->step_data_file = \Setup::$ROOT.\Setup::$STORAGE_DIR.self::$step_data_file_name;
    }

    /**
     * Очащает массив исключенных товаров
     */
    public function clearExcluded()
    {
        $this->excluded = [];
    }

    /**
     * Возвращает массив исключенных товаров
     * @return array - массив исключенных товаров
     */
    public function getExcluded() {
        return $this->excluded;
    }

    /**
     *  Возвращает массив объектов пользователей, находящихся в группе "Поставщики"
     *
     * @return array
     */
    static function staticSelectProviders()
    {
        $result = [];
        if ($group_providers = \RS\Config\Loader::byModule('catalog')->provider_user_group) {
            $users = \RS\Orm\Request::make()
                ->select('U.*')
                ->from(new \Users\Model\Orm\User(), 'U')
                ->join(new \Users\Model\Orm\UserInGroup(), 'G.user = U.id', 'G')
                ->where([
                    'G.group' => $group_providers,
                ])
                ->objects(null, 'id');
            foreach($users as $user) {
                if($user['company']){
                    $company_line = $user['company'];
                    if($user['company_inn']){
                        $company_line .= t(' (ИНН: ').$user['company_inn'].(')');
                    }
                    $result[$user['id']] = $company_line;
                }else{
                    $result[$user['id']] = $user->getFio();
                }
            }
        }
        return array_merge([0 => t('Не выбрано')], $result);
    }

    /**
     *  Подготавлявает массив товара из одной строки csv
     *
     * @param string $row - строка csv файла товаров
     * @return array | bool
     */
    function prepareItemsFromCsv($row, $type = null)
    {
        $array = explode(';', trim($row));
        if(count($array) > 1){
            $config = \RS\Config\Loader::byModule("catalog");
            $field_identificate = $config['csv_id_fields_ic'];

            $uniq = $array[0];
            $product_identificator = $array[1];
            $offer_identificator = $array[2];
            $product = Product::loadByWhere([$field_identificate => $product_identificator]);

            if ($product->id) {
                if(!$offer_identificator){
                    $main_offer = $product->getMainOffer();
                    $offer_id = $main_offer['id'];
                }else{
                    $offer = Offer::loadByWhere([$field_identificate => $offer_identificator]);
                    $offer_id = $offer['id'];
                }
                $item = [
                    'uniq' => $uniq ? $uniq : DocumentApi::getUniq(),
                    'amount' => $array[3],
                    'product_id' => $product['id'],
                    'title' => $product['title'],
                    'offer_id' => $offer_id,
                ];

                if($type){
                    if($type == Inventorization::DOCUMENT_TYPE_INVENTORY){
                        $item['fact_amount'] = $item['amount'];
                    }
                }
                return $item;
            }else {
                $this->excluded[] = $product_identificator;
            }
        }
        return false;
    }

    /**
     *  Обнуляет остатки комплектаций
     *
     * @param $product_id
     */
    function setToZeroStocks($product_id)
    {
        \RS\Orm\Request::make()
            ->update(new \Catalog\Model\Orm\Offer())
            ->set(['num' => 0, 'reserve' => 0, 'waiting' => 0, 'remains' => 0])
            ->where(['product_id' => $product_id])
            ->exec();
        \RS\Orm\Request::make()
            ->update(new \Catalog\Model\Orm\Xstock())
            ->set(['stock' => 0, 'reserve' => 0, 'waiting' => 0, 'remains' => 0])
            ->where(['product_id' => $product_id])
            ->exec();
    }

    /**
     *  Колонки для импорта/экспорта товаров csv
     *
     * @return array
     */
    static function getPossibleIdFields()
    {
        return [
            'barcode' => "Артикул",
            'sku' => "Штрихкод",
            'xml_id' => "Идентификатор 1С",
            'title' => "Корткое название",
        ];
    }

    /**
     *  Скачать csv с товарами документа
     *
     * @param integer $document_id - id документа
     * @param integer $document_type - тип документа
     * @return string
     */
    function getProductsCsv($document_id, $document_type)
    {
        $config = \RS\Config\Loader::byModule($this);
        $field = $config['csv_id_fields_ic'];
        $document_api = new DocumentApi();
        $api = $document_api->getApiForDocumentType($document_type);

        $products = $api->getProductsByDocumentId($document_id);
        $columns = $this->getColumnsTitles($field);
        $csv = implode($columns, ";").PHP_EOL;
        $offers = [];

        foreach ($products as $product){
            $offers[] = $product['offer_id'];
        }
        $offer_barcodes = \RS\Orm\Request::make()
            ->select($field, 'id')
            ->from(new Offer())
            ->whereIn('id', $offers)
            ->exec()
            ->fetchSelected('id', $field);

        $inverse_amount = false;
        if($document_type == Document::DOCUMENT_TYPE_WRITE_OFF ||
            $document_type == Document::DOCUMENT_TYPE_RESERVE){
            $inverse_amount = true;
        }
        foreach ($products as $product){
            $offer = new Offer($product['offer_id']);
            $product_ = new Product($product['product_id']);
            $product_identificator = $product_[$field];
            if($document_type == \Catalog\Model\Orm\Inventory\Inventorization::DOCUMENT_TYPE_INVENTORY){
                $product['amount'] = $product['fact_amount'];
            }
            $array = [
                $product['uniq'],
                $product_identificator,
                $offer_barcodes[$offer['id']],
                $inverse_amount ? -$product['amount'] : $product['amount'],
            ];
            $csv .= implode($array, ";").PHP_EOL;
        }

        $mime     = 'text/csv';
        $app      = \RS\Application\Application::getInstance();
        $filename = "products.csv";
        $app->cleanOutput();
        $app->headers->addHeaders([
            'Content-Type'              => $mime,
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename="'.$filename.'"',
            'Connection'                => 'close'
        ]);
        $csv = iconv("utf-8", "windows-1251", $csv);
        return $csv;
    }

    /**
     *  Получить пример csv файла
     */
    function GetExampleCsv()
    {
        $config = \RS\Config\Loader::byModule($this);
        $field = $config['csv_id_fields_ic'];
        $products_amount = 5;
        $columns = $this->getColumnsTitles($field);
        $csv = implode(";", $columns).PHP_EOL;

        for ($i = 0; $i < $products_amount; $i++){
            $array = [
                DocumentApi::getUniq(),
                rand(10000,99999),
                rand(10000,99999),
                rand(1, 10),
            ];
            $csv .= implode(";", $array).PHP_EOL;
        }
        $file = "products.csv";
        header('X-SendFile: ' . realpath($file));
        header('Content-Disposition: attachment; filename=' . basename($file));
        $csv = iconv("utf-8", "windows-1251", $csv);
        echo $csv;
    }

    /**
     *  Получить названия колонок для экспорта csv
     *
     * @param string $field - поле, по которому идентифицировать товар
     * @return array
     */
    function getColumnsTitles($field)
    {
        $fields = $this->getPossibleIdFields();
        return [
            t("Уникальный id"),
            $fields[$field] . t(" товара"),
            $fields[$field] . t(" комплектации"),
            t("Количество"),
        ];
    }

    /**
     *  Проверить возможность архивирования документов
     *
     * @param string $date - дата
     * @param string $do - архивировать или разархивировать
     * @return array|bool
     */
    function checkArchiveErrors($date, $do)
    {
        $archive_params = $this->getArchiveParams($do);
        $amount = $this->getDocsAmountToArchive($archive_params, $date);
        if(!$amount){
            return ['error' => t('Нет документов на указанную дату')];
        }
        return true;
    }

    /**
     *  Архивировать документы пошагово
     *
     * @param string $date - дата
     * @param integer $step - этап операции
     * @param string $do - архивировать или разархивировать
     * @param array $params - параметры операции
     * @return bool|array
     */
    function archiveProducts($date, $step, $do, $params)
    {
        $archive_params = $this->getArchiveParams($do);
        if(!$step){
            $docs_id = $this->getDocsIdToArchive($archive_params, $date);
            $params['docs_id'] = $docs_id;
            return $params;
        }
        elseif($step == 1) {
            $this->moveProducts($params['docs_id'], $archive_params);
            return $params;
        }
        elseif($step == 2){
            $this->cleanMovedProducts($archive_params, $params['docs_id']);
            return $params;
        }
        elseif($step == 3){
            $this->updateStartNum();
            return $params;
        }
        elseif($step == 4){
            $this->markArchivedDocuments($archive_params, $params['docs_id']);
            return $params;
        }
        return true;
    }

    /**
     *  Сохраняет результат выполнения одного шага архивации
     *
     * @param array $params
     */
    function setStepData($params)
    {
        file_put_contents($this->step_data_file, serialize($params));
    }

    /**
     *  Получить результат последнего шага архивации
     *
     * @return mixed
     */
    function getStepData()
    {
        return unserialize(file_get_contents($this->step_data_file));
    }

    /**
     *  Получить параметры для архивации/разархивации документов
     *
     * @param string $do
     * @return array
     */
    function getArchiveParams($do)
    {
        $params = [];
        if($do == self::$do_archive){
            $params['sign'] = "<";
            $params['orm_from'] = new DocumentProducts();
            $params['orm_to'] = new DocumentProductsArchive();
            $params['archived'] = 0;
        }elseif($do == self::$do_unarchive){
            $params['sign'] = ">";
            $params['orm_from'] = new DocumentProductsArchive();
            $params['orm_to'] = new DocumentProducts();
            $params['archived'] = 1;
        }
        return $params;
    }

    /**
     *  Получить количество документов подходящих для архивации
     *
     * @param array $params
     * @param string $date
     * @return mixed
     */
    function getDocsAmountToArchive($params, $date)
    {
        $q = \RS\Orm\Request::make()
            ->select('count(*) as doc_amount')
            ->from(new Document());

        if($date != 'all'){
            $q->where(sprintf("date %s '$date' and archived = %d", $params['sign'], $params['archived']));
        }

        $doc_amount = $q->whereIn('type', [
                Document::DOCUMENT_TYPE_WAITING,
                Document::DOCUMENT_TYPE_RESERVE,
                Document::DOCUMENT_TYPE_ARRIVAL,
                Document::DOCUMENT_TYPE_WRITE_OFF,
        ])
            ->exec()
            ->getOneField('doc_amount');
        return $doc_amount;
    }

    /**
     *  Получить id документов подходящих для архивации
     *
     * @param array $params
     * @param string $date
     * @return array
     */
    function getDocsIdToArchive($params, $date)
    {
        $q = \RS\Orm\Request::make()
            ->select('id')
            ->from(new Document());

        if($date != 'all'){
            $q->where(sprintf("date %s '$date' and archived = %d", $params['sign'], $params['archived']));
        }

        $ids = $q->whereIn('type', [
                Document::DOCUMENT_TYPE_WAITING,
                Document::DOCUMENT_TYPE_RESERVE,
                Document::DOCUMENT_TYPE_ARRIVAL,
                Document::DOCUMENT_TYPE_WRITE_OFF,
        ])
            ->exec()
            ->fetchSelected(null, 'id');
        return $ids;
    }

    /**
     *  Перемещение товаров между таблицами архива и документов
     *
     * @param array $docs_id_arr
     * @param array $params
     * @return void
     */
    function moveProducts($docs_id_arr, $params)
    {
        $table_to = $params['orm_to']->_getTable();
        $table_from = $params['orm_from']->_getTable();
        $docs_id_string = implode(',', $docs_id_arr);
        $query = "INSERT INTO $table_to SELECT * FROM $table_from WHERE document_id in ($docs_id_string)";
        \RS\Db\Adapter::sqlExec($query);
    }

    /**
     *  Обновляет количество архивных товаров
     *
     * @return void
     */
    function updateStartNum()
    {
        \RS\Orm\Request::make()
            ->delete()
            ->from(new StartNum())
            ->exec();

        $manager = new InventoryManager();
        $items = $this->getItemsInArchive();
        foreach ($items as $item){
            $amounts_arr = $manager->getAmountByDocuments($item['product_id'], $item['offer_id'], $item['warehouse'], new DocumentProductsArchive());
            $num = $manager->getNum($amounts_arr);
            $start_num = new StartNum();
            $start_num['product_id'] = $item['product_id'];
            $start_num['offer_id'] = $item['offer_id'];
            $start_num['warehouse_id'] = $item['warehouse'];
            $start_num['stock'] = $num;
            $start_num['remains'] = $amounts_arr['remains_sum'];
            $start_num['reserve'] = $amounts_arr['reserve_sum'];
            $start_num['waiting'] = $amounts_arr['waiting_sum'];
            $start_num->replace();
        }
    }

    /**
     *  Удалить товары, которые были перемещены из таблицы
     *
     * @param array $params
     * @param array $docs_id_arr
     */
    function cleanMovedProducts($params, $docs_id_arr)
    {
        \RS\Orm\Request::make()
            ->delete()
            ->from($params['orm_from'])
            ->whereIn('document_id', $docs_id_arr)
            ->exec();
    }

    /**
     *  Отметить архивные документы
     *
     * @param array $params
     * @param array $docs_id_arr
     */
    function markArchivedDocuments($params, $docs_id_arr)
    {
        \RS\Orm\Request::make()
            ->update()
            ->from(new Document())
            ->set(['archived' => $params['archived'] == 1 ? 0 : 1])
            ->whereIn('id', $docs_id_arr)
            ->exec();
    }

    /**
     *  Получить все товары из архива
     *
     * @return array
     */
    function getItemsInArchive()
    {
        $result = \RS\Orm\Request::make()
            ->select('product_id, offer_id, warehouse')
            ->from(new DocumentProductsArchive())
            ->groupby('offer_id, warehouse')
            ->exec()
            ->fetchSelected(null);
        return $result;
    }

    /**
     *  Изменить тип документа
     *
     * @param integer $document_id - id документа
     * @param string $type_from - предыдущий тип документа
     * @param string $type_to - тип документа, который нужно установить
     */
    function changeDocumentType($document_id, $type_from, $type_to)
    {
        $api = new DocumentApi();
        $document = new Document($document_id);
        $manager = new \Catalog\Model\DocumentLinkManager();
        $links = $manager->getLinks($document);
        foreach ($links as $link) {
            $manager->changeLinkType($link, $type_from, $type_to);
        }
        $document['type'] = $type_to;
        $document['items'] = $api->getProductsByDocumentId($document_id);
        $document->update();
    }
}