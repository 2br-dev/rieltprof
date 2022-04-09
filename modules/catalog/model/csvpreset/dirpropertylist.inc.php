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
 * Добавляет к экспорту колонку, "характеристики товара в списке"
 */
class DirPropertyList extends \RS\Csv\Preset\AbstractPreset
{
    protected static
        $props,
        $groups,
        $index;

    protected
        $delimiter = ";",
        $link_foreign_field = 'in_list_properties',
        $link_preset_id = 0,
        $title;

    function __construct($options)
    {
        $defaults = [
            'ormObject' => new \Catalog\Model\Orm\Property\Item(),
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
                ->select('id, title', 'parent_id')
                ->from(new \Catalog\Model\Orm\Property\Item())
                ->where($this->getMultisiteExpr() ?: null)
                ->exec()->fetchSelected( 'id');

            self::$groups = \RS\Orm\Request::make()
                ->select('id, title')
                ->from(new \Catalog\Model\Orm\Property\Dir())
                ->where($this->getMultisiteExpr() ?: null)
                ->exec()->fetchSelected( 'id');

            foreach(self::$props as $prop) {
                $group_name = isset(self::$groups[$prop['parent_id']]) ? self::$groups[$prop['parent_id']]['title'] : '';
                self::$index["($group_name)".$prop['title']] = $prop['id'];
            }
        }
    }

    /**
     * Устанавливает связываемое поле из базового пресета
     *
     * @param string $field
     * @return void
     */
    function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
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
     * Возвращает колонки, предоставляемые данным пресетом
     *
     * @return array
     */
    function getColumns()
    {
        return [
            $this->id.'-properties_in_list' => [
                'key' => 'properties_in_list',
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
        $property_ids = $this->schema->rows[$n][$this->link_foreign_field];
        $data = [];

        if (is_array($property_ids)) {
            foreach ($property_ids as $prop_id) {
                if (isset(self::$props[$prop_id])) {
                    $prop = self::$props[$prop_id];
                    $group = self::$groups[$prop['parent_id']] ?? [];

                    $data[] = sprintf('(%s)%s',
                        $group['title'] ?? '',
                        $prop['title'] ?? ''
                    );
                }
            };
        }

        return [$this->id.'-properties_in_list' => implode(";\n", $data)];
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
        if (isset($this->row['properties_in_list']) && !empty($this->row['properties_in_list'])) {
            $items = explode($this->delimiter, $this->row['properties_in_list']);

            $property_ids = [];
            foreach ($items as $item) {
                $item = trim($item);
                if (isset(self::$index[$item])) {
                    $property_ids[] = self::$index[$item];
                }
            }

            $this->schema->getPreset($this->link_preset_id)->row[$this->link_foreign_field] = $property_ids;
        }
    }
}
