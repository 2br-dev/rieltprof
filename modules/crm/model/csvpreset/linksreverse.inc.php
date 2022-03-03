<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvPreset;

use RS\Csv\Preset\AbstractPreset;

/**
 * Класс позволяет экспортировать связь чужих объектов с вашим объектом
 * Класс не поддерживает импорт
 */
class LinksReverse extends AbstractPreset
{
    public $row = [];

    protected
        $export_object,
        $export_format_callback,
        $line_delimiter = '; ',
        $link_type,
        $link_id_field,
        $source_type,
        $column_title;

    /**
     * Устанавливает разделитель между записями
     *
     * @param $line_delimiter
     */
    function setLineDelimiter($line_delimiter)
    {
        $this->line_delimiter = $line_delimiter;
    }

    /**
     * Устанавливает заголовок для колонки
     */
    function setColumnTitle($column_title)
    {
        $this->column_title = $column_title;
    }

    /**
     * Устанавливает link_type, который использован для связи с текущим объектом
     *
     * @param string $link_type
     */
    function setLinkType($link_type)
    {
        $this->link_type = $link_type;
    }

    /**
     * Устанавливает тип связанного объекта
     *
     * @param $source_type
     * @return void
     */
    function setSourceType($source_type)
    {
        $this->source_type = $source_type;
    }

    /**
     * Устанвливает объект, который связан с текущим объектом
     *
     * @param $object
     */
    function setExportObject($object)
    {
        $this->export_object = $object;
    }

    /**
     * Устанвливает, в каком поле у основного объекта хранится ID
     */
    function setLinkIdField($link_id_field)
    {
        $this->link_id_field = $link_id_field;
    }

    /**
     * Устанавливает функцию, форматирующую отображение данных
     *
     * @param $callback $callback
     */
    function setExportFormatCallback($callback)
    {
        $this->export_format_callback = $callback;
    }

    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    function getColumnsData($n)
    {
        $this->row = [];

        $links = \RS\Orm\Request::make()
            ->from(new \Crm\Model\Orm\Link())
            ->where([
                'source_type' => $this->source_type,
                'link_id' => $this->schema->rows[$n][$this->link_id_field],
                'link_type' => $this->link_type
            ])
            ->objects();

        $title_lines = [];

        foreach($links as $link) {
            $object = $link->loadSourceObject($this->export_object);
            $title_lines[] = call_user_func($this->export_format_callback, $object, $this);
        }

        $this->row[$this->id.'-linksreverse-title'] = implode($this->line_delimiter, $title_lines);

        return $this->row;
    }

    /**
     * Импортирует одну строку данных
     *
     * @return void
     */
    function importColumnsData()
    {
        //Не участвует в импорте
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns()
    {
        return [
            $this->id.'-linksreverse-title' => [
                'key' => 'linksreverse-title',
                'title' => $this->column_title
            ],
        ];
    }
}