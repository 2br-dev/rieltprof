<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model\CsvPreset;

use RS\Csv\Preset\AbstractPreset;

class GeoPoint extends AbstractPreset
{
    protected $link_preset_id;
    protected $title;
    protected $field;
    protected $longitude_field;

    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    public function getColumnsData($n)
    {
        $orm = $this->schema->rows[$n];

        if ($this->longitude_field === null) {
            $field = $orm['__' . $this->field];
            $this->longitude_field = $field->getFieldLongitudeName();
        }

        return [$this->id . '-geopoint' => $orm[$this->field] . ', ' . $orm[$this->longitude_field]];
    }

    /**
     * Импортирует одну строку данных
     *
     * @return void
     */
    public function importColumnsData()
    {
        if ($this->longitude_field === null) {
            $orm = $this->schema->getPreset($this->link_preset_id)->getOrmObject();
            $field = $orm['__' . $this->field];
            $this->longitude_field = $field->getFieldLongitudeName();
        }

        $value = $this->row['geopoint'];
        $parts = explode(',', $value);

        $this->schema->getPreset($this->link_preset_id)->row[$this->field] = trim($parts[0]);
        $this->schema->getPreset($this->link_preset_id)->row[$this->longitude_field] = trim($parts[1]);
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            $this->id . '-geopoint' => [
                'key' => 'geopoint',
                'title' => $this->title,
            ]
        ];
    }

    protected function setLinkPresetId($link_preset_id)
    {
        $this->link_preset_id = $link_preset_id;
    }

    /**
     * Устанавливает название экспортной колонки
     *
     * @param mixed $title
     */
    protected function setTitle($title)
    {
        $this->title = $title;
    }

    protected function setField($field)
    {
        $this->field = $field;
    }
}
