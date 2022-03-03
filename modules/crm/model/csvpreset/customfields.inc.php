<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvPreset;

/**
 * Пресет предоставляет для импорта/экспорта в CSV колонки пользовательских полей, заведенных пользователем в админке
 */
class CustomFields extends \RS\Csv\Preset\AbstractPreset
{
    public
        $row;

    protected
        $user_fields_manager,
        $link_id_field,
        $link_preset_id;

    /**
     * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
     *
     * @param integer $n - индекс в наборе строк $this->rows
     * @return array
     */
    function getColumnsData($n)
    {
        $custom_fields = (array)$this->schema->rows[$n][$this->link_id_field];

        $values_array = [];
        foreach($custom_fields as $key => $value) {
            $values_array[$this->id.'-customfields~'.$key] = $value;
        }

        return $values_array;
    }

    /**
     * Импортирует одну строку данных
     *
     * @return void
     */
    function importColumnsData()
    {
        $data = [];

        foreach($this->row as $key_info => $item) {
            $key_info = explode("~",trim($key_info)); //Получим информацию из поля
            if (isset($key_info[1])) {

                $key = $key_info[1];
                $item = trim($item);
                if ($item != '') {
                    $data[$key] = $item;
                }
            }
        }

        $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field] = $data;
    }

    /**
     * @param $user_fields_manager
     */
    function setUserFieldsManager($user_fields_manager)
    {
        $this->user_fields_manager = $user_fields_manager;
    }

    /**
     * Устанавливает связываемое поле из базового пресета
     *
     * @param string $field
     * @return void
     */
    function setLinkIdField($field)
    {
        $this->link_id_field = $field;
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
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    function getColumns()
    {
        $structure = $this->user_fields_manager->getStructure();

        $result = [];
        foreach($structure as $key => $field) {
            $result[$this->id.'-customfields~'.$key] = [
                'key' => 'customfields~'.$key,
                'title' => $field['title']
            ];
        }
        return $result;
    }
}