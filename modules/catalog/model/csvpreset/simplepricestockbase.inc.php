<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

use RS\Csv\Preset\Base as BasePreset;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;

/**
 * Добавляет к экспорту колонки соответствующие свойствам ORM объекта.
 * Самый простой набор колонок. В качестве названия колонок выступают названия свойств Orm объекта
 */
class SimplePriceStockBase extends BasePreset
{
    protected $fields = [];
    protected $select_request;
    protected $id_field = 'id';
    protected $saved_request = null; //Объект запроса из сессии с параметрами текущего просмотра списка
    protected $select_order;
    protected $orm_product_object;
    protected $show_fields = [];
    protected $extra_fields = [];
    protected $titles = [];
    protected $search_fields = [];
    protected $load_expression;
    protected $is_multisite = false;
    protected $use_cache = false;
    protected $cache = [];
    protected $null_fields = [];
    protected $orm_unset_fields = [];
    protected $replace_mode = false;
    protected $use_temporary_id;
    protected $uniq_fields;

    /**
     * Возвращает данные для вывода в CSV
     *
     * @param int $n - индекс строки
     * @return array
     */
    public function getColumnsData($n)
    {
        $this->row = [];
        foreach ($this->getColumns() as $id => $column) {
            $value = $this->rows[$n][$column['key']];
            //Если нулевая комплектация и пустой артикул, то присвоим артикул комплектации от самого товара
            if ($column['key'] == 'barcode' && empty($this->rows[$n]['sortn']) && ($this->rows[$n]['sortn'] == 0)) {
                $value = $this->rows[$n]['product_barcode'];
            }

            //Если это колонка с название комплектации, то добавим информацивности в данную колонку
            if ($column['key'] == 'title') {
                $offer_title = " [" . $this->rows[$n]['title'] . "]";
                if (empty($this->rows[$n]['title']) && $this->rows[$n]['sortn'] > 0) { //Если пуст заголовок и комплектация не нулевая
                    $offer_title = " [" . t('Комплектация') . " " . ($this->rows[$n]['sortn'] + 1) . "]";
                } elseif (empty($this->rows[$n]['title']) && $this->rows[$n]['sortn'] == 0) {
                    $offer_title = "";
                }
                $value = $this->rows[$n]['product_title'] . $offer_title;
            }
            $this->row[$id] = trim($value);
        }

        return $this->row;
    }

    /**
     * Устанавливает ORM объект товара, по которому будет искатся ненайденная комплектация
     *
     * @param \RS\Orm\AbstractObject $orm_object - объект ORM
     */
    public function setOrmProductObject(AbstractObject $orm_object)
    {
        $this->orm_product_object = $orm_object;
    }

    /**
     * Устанавливает поля, которые нужно у ORM объекта убрать
     *
     * @param array $fields - массив поле для
     */
    public function setOrmUnsetFields($fields)
    {
        $this->orm_unset_fields = $fields;
    }

    /**
     * Возвращает объект товара
     *
     */
    public function getOrmProductObject()
    {
        return $this->orm_product_object;
    }

    /**
     * Загружает объект из базы по имеющимся данным в row или возвращает false
     *
     * @return \RS\Orm\AbstractObject|false
     */
    public function loadObject()
    {
        $cache_key = implode('.', array_keys($this->getSearchExpr())) . implode('.', $this->getSearchExpr());

        if (!$this->use_cache || !isset($this->cache[$cache_key])) {
            $q = OrmRequest::make()
                ->from($this->getOrmObject())
                ->where($this->getSearchExpr())
                ->where($this->getMultisiteExpr());

            if ($this->load_expression) {
                $q->where($this->load_expression);
            }
            $object = $q->object();

            //Если комплектация не найдена, то тогда пробуем найти сам товар, а уже по нему нулевую комплектацию
            if (!$object) {
                $product_sql = OrmRequest::make()
                    ->select('P.id')
                    ->from($this->getOrmProductObject(), 'P')
                    ->where($this->getSearchExpr())
                    ->where($this->getMultisiteExpr())
                    ->limit(1)
                    ->toSql();

                $object = OrmRequest::make()
                    ->from($this->getOrmObject(), 'OFFER')
                    ->where('OFFER.product_id = (' . $product_sql . ')')
                    ->where([
                        'OFFER.sortn' => 0
                    ])
                    ->where($this->getMultisiteExpr())
                    ->object();
            }
            if ($object) {
                if ($this->use_cache) {
                    $this->cache[$cache_key] = $object;
                }
                return $object;
            } else {
                return false;
            }
        }
        return $this->cache[$cache_key];
    }

    /**
     * Импортирует данные одной строки текущего пресета в базу
     *
     * @return void
     */
    public function importColumnsData()
    {
        foreach ($this->row as $field => $value) {
            if ($value === '' && in_array($field, $this->null_fields)) {
                unset($this->row[$field]);
            }
        }

        if ($this->replace_mode) {
            $orm_object = clone $this->getOrmObject();
            $orm_object->getFromArray($this->row);
            $orm_object->replace();
        } else {
            $orm_object = $this->loadObject();

            if ($orm_object) { //Есть только обновление

                //Обновление
                unset($this->row[$this->id_field]);
                //Удалим ненужные поля, которые нам не нужны для обновления, но есть у подгруженного объекта
                if (!empty($this->orm_unset_fields)) {
                    foreach ($this->orm_unset_fields as $field) {
                        unset($orm_object[$field]);
                    }
                }
                $orm_object->getFromArray($this->row);
                $orm_object->update();
            }
        }
    }
}
