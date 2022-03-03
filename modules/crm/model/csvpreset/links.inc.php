<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvPreset;
use \RS\Csv\Preset\AbstractPreset;

/**
 * Класс позволяет импортировать/экспортировать связи объекта с другими объектами
 */
class Links extends AbstractPreset
{
    public
        $row;

    protected
        $line_delimiter = ";",
        $link_source_type,
        $link_id_field,
        $link_foreign_field,
        $link_preset_id;

    /**
     * Определяет foreign key другого объекта
     *
     * @param string $field
     * @return void
     */
    function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
    }

    /**
     * Устанавливает тип исходного объекта, к которому линкуются связи
     *
     * @param $link_source_type
     */
    function setLinkSourceType($link_source_type)
    {
        $this->link_source_type = $link_source_type;
    }

    /**
     * @param $link_source_type
     * @return mixed
     */
    function setLinkIdField($link_id_field)
    {
        return $this->link_id_field = $link_id_field;
    }

    /**
     * Устанавливает номер базового пресета
     *
     * @param integer $id
     * @return void
     */
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }

    /**
     * Устанавливает разделитель, которым визуально будут отделены связи
     *
     * @param $line_delimiter
     */
    function setLineDelimiter($line_delimiter)
    {
        $this->line_delimiter = $line_delimiter;
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
                'source_type' => $this->link_source_type,
                'source_id' => $this->schema->rows[$n][$this->link_id_field]
            ])
            ->objects();

            $title_lines = [];
            $lines = [];

            foreach($links as $link) {
                $title_lines[] = $link->getLinkTypeObject()->getLinkText();
                $lines[] = $link['link_type'].':'.$link['link_id'];
            }

            $this->row[$this->id.'-links-title'] = implode($this->line_delimiter, $title_lines);
            $this->row[$this->id.'-links'] = implode($this->line_delimiter, $lines);

        return $this->row;

    }

    /**
     * Импортирует одну строку данных
     *
     * @return void
     */
    function importColumnsData()
    {
        if (isset($this->row['links'])) {
            $data = [];
            $lines = explode($this->line_delimiter, $this->row['links']);
            foreach($lines as $line) {
                if (preg_match('/^(.+)\:(.+?)$/', trim($line), $match)) {
                    if (!isset($data[$match[1]])) {
                        $data[$match[1]] = [];
                    }
                    $data[$match[1]][] = $match[2];
                }
            }

            $this->schema->getPreset($this->link_preset_id)->row[$this->link_foreign_field] = $data;
        }
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns()
    {
        return [
            $this->id.'-links-title' => [
                'key' => 'links-title',
                'title' => t('Список наименований связей')
            ],
            $this->id.'-links' => [
                'key' => 'links',
                'title' => t('Технический список связей')
            ],
        ];
    }
}