<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

use RS\Helper\Tools as HelperTools;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Добавляет к экспорту колонки соответствующие свойствам ORM объекта.
 * Самый простой набор колонок. В качестве названия колонок выступают названия свойств Orm объекта
 */
class Base extends AbstractPreset
{
    protected $fields = [];
    protected $select_request;
    protected $id_field = 'id';
    /** @var OrmRequest */
    protected $saved_request = null; //Объект запроса из сессии с параметрами текущего просмотра списка
    protected $select_order;
    protected $exclude_fields = [];
    protected $extra_fields = [];
    protected $titles = [];
    protected $search_fields = [];
    protected $load_expression;
    protected $is_multisite = false;
    protected $use_cache = true;
    protected $cache = [];
    protected $null_fields = [];
    protected $replace_mode = false;
    protected $use_temporary_id;
    protected $uniq_fields;
    protected $custom_html_encoded_field;
    /** @var AbstractObject */
    protected $orm_object;

    /**
     * Устанавливает использовать ли REPLACE вместо INSERT и UPDATE, при вставке в базу
     *
     * @param bool $bool
     * @return void
     */
    public function setReplaceMode($bool)
    {
        $this->replace_mode = $bool;
    }

    /**
     * Устнавливает поля объекта которые будут показываться несмотря на то, runtime они или не видимые
     *
     * @param array $fields - массив экстра полей
     */
    public function setExtraFields($fields)
    {
        $this->extra_fields = $fields;
    }

    /**
     * Устнавливает запрос, который был взят из сессии с установленными параметрами просмотра списка
     *
     * @param \RS\Orm\Request|null $request - объект из сессии
     */
    public function setSavedRequest($request)
    {
        $this->saved_request = $request ? clone $request : null;
    }

    /**
     * Указывает какое поле является уникальным идентификатором объекта
     *
     * @param mixed $field
     */
    public function setIdFIeld($field)
    {
        $this->id_field = $field;
    }

    /**
     * Устанавливает колонки, которые в случае пустоты будут записаны в базу как NULL
     *
     * @param mixed $fields
     */
    public function setNullFields(array $fields)
    {
        $this->null_fields = $fields;
    }

    /**
     * Загружает данные перед экспортом
     *
     * @return void
     */
    public function loadData()
    {
        $this->rows = $this->schema->rows;
    }

    /**
     * Возвращает данные для вывода в CSV
     *
     * @param int $n - индекс строки
     * @return string[]
     */
    public function getColumnsData($n)
    {
        $html_encoded_fields = $this->getHtmlEncodedFields();
        $this->row = [];

        foreach ($this->getColumns() as $id => $column) {
            //Если поле закодировано, то раскодируем его
            (in_array($column['key'], $html_encoded_fields)) ? $value = HelperTools::unEntityString($this->rows[$n][$column['key']]) :
                $value = $this->rows[$n][$column['key']];

            $this->row[$id] = trim($value);
        }
        return $this->row;
    }

    /**
     * Импортирует данные одной строки текущего пресета в базу
     *
     * @return void
     */
    public function importColumnsData()
    {
        $html_encoded_fields = $this->getHtmlEncodedFields();


        if(isset($this->row['recommended'])){
            $this->row['recommended_arr'] = unserialize($this->row['recommended']);
        }
        if(isset($this->row['concomitant'])){
            $this->row['concomitant_arr'] = unserialize($this->row['concomitant']);
        }
        foreach ($this->row as $field => $value) {
            if ($value === '' && in_array($field, $this->null_fields)) {
                unset($this->row[$field]);
            }
            //Если поле должно быть закодированным в базе, то кодируем его
            if (isset($this->row[$field]) && in_array($field, $html_encoded_fields)) {
                $this->row[$field] = HelperTools::toEntityString($value);
            }
        }

        if ($this->replace_mode) {
            $orm_object = clone $this->getOrmObject();
            $orm_object->getFromArray($this->row);
            $orm_object->replace();
        } else {
            $orm_object = $this->loadObject();
            if ($orm_object) {
                //Обновление
                unset($this->row[$this->id_field]);
                $orm_object->getFromArray($this->row);
                $orm_object->update();
            } else {
                //Создание
                $orm_object = clone $this->getOrmObject();
                $orm_object->getFromArray($this->row);
                $orm_object->insert();
            }
        }
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     */
    public function getColumns()
    {
        $result = [];
        foreach ($this->orm_object->getProperties() as $key => $property) {
            if (!in_array($key, $this->exclude_fields) && (!$this->fields || isset($this->fields[$key])) && (!$property->isRuntime() || isset($this->fields[$key])) || in_array($key, $this->extra_fields)) {
                $title = isset($this->titles[$key]) ? $this->titles[$key] : $property->getTitle();
                $result[$this->id . '-' . $key] = [
                    'key' => $key,
                    'title' => $title
                ];
            }
        }
        return $result;
    }

    /**
     * Устанавливает пользовательские названия для колонок
     *
     * @param array $titles
     * @return void
     */
    public function setTitles(array $titles)
    {
        $this->titles = $titles;
    }

    /**
     * Устанавливает свойства, которые должны появиться в экспорте
     *
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = array_combine($fields, $fields);
    }

    /**
     * Возвращает какие поля следует исключить из выгрузки
     *
     * @return array
     */
    public function getExcludeFields()
    {
        return $this->exclude_fields;
    }

    /**
     * Устанавливает какие поля следует исключить из выгрузки
     *
     * @param array $fields
     * @return void
     */
    public function setExcludeFields($fields)
    {
        $this->exclude_fields = $fields;
    }

    /**
     * Возвращает поля, которые будут участвовать в выгрузке
     */
    public function getFields()
    {
        if (!$this->fields) {
            $this->fields = [];
            foreach ($this->getColumns() as $column) {
                $this->fields[] = $column['key'];
            }
        }
        return $this->fields;
    }


    /**
     * Устанавливает дополнительное условие для поиска уже имеющегося элемента в базе во время импорта.
     *
     * @param array | string $expr
     * @return void
     */
    public function setLoadExpression($expr)
    {
        $this->load_expression = $expr;
    }

    /**
     * Добавляет дополнительное условие в виде site_id = ТЕКУЩИЙ САЙТ, если задано true
     *
     * @param bool $bool
     * @return void
     */
    public function setMultisite($bool)
    {
        $this->is_multisite = $bool;
    }

    /**
     * Возвращает условие для добавления к Where, если установлено свойство multisite => true
     *
     * @return array
     */
    public function getMultisiteExpr()
    {
        return $this->is_multisite ? ['site_id' => SiteManager::getSiteId()] : null;
    }

    /**
     * Поля для поиска
     *
     * @param array $fields
     * @return void
     */
    public function setSearchFields(array $fields)
    {
        $this->search_fields = $fields;
    }

    /**
     * Возвращает массив c условиями для поиска
     *
     * @return array | null
     */
    public function getSearchExpr()
    {
        if (!$this->search_fields) {
            $this->search_fields = $this->getFields();
        }

        $search_expr = [];
        foreach ($this->search_fields as $field) {
            $search_expr[$field] = isset($this->row[$field]) ? $this->row[$field] : '';
        }
        return $search_expr;
    }

    /**
     * Устанавливает объект, связанный с данным набором колонок
     *
     * @param mixed $orm_object
     */
    public function setOrmObject(AbstractObject $orm_object)
    {
        $this->orm_object = $orm_object;
    }

    /**
     * Возвращает объект, связанный с данным набором колонок
     *
     * @return \RS\Orm\AbstractObject
     */
    public function getOrmObject()
    {
        return $this->orm_object;
    }

    /**
     * Загружает объект из базы по имеющимся данным в row или возвращает false
     *
     * @return \RS\Orm\AbstractObject|bool
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
     * Возвращает объект Orm\Request для стартовой выборки элементов
     *
     * @return \RS\Orm\Request
     */
    public function getSelectRequest()
    {
        if (!$this->select_request) {
            if (!$this->saved_request) { //Если нет запроса сохранённого в сессии
                $this->select_request = OrmRequest::make()->from($this->getOrmObject());
                if ($this->is_multisite) {
                    $this->select_request->where(['site_id' => SiteManager::getSiteId()]);
                }

                if ($this->select_order) {
                    $this->select_request->orderby($this->select_order);
                }
            } else { //Если есть запрос сохранённый в сессии
                $this->saved_request->limit(null);
                $this->select_request = $this->saved_request;
            }
        }
        return $this->select_request;
    }

    /**
     * Устанавливает порядок сортировки выборки для выгрузки
     *
     * @param string $order - сортировка выборки
     * @return AbstractPreset
     */
    public function setSelectOrder($order)
    {
        $this->select_order = $order;
        return $this;
    }

    /**
     * Устанавливает объект запроса для стартовой выборки
     *
     * @param \RS\Orm\Request $q
     * @return self
     */
    public function setSelectRequest(OrmRequest $q)
    {
        $this->select_request = $q;
        return $this;
    }

    /**
     * Устанавливает кодируемые поля в пресете
     *
     * @param $fields
     */
    public function setCustomHtmlEncodedFields($fields)
    {
        $this->custom_html_encoded_field = $fields;
    }

    /**
     * Возвращает установленные кодируемые поля в пресете
     * @return mixed
     */
    public function getCustomHtmlEncodedFields()
    {
        return $this->custom_html_encoded_field;
    }

    /**
     * Если не заданы кодируемые поля, то вернет их по базовому алгоритму из ORM-объектов
     *
     * @return mixed
     */
    public function getHtmlEncodedFields()
    {
        if (is_null($this->custom_html_encoded_field)) {
            $fields = $this->orm_object->getHtmlEncodedFields();
        } else {
            $fields = $this->custom_html_encoded_field;
        }

        return $fields;
    }
}
