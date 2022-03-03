<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\CsvSchema;
use Main\Config\ModuleRights;
use Main\Model\CustomPreset\LangPhrase;
use Main\Model\LangApi;
use RS\AccessControl\Rights;

/**
 * Схема импорта/экспорта данных по переводам
 */
class Lang extends \RS\Csv\AbstractSchema
{
    protected $import_upload_right = ModuleRights::RIGHT_TRANSLATE_UPDATE;

    function __construct()
    {
        parent::__construct(new LangPhrase(), [], [
            'afterImport' => [$this, 'flushData']
        ]);
    }

    /**
     * Загружает и возвращает $limit строк с объектами выборки
     *
     * @param integer $offset Смещение, относительно начала
     * @param integer $limit Количество элементов
     * @return array
     */
    public function loadRows($offset, $limit)
    {
        $filters = LangApi::getSavedFilters();
        if (!isset($filters['lang'])) {
            return [];
        }

        $page = ($offset / $limit) + 1;
        $api = new LangApi();
        $api->setFilter($filters);
        return $api->getList($page, $limit);
    }

    /**
     * Записывает накопленные данные на диск
     */
    public function flushData(Lang $schema)
    {
        $schema->getPreset(0)->flushData();
    }
}