<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
 * ReadyScript (http://readyscript.ru)
 *
 * @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
 * @license http://readyscript.ru/licenseAgreement/
 */
namespace Catalog\Model\CsvPreset;
use Catalog\Model\Orm\Property\Item;
use \Catalog\Model\Orm\Property\ItemValue;
use Catalog\Model\PropertyApi;
use RS\Orm\Request;

/**
 * Добавляет к экспорту колонки, связанные с характеристиками и их значениями у категорий
 */
class DirProperty extends \RS\Csv\Preset\AbstractPreset
{
    const ROOT_DIR_ID = 'root_dir';

    protected static
        $props,
        $groups,
        $index;

    protected
        $delimiter = ";",
        $value_delimiter = "|",
        $id_field = 'id',
        $link_id_field = 'id',
        $link_preset_id = 0,
        $mask = '({group})({public},{useval},{is_expanded}){property}:{value}',
        $import_pattern = '(%s)(%s,%s,%s)%s:%s',
        $mask_fields = [],
        $mask_pattern,
        $title,
        $array_field = 'prop',
        $manylink_foreign_id_field = 'prop_id',
        $manylink_id_field = 'group_id',
        $manylink_orm,
        $list_values_orm;

    protected static
        $existed_group = null,
        $existed_prop = null,
        $existed_values = null;

    function __construct($options)
    {
        $defaults = [
            'ormObject' => new \Catalog\Model\Orm\Property\Item(),
            'manylinkOrm' => new \Catalog\Model\Orm\Property\Link(),
            'listValuesOrm' => new \Catalog\Model\Orm\Property\ItemValue(),
            'mask' => '({group})({public},{usevalue},{is_expanded}){title}:{value}',
            'multisite' => true
        ];
        parent::__construct($options + $defaults);
        $this->loadProperty();
    }


    /**
     * Добавляет дополнительное условие в виде site_id = ТЕКУЩИЙ САЙТ, если задано true
     *
     * @param bool $bool
     * @return void
     */
    function setMultisite($bool)
    {
        $this->is_multisite = $bool;
    }

    /**
     * Возвращает условие для добавления к Where, если установлено свойство multisite => true
     *
     * @return array
     */
    function getMultisiteExpr()
    {
        return $this->is_multisite ? ['site_id' => \RS\Site\Manager::getSiteId()] : [];
    }

    /**
     * Загружает справочники характеристик и групп
     *
     * @return void
     */
    function loadProperty()
    {
        if (!isset(self::$props)) {
            self::$props = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Property\Item())
                ->where($this->getMultisiteExpr() ?: null)
                ->objects(null, 'id');

            self::$groups = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Property\Dir())
                ->where($this->getMultisiteExpr() ?: null)
                ->objects(null, 'id');

            foreach(self::$props as $prop) {
                $group_name = isset(self::$groups[$prop['parent_id']]) ? self::$groups[$prop['parent_id']]['title'] : '';
                self::$index["($group_name)".$prop['title']] = $prop['id'];
            }
        }
    }

    /**
     * Устанавливает ORM объект связки многие ко многим
     *
     * @param \RS\Orm\AbstractObject $orm
     * @return void
     */
    protected function setManylinkOrm(\RS\Orm\AbstractObject $orm)
    {
        $this->manylink_orm = $orm;
    }

    /**
     * Устанавливает ORM объект значения характеристики
     *
     * @param \RS\Orm\AbstractObject $orm
     */
    protected function setListValuesOrm(\RS\Orm\AbstractObject $orm)
    {
        $this->list_values_orm = $orm;
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
     * Устанавливает название экспортной колонки
     *
     * @param mixed $title
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Устанавливает маску для формирования строки из данных в CSV файле
     *
     * @param string $mask
     * @return void
     */
    protected function setMask($mask)
    {
        $this->mask = $mask;
        $this->mask_fields = [];
        if (preg_match_all('/\{(.*?)\}/', $this->mask, $match)) {
            foreach($match[1] as $field) {
                $this->mask_fields[] = $field;
            }
        }
        $pattern = preg_replace_callback('/(\{.*?\})/', function($match) {
            $field = trim($match[1], '{}');
            return "(?P<{$field}>.*?)";
        }, quotemeta($this->mask));
        $this->mask_pattern = '/^'.$pattern.'$/';
    }
    /**
     * Загружает связанные данные
     *
     * @return void
     */
    function loadData()
    {
        $this->row = [];
        if ($this->schema->ids) {
            $this->rows = \RS\Orm\Request::make()
                ->select('L.*, V.value as val_list_id')
                ->from($this->manylink_orm, 'L')
                ->leftjoin($this->list_values_orm, 'V.id = L.val_list_id ', 'V')
                ->whereIn($this->manylink_id_field, $this->schema->ids)
                ->objects(null, $this->manylink_id_field, true);

        }
    }

    /**
     * Возвращает колонки, предоставляемые данным пресетом
     *
     * @return array
     */
    function getColumns()
    {
        return [
            $this->id.'-properties' => [
                'key' => 'properties',
                'title' => $this->title
            ]
        ];
    }

    /**
     * Возвращает данные для экспорта
     *
     * @param integer $n - номер строки
     * @return array
     */
    function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_id_field];
        $data = [];

        $prop_api = new PropertyApi();
        $properties = $prop_api->getGroupProperty($id, false);

        foreach ($properties as $grouped) {
            $properties_by_group = $grouped['properties'];
            foreach ($properties_by_group as $property) {
                if (!isset($data[$property['id']])) {
                    $values = $property['value'];
                    if (is_array($values) && !empty($values)) {
                        $values = Request::make()
                            ->select('value')
                            ->from(ItemValue::_getTable())
                            ->whereIn('id', $values)
                            ->exec()->fetchSelected(null, 'value');
                    }
                    $values = (array)$values;
                    $data[$property['id']] = sprintf($this->import_pattern,
                            isset(self::$groups[$property['parent_id']]) ? self::$groups[$property['parent_id']]['title'] : '',
                            (int)$property['public'],
                            (int)$property['useval'],
                            (int)$property['is_expanded'],
                            $property['title'],
                            implode('|', $values)
                    );
                }
            }
        }

        return [$this->id.'-properties' => implode(";\n", $data)];
    }

    /**
     * Импортирует колонки данного пресета
     *
     * @return void
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     */
    function importColumnsData()
    {
        if (isset($this->row['properties']) && !empty($this->row['properties'])) {
            $items = explode($this->delimiter, $this->row['properties']);
            foreach ($items as &$item) {
                preg_match_all($this->mask_pattern, trim($item), $item);
                foreach ($item as &$item_val) {
                    if (is_array($item_val)) $item_val = reset($item_val);
                }
            }

            $this->loadImportData();
            $this->checkGroup($items);
            $this->checkProperty($items);
            $this->checkValues($items);

            $this->sortByPropId($items);

            $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $items;
        }
    }

    /**
     * Проверяет наличие группы харарктеристик. При отсутствии создаёт группу
     * @param $items
     */
    private function checkGroup(&$items)
    {
        foreach ($items as &$item) {
            if (empty($item['group'])) {
                $item['group'] = self::ROOT_DIR_ID;
            }
            $dir = self::$existed_group[$item['group']] ?? $this->createPropDir($item);

            $item['group_id'] = $dir['id'];
        }
    }

    /**
     * Проверяет наличие характеристики. При отсутствии создаёт
     * @param $items
     */
    private function checkProperty(&$items)
    {
        foreach ($items as &$item) {
            $prop = self::$existed_prop[$item['title']] ?? $this->createPropItem($item);

            $item['prop_id'] = $item['id'] = $prop['id'];
            $item['type'] = $prop['type'];
        }
    }

    /**
     * Проверяет наличие значений для характеристики. При отсутствии создаёт
     * @param $items
     * @throws \RS\Event\Exception
     */
    private function checkValues(&$items)
    {
        $allow_types = Item::getAllowTypeData();

        foreach ($items as &$item) {
            if ($allow_types[$item['type']]['is_list']) {
                $item['value'] = explode('|', $item['value']);
                foreach ($item['value'] as &$value) {
                    if ($value !== '') {
                        $value_id = self::$existed_values[$item['prop_id']][$value] ?? $this->createPropValue($item, $value);

                        $value = $value_id;
                    }
                }
            }
        }
    }

    /**
     * Сортирует по prop_id
     * @param $items
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     */
    private function sortByPropId(&$items)
    {
        $sorted_items = [];
        foreach ($items as $item) {
            $sorted_items[$item['prop_id']] = $item;
        }

        $items = $sorted_items;
        unset($sorted_items);
    }

    /**
     * @param $item
     * @return \Catalog\Model\Orm\Property\Dir
     */
    public function createPropDir($item) : \Catalog\Model\Orm\Property\Dir
    {
        $dir = new \Catalog\Model\Orm\Property\Dir();
        $dir['title'] = $item['group'];

        self::$existed_group[$dir['title']] = $dir->getValues();

        $dir->insert();

        return $dir;
    }

    /**
     * @param $item
     * @return Item
     */
    public function createPropItem($item) : Item
    {
        $prop = new \Catalog\Model\Orm\Property\Item();
        $prop['title'] = $item['title'];
        $prop['type'] = 'string';
        $prop['parent_id'] = $item['group_id'];

        self::$existed_prop[$prop['title']] = $prop->getValues();

        $prop->insert();

        return $prop;
    }

    /**
     * @param $item
     * @param $value
     * @return string
     */
    public function createPropValue($item, $value) : string
    {
        $value_obj = new ItemValue();
        $value_obj['value'] = $value;
        $value_obj['prop_id'] = $item['prop_id'];

        $value_obj->insert();

        self::$existed_values[$item['prop_id']][$value] = $value_obj['id'];

        return $value_obj['id'];
    }

    /**
     *
     */
    public function loadImportData() : void
    {
        self::$existed_group = Request::make()
            ->select('id, title')
            ->from(\Catalog\Model\Orm\Property\Dir::_getTable())
            ->exec()->fetchSelected('title', ['id']);
        self::$existed_group[self::ROOT_DIR_ID] = ['id' => 0];

        self::$existed_prop = Request::make()
            ->select('title, type, parent_id, id')
            ->from(\Catalog\Model\Orm\Property\Item::_getTable())
            ->exec()->fetchSelected('title', ['type', 'parent_id', 'id']);

        self::$existed_values = Request::make()
            ->select('prop_id, value, id')
            ->from(\Catalog\Model\Orm\Property\ItemValue::_getTable())
            ->exec()->fetchSelected('prop_id', ['value', 'id'], true);

        foreach (self::$existed_values as &$item) {
            $new_item = [];
            foreach ($item as $index => $sub_item) {
                $new_item[$sub_item['value']] = $sub_item['id'];
            }
            $item = $new_item;
        }
    }
}
