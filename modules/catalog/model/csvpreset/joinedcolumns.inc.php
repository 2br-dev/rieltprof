<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

/**
 * Добавляет к экспорту колонки соответствующие свойствам ORM объекта.
 * Самый простой набор колонок. В качестве названия колонок выступают названия свойств Orm объекта
 */
class JoinedColumns extends \RS\Csv\Preset\Base
{
    protected
        $modificate_columns = [],
        $joinFields = [];


    /**
     * Возвращает колонки, которые добавляются текущим набором
     */
    function getColumns()
    {
        $result = [];
        foreach($this->joinFields as $key => $title) {
            $result['join'.'-'.$key] = [
                'key' => $key,
                'title' => $title
            ];
        }
        return $result;
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
            $value = trim($this->rows[$n][$column['key']]);
            if(isset($this->modificate_columns[$column['key']])){
                $value = call_user_func($this->modificate_columns[$column['key']], $value);
            }
            $this->row[$id] = trim($value);
        }
        return $this->row;
    }

    function setJoinFields($fields)
    {
        $this->joinFields = $fields;
    }

    function setModificateColumns($modificate_columns)
    {
        $this->modificate_columns = $modificate_columns;
    }
}