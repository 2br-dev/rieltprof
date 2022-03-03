<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\CustomPreset;

use Main\Model\LangApi;
use RS\Csv\Preset\AbstractPreset;

/**
 * Пресет обеспечивает формирование колонок для импорта/экспорта фраз для перевода
 */
class LangPhrase extends AbstractPreset
{
    /**
     * @var array Содержит временный буфер фраз, сгруппированных по файлам
     */
    protected $phrase_buffer = [];
    protected $lang_api;

    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->lang_api = new LangApi();
    }

    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    public function getColumnsData($n)
    {
        $this->row = [];
        foreach ($this->getColumns() as $id => $column) {
            //Если поле закодировано, то раскодируем его
            $value = $this->rows[$n][$column['key']] ?? '';
            $this->row[$id] = $value;
        }
        return $this->row;
    }

    /**
     * Импортирует одну строку данных. Складывает данные в буфер,
     * а буфер будет сохранен при вызове flushData
     *
     * @return void
     */
    public function importColumnsData()
    {
        if (!empty($this->row['module']) && !empty($this->row['type']) && !empty($this->row['lang'])) {
            $source_id = $this->row['module'].'-'.$this->row['lang'].'-'
                            .$this->row['type'].'-'.$this->lang_api->getHashSourcePhrase($this->row['source']);

            $this->phrase_buffer[$source_id] = [
                'source' => $this->row['source'],
                'translate' => $this->row['translate'],
            ];
        }
    }

    /**
     * Сохраняет данные на диск
     */
    public function flushData()
    {
        if ($this->phrase_buffer) {
            $this->lang_api->saveTranslates($this->phrase_buffer, true);
            $this->phrase_buffer = [];
        }
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    public function getColumns()
    {
        $result = [
            $this->id.'-lang' => [
                'key' => 'lang',
                'title' => t('Язык')
            ],
            $this->id.'-module' => [
                'key' => 'module',
                'title' => t('Модуль')
            ],
            $this->id.'-type' => [
                'key' => 'type',
                'title' => t('Тип перевода')
            ],
            $this->id.'-source' => [
                'key' => 'source',
                'title' => t('Исходная фраза')
            ],
            $this->id.'-translate' => [
                'key' => 'translate',
                'title' => t('Перевод')
            ],
        ];
        return $result;
    }

    /**
     * Не используется, так как данные не связаны с выборкой из БД
     */
    public function getSelectRequest()
    {}

    /**
     * Загружает данные перед экспортом
     *
     * @return void
     */
    public function loadData()
    {
        $this->rows = $this->schema->rows;
    }
}