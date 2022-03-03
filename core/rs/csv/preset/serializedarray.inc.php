<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Добавляет к экспорту колонку для поля с сериализованными данными.
* Обеспечивает корректный импорт
*/
class SerializedArray extends AbstractPreset
{
    protected
        $keyval_delimiter = ':',
        $line_delimiter = ";\n",
        $line_delimiter_pattern = '/;[\r\n]+/',
        $link_preset_id,
        $link_foreign_field,
        $title;
        
    function loadData()
    {
        $this->rows = $this->schema->rows;
    }
    
    /**
    * Устанавливает название колонки
    * 
    * @param string $title
    * @return void
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
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
    * Устанавливает номер пресета, к которому линкуется текущий пресет
    * 
    * @param integer $n - номер пресета
    * @return void
    */
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }
    
    
    /**
    * Возвращает данные для вывода в CSV
    * 
    * @return array
    */
    function getColumns()
    {
        return [
            $this->id.'-serializedarray' => [
                'key' => 'serializedarray',
                'title' => $this->title
            ]
        ];
    }
    
    /**
    * Возвращает данные для вывода в CSV
    * 
    * @return array
    */
    function getColumnsData($n)
    {
        $this->row = [];
        foreach($this->getColumns() as $id => $column) {
            $serialized = $this->schema->rows[$n][$this->link_foreign_field];
            $data = @unserialize($serialized);
            $lines = [];
            if (is_array($data)) {
                foreach($data as $key => $value) {
                    $lines[] = $key.$this->keyval_delimiter.$value;
                }
            }
            $this->row[$id] = implode($this->line_delimiter, $lines);
        }
        return $this->row;
    }
    
    
    /**
    * Импортирует данные одной строки текущего пресета в базу
    * 
    * @return void
    */
    function importColumnsData()
    {
        if ($this->row['serializedarray']) {
            $lines = preg_split($this->line_delimiter_pattern, $this->row['serializedarray']);
            $arr = [];
            foreach($lines as $line) {
                @list($key, $value) = explode($this->keyval_delimiter, $line);
                $arr[$key] = $value;
            }
            
            $linked_preset = $this->schema->getPreset($this->link_preset_id);
            $linked_preset->row[$this->link_foreign_field] = serialize($arr);
        }
    }
}