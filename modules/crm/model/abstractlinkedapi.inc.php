<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Model\Orm\Link;
use RS\Application\Auth;
use RS\Orm\Request;
use Crm\Model\Orm\CustomData;
use RS\Config\Loader;
use RS\Html\Table\Control as TableControl;
use RS\Html\Table\Type as TableType;

/**
 * Базовый класс API для объектов, имеющих связи с другими объектами
 */
abstract class AbstractLinkedApi extends \RS\Module\AbstractModel\EntityList
{
    protected
        $creator_user_id_field = 'creator_user_id',
        $implementer_user_id_field = '';


    /**
     * Устанавливает фильтр по типу связи с объектом. если вызвано более 1 раза, то объединяется чрез ИЛИ
     *
     * @param $link_type
     * @param $link_id
     * @return void
     * @throws \RS\Exception
     */
    function addFilterByLink($link_type, $link_id)
    {
        $link = new Link();
        if ($this->queryObj()->issetTable($link)) {

            $this->queryObj()
                ->where([
                    'link_type' => $link_type,
                    'link_id' => $link_id,
                    'source_type' => $this->getElement()->getLinkSourceType()
                ], null, 'OR');

        } else {

            $this->queryObj()
                ->join($link, 'L.source_id = A.id', 'L')
                ->where([
                    'link_type' => $link_type,
                    'link_id' => $link_id,
                    'source_type' => $this->getElement()->getLinkSourceType()
                ]);

        }
    }

    /**
     * Удаляет все объекты, связанные с объектом $link_type, $link_id
     *
     * @param string $link_type
     * @param integer $link_id
     * @return bool
     */
    function removeAllByLink($link_type, $link_id)
    {
        if ($this->noWriteRights()) return false;

        $links = Request::make()
            ->from(new Link())
            ->where([
                'link_type' => $link_type,
                'link_id' => $link_id,
                'source_type' => $this->getElement()->getLinkSourceType()
            ])->objects();

        foreach($links as $link) {
            $link->deleteWithSource($this->getElement());
        }

        return true;
    }

    /**
     * Удаляет необходимые связанные элементы по ID
     *
     * @param string $link_type тип связи
     * @param integer $link_id id связи
     * @param array $ids
     * @return bool
     */
    function removeByIds($link_type, $link_id, $ids)
    {
        if (!$ids || $this->noWriteRights()) return false;

        $links = Request::make()
            ->from(new Link())
            ->where([
                'link_type' => $link_type,
                'link_id' => $link_id,
                'source_type' => $this->getElement()->getLinkSourceType()
            ])
            ->whereIn('source_id', $ids)
            ->objects();

        foreach($links as $link) {
            $link->deleteWithSource($this->getElement());
        }

        return true;
    }

    /**
     * Возвращает пользовательские колонки, которые следует добавить в таблицу
     *
     * @return array
     */
    function getCustomTableColumns($field_manager)
    {
        $columns = [];
        foreach($field_manager->getStructure() as $field) {

            $column_name = 'custom_'.$field['alias'];
            $options = [
                'hidden' => true,
                'custom_field_alias' => $field['alias'],
                'sortable' => SORTABLE_BOTH
            ];

            if ($field['type'] == 'bool') {
                $column = new TableType\StrYesno($column_name, $field['title'], $options);
            } else {
                $column = new TableType\Text($column_name, $field['title'], $options);
            }

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * Добавляет в выборку необходимые данные по включенным пользовательским полям
     *
     * @param TableControl $table_control Объект управления таблицей на странице
     * @param string $object_type_alias Идентификатор объекта, к которому привязаны пользовательские поля
     * @return void
     */
    function addCustomFieldsData(TableControl $table_control, $object_type_alias)
    {
        $table_control->fill();
        $table = $table_control->getTable();

        //Определяем включенные колонки
        $add_columns = [];
        foreach($table->getColumns() as $n => $col) {
            if (isset($col->property['custom_field_alias'])) {
                if (!$col->isHidden()) {
                    $add_columns[] = $col->property['custom_field_alias'];
                }
            }
        }

        //Модифицируем запрос на выборку, добавляем в него нужные данные
        foreach($add_columns as $column_alias) {
            $alias = 'XC'.$column_alias;
            $this->queryObj()
                ->select("$alias.value as custom_".$column_alias)
                ->leftjoin(new CustomData(), "$alias.object_id=A.id 
                                                AND $alias.object_type_alias='{$object_type_alias}'
                                                AND $alias.field='{$column_alias}'", $alias);
        }
    }
}