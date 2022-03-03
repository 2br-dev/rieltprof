<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Db\Adapter as DbAdapter;
use RS\Db\Exception as DbException;
use RS\Orm\AbstractObject;
use RS\Orm\Exception as OrmException;

/**
 * Класс модели. Содержит стандартное API для работы с плоским (не древовидным) списком.
 */
class EntityList extends BaseModel
{
    const WHERE_CONDITION_VAR = 'where_conditions';

    static protected $instance = [];

    protected $default_order;
    protected $load_on_delete = false;
    protected $id_field = 'id';
    protected $alias_field;
    protected $sort_field;
    protected $name_field;
    protected $site_id_field = 'site_id';
    protected $is_multisite = false;
    protected $site_context;
    protected $def_table_alias = 'A'; //Псевдоним таблицы по умолчанию.
    protected $obj; //string - Объект с которым работает этот класс
    protected $obj_instance;
    protected $filter_active = false; //Если true, значит фильтр применен
    protected $multiedit_template = '%system%/coreobject/multiedit_form.tpl';
    protected $assocPlainList = []; //Полностью загруженный список
    protected $parsePrefixes = ['&' => 'AND', '|' => 'OR', '' => ''];
    /** @var \RS\Orm\Request */
    protected $q;

    function __construct(AbstractObject $orm_element, array $options = [])
    {
        $this
            ->setElement($orm_element)
            ->setSiteContext();

        foreach ($options as $key => $value) {
            $method_name = 'set' . $key;
            if (method_exists($this, $method_name)) {
                $this->$method_name($value);
            } else {
                $this->$key = $value;
            }
        }

        $this->resetQueryObject();
    }

    /**
     * Возвращает экземпляр текущего класса
     *
     * @param string $key - идентификатор экземпляра класса
     * @return static
     */
    public static function getInstance($key = 'default')
    {
        if (!isset(self::$instance[get_called_class()][$key])) {
            self::$instance[get_called_class()][$key] = new static(); // у наследников конструктор без параметров
        }
        return self::$instance[get_called_class()][$key];
    }

    /**
     * Устанавливает сортировку по-умолчанию
     *
     * @param string $order - условие для секции ORDER BY
     * @return EntityList
     */
    function setDefaultOrder($order)
    {
        $this->default_order = $order;
        return $this;
    }

    /**
     * Возвращает поле, в котором находится название записи
     *
     * @return string
     */
    public function getNameField()
    {
        return $this->name_field;
    }

    /**
     * Устанавливает поле, в котором находится название записи.
     * Например для товара это поле: "title", в котором находится название товара.
     *
     * @param string $field - поле ORM объекта
     * @return EntityList
     */
    public function setNameField($field)
    {
        $this->name_field = $field;
        return $this;
    }

    /**
     * Устанавливает поле, в котором находится символьный идентификатор записи.
     *
     * @param string $field - поле ORM Объекта
     * @return EntityList
     */
    function setAliasField($field)
    {
        $this->alias_field = $field;
        return $this;
    }

    /**
     * Устанавливает поле, в котором находится сортировочный индекс.
     *
     * @param string $field - поле ORM Объекта
     * @return EntityList
     */
    function setSortField($field)
    {
        $this->sort_field = $field;
        return $this;
    }

    /**
     * Устнавливает поле, в котором находится уникальный идентификатор записи.
     * Обычно это поле "id"
     *
     * @param string $field - поле ORM объекта
     * @return EntityList
     */
    function setIdField($field)
    {
        $this->id_field = $field;
        return $this;
    }

    /**
     * Возвращает название поля, являющееся ID
     *
     * @return string
     */
    function getIdField()
    {
        return $this->id_field;
    }

    /**
     * Возвращает название поля, содержащего ID сайта
     *
     * @return string
     */
    function getSiteIdField()
    {
        return $this->site_id_field;
    }

    /**
     * Если передано True, то перед удалением объект будет полностью загружаться по ID.
     * Иначе объекту будет проставлено только поле ID
     *
     * @param bool $bool
     * @return EntityList
     */
    function setLoadOnDelete($bool)
    {
        $this->load_on_delete = $bool;
        return $this;
    }

    /**
     * Устанавливает id сайта, в рамках которого будут выбираться данные для объекта
     *
     * @param integer $site_id - ID сайта
     * @return EntityList
     */
    function setSiteContext($site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }
        $this->site_context = $site_id;
        return $this;
    }

    /**
     * Возвращает id сайта, в рамках которого будут выбираться записи
     *
     * @return integer
     */
    function getSiteContext()
    {
        return $this->site_context;
    }

    /**
     * Устанавливает, связаны ли данные ORM объекта с конкретным сайтом. Или объект имеет общие данные для всех сайтов.
     *
     * @param bool $bool - Если задано True, то ко всем выборкам будет добавлено условие WHERE site_id = ID сайта
     * @return EntityList
     */
    function setMultisite($bool)
    {
        $this->is_multisite = $bool;
        return $this;
    }

    /**
     * Возвращает true, если данные объекта связаны с ID сайта
     *
     * @return bool
     */
    function isMultisite()
    {
        return $this->is_multisite;
    }


    /**
     * Сбрасывает условие выборки элементов на значение по умолчанию
     *
     * @return EntityList
     */
    function resetQueryObject()
    {
        $this->q = $this->getCleanQueryObject();
        return $this;
    }

    /**
     * Возвращает новый объект с установленными по-умолчанию фильтрами для выборки элементов
     *
     * @return \RS\Orm\Request
     */
    function getCleanQueryObject()
    {
        $q = \RS\Orm\Request::make()
            ->select('*')
            ->from($this->obj_instance)->asAlias($this->defAlias())
            ->orderby($this->default_order);

        if ($this->is_multisite) {
            $q->where([$this->defAlias() . '.' . $this->site_id_field => $this->getSiteContext() ?: 0]);
        }

        return $q;
    }

    /**
     * Возвращает объект выборки элементов
     *
     * @return \RS\Orm\Request
     */
    function queryObj()
    {
        return $this->q;
    }

    /**
     * Устанавливает объект выборки элементов
     *
     * @param \RS\Orm\Request $q
     * @return EntityList
     */
    function setQueryObj(\RS\Orm\Request $q)
    {
        $this->q = $q;
        return $this;
    }

    /**
     * Возвращает псевдоним(alias) к таблице с которой работает класс
     * @return string
     */
    function defAlias()
    {
        return $this->def_table_alias;
    }

    /**
     * Устанавливает фильтр для последующей выборки элементов
     *
     * @param string | array $key - имя поля (соответствует имени поля в БД) или массив для установки группового фильтра
     * Пример применения группового фильтра:
     * array(
     *   'title' => 'Название',                     // AND title = 'Название'
     *   '|title:%like%' => 'Текст'                 // OR title LIKE '%Текст%'
     *   '&title:like%' => 'Текст'                  // AND title LIKE 'Текст%'
     *   'years:>' => 18,                           // AND years > 18
     *   'years:<' => 21,                           // AND years < 21
     *   ' years:>' => 30,                          // AND years > 30  #пробелы по краям вырезаются
     *   ' years:<' => 40,                          // AND years < 40  #пробелы по краям вырезаются
     *   'id:in' => '12,23,45,67,34',               // AND id IN (12,23,45,67,34)
     *   '|id:notin' => '44,33,23'                  // OR id NOT IN (44,33,23)
     *   'id:is' => 'NULL'                          // AND id IS NULL
     *   'id:is' => 'NOT NULL'                      // AND id IS NOT NULL
     *
     *   array(                                     // AND (
     *       'name' => 'Артем',                     // name = 'Артем'
     *       '|name' => 'Олег'                      // OR name = 'Олег'
     *   ),                                         // )
     *
     *   '|' => array(                              // OR (
     *       'surname' => 'Петров'                  // surname = 'Петров'
     *       '|surname' => 'Иванов'                 // OR surname = 'Иванов'
     *   )                                          // )
     * )
     * Общая маска ключей массива:
     * [пробелы][&|]ИМЯ ПОЛЯ[:ТИП ФИЛЬТРА]
     *
     * @param mixed $value - значение
     * @param string $type - =,<,>, in, notin, fulltext, %like%, like%, %like тип соответствия поля значению.
     * @param string $prefix условие связки с предыдущими условиями (AND/OR/...)
     * @param array $options
     * @return EntityList
     */
    function setFilter($key, $value = null, $type = '=', $prefix = 'AND', array $options = [])
    {
        if (is_array($key)) {
            //Парсим групповой фильтр
            $this->parseArrayFilter($key);
        } else {
            $key = '`' . str_replace('.', "`.`", $key, $count) . '`';
            if (!$count) {
                $key = '`' . $this->defAlias() . '`.' . $key;
            }
            $type_short = str_replace('%', '', $type);
            $method_name = 'filter' . $type_short;
            if (!method_exists($this, $method_name)) {
                $method_name = 'filterDefault';
            }
            call_user_func([$this, $method_name], $key, $value, $type, $prefix, $options);
        }
        return $this;
    }

    /**
     * Рекурсивно парсит групповые фильтры
     *
     * @param array $filters - массив с групповыми фильтрами. Формат - см. метод setFilter
     * @return void
     */
    protected function parseArrayFilter($filters)
    {
        foreach ($filters as $key => $value) {
            $key = trim($key); //С помощью пробелов по краям, можно обходить объединение ключей в массиве
            if (is_array($value)) {
                $this->queryObj()->openWGroup(!is_numeric($key) ? $this->parsePrefixes[$key] : 'AND');
                $this->parseArrayFilter($value);
                $this->queryObj()->closeWGroup();
            } else {
                if (preg_match('/^([&|])?(.*?)(:.*?)?$/', $key, $match)) {
                    $prefix = $match[1] ? $this->parsePrefixes[$match[1]] : 'AND';
                    $field = $match[2];
                    $type = isset($match[3]) ? ltrim($match[3], ':') : '=';
                    $this->setFilter($field, $value, $type, $prefix);
                }
            }
        }
    }

    /**
     * Добавляет секцию LIKE в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterLike($key, $value, $type, $prefix, $options)
    {
        $value = str_replace('like', $value, $type);
        $this->queryObj()->where("{$key} LIKE '#phrase'", ['phrase' => $value], $prefix);
    }

    /**
     * Добавляет секцию IS в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterIs($key, $value, $type, $prefix, $options)
    {
        if ($value === null) $value = 'NULL';
        $this->queryObj()->where("{$key} IS #phrase", ['phrase' => $value], $prefix);
    }

    /**
     * Добавляет секцию MATCH AGAINT в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterFullText($key, $value, $type, $prefix, $options)
    {
        $this->queryObj()->where("MATCH({$key}) AGAINST('#phrase')", ['phrase' => $value], $prefix);
    }

    /**
     * Добавляет секцию IN (...) в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterIn($key, $value, $type, $prefix, $options)
    {
        if (is_array($value)) {
            $value = implode(',', \RS\Helper\Tools::arrayQuote($value));
        }
        $this->queryObj()->where("{$key} IN ({$value})", null, $prefix);
    }

    /**
     * Добавляет секцию NOT IN (...) в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterNotin($key, $value, $type, $prefix, $options)
    {
        if (is_array($value)) {
            $value = implode(',', \RS\Helper\Tools::arrayQuote($value));
        }
        $this->queryObj()->where("{$key} NOT IN ({$value})", null, $prefix);
    }

    /**
     * Добавляет секцию <,=,> в условие выборки
     *
     * @param string $key - поле, для которого будет установлен фильтр
     * @param string $value - значение
     * @param string $type - тип фильтра. оригинальное знаение из метода setFilter
     * @param string $prefix - префикс перед фильтром (AND, OR)
     * @param array $options - массив дополнительных параметров. зарезервировано.
     * @return void
     */
    protected function filterDefault($key, $value, $type, $prefix, $options)
    {
        if ($value === null) {
            $this->queryObj()->where("{$key} IS NULL", null, $prefix);
        } else {
            $this->queryObj()->where("{$key} {$type} '#data'", ['data' => $value], $prefix);
        }
    }

    /**
     * Очищает условия выборки
     * @return static
     */
    function clearFilter()
    {
        $this->resetQueryObject();
        return $this;
    }

    /**
     * Возвращает список объектов, согласно заданным раннее условиям
     *
     * @param integer $page - номер страницы
     * @param integer $page_size - количество элементов на страницу
     * @param string $order - условие сортировки
     * @return AbstractObject[]
     */
    function getList($page = null, $page_size = null, $order = null)
    {
        $this->setPage($page, $page_size);
        $this->setOrder($order);
        return $this->queryObj()->objects($this->obj);
    }

    /**
     * Возвращает список массивов, согласно заданным раннее условиям
     *
     * @param integer $page - номер страницы
     * @param integer $page_size - количество элементов на страницу
     * @param string $order - условие сортировки
     * @return array
     */
    function getListAsArray($page = 0, $page_size = 0, $order = '')
    {
        $this->setPage($page, $page_size);
        $this->setOrder($order);
        return $this->q->exec()->fetchAll();
    }

    /**
     * Возвращает результат выборки, согласно заданным раннее условиям, в виде объекта \RS\Db\Result
     *
     * @param integer $page - номер страницы
     * @param integer $page_size - количество элементов на страницу
     * @param string $order - условие сортировки
     * @return \RS\Db\Result
     */
    function getListAsResource($page = 0, $page_size = 0, $order = '')
    {
        $this->setPage($page, $page_size);
        $this->setOrder($order);
        return $this->q->exec();
    }

    /**
     * Возвращает результат выборки, согласно заданным раннее условиям, в виде объекта-пагинатора \RS\Orm\Pages
     *
     * @return \RS\Orm\Pages
     */
    function getPagedList($page_size)
    {
        return new \RS\Orm\Pages($this->queryObj(), $page_size);
    }

    /**
     * Возвращает результат выборки,  согласно заданным раннее условиям, в виде ассоциативного массива
     *
     * @param string $key_field - поле, значение из которого будет использовано в ключе
     * @param string | null $value - поле, значение которого будет использовано в качестве значения для ключа $key_field,
     * если null, то в значение будет помещен объект выборки
     *
     * @return array
     */
    function loadAssocList($key_field, $value = null)
    {
        $key = crc32($key_field . $value);
        if (!isset($this->assocPlainList[$key])) {
            if ($value === null) {
                $this->assocPlainList[$key] = $this->queryObj()->objects($this->obj_instance, $key_field);
            } else {
                $this->assocPlainList[$key] = $this->queryObj()->exec()->fetchSelected($key_field, $value);
            }
        }
        return $this->assocPlainList[$key];
    }

    /**
     * Возвращает результат выборки,  согласно заданным раннее условиям, в виде ассоциативного массива
     *
     * @param string $key_field - поле, значение из которого будет использовано в ключе
     * @param string | null $value - поле, значение которого будет использовано в качестве значения для ключа $key_field,
     * если null, то в значение будет помещен объект выборки
     *
     * @return array
     */
    function getAssocList($key_field, $value = null)
    {
        return $this->loadAssocList($key_field, $value);
    }

    /**
     * Устанавливает страницу и количество элементов на страницу для выборки
     *
     * @param integer $page - номер страницы
     * @param integer $pageSize - количество элементов на страницу
     * @return EntityList
     */
    protected function setPage($page, $pageSize)
    {
        if ($page) {
            $offset = ($page - 1) * $pageSize;
            $this->q
                ->offset($offset)
                ->limit($pageSize);
        }
        return $this;
    }

    /**
     * Устанавливает сортировку последующей выборки
     *
     * @param string $order - условие сортировки, будет подставлено в SQL запрос
     * @return EntityList
     */
    function setOrder($order = null, array $values = null)
    {
        if ($order) {
            $this->q
                ->orderby($order, $values);
        }
        return $this;
    }

    /**
     * Устанавливает группировку последующей выборки
     *
     * @param mixed $order
     * @return EntityList
     */
    function setGroup($group = null)
    {
        if ($group) {
            $this->q
                ->groupby($group);
        }
        return $this;
    }

    /**
     * Возвращает общее количество элементов, согласно условию.
     *
     * @return integer
     */
    function getListCount()
    {
        $q = clone $this->queryObj();
        return $q->limit(null)->orderby(null)->count();
    }

    /**
     * Возвращает первый элемент, согласно условию
     *
     * @return AbstractObject|null
     */
    function getFirst()
    {
        $q = clone $this->queryObj();
        $object = $q->limit(0, 1)->object($this->obj);
        return $object ?: null;
    }

    /**
     * Загружает из базы 1 элемент
     *
     * @param mixed $id
     * @return AbstractObject|false
     */
    function getOneItem($id)
    {
        $q = $this->getCleanQueryObject();
        return $q->where([$this->id_field => $id])->orderby(null)->object();
    }

    /**
     * Возвращает элемент по id или псевдониму
     *
     * @param mixed $id - id элемента или псевдоним (псевдоним имеет больший приоритет)
     * @return AbstractObject|false
     */
    function getById($id)
    {
        $q = $this->getCleanQueryObject();

        $q->openWGroup();

        if (!$this->alias_field || is_numeric($id)) {
            //Для оптимизации работы с БД исключаем поиск по ID строковых значений.
            //Считаем, что строковые значения хранятся только в alias поле
            $q->where("`$this->id_field` = '#alias_or_id'", ['alias_or_id' => $id]);
        }

        if ($this->alias_field) {
            $q->where("`{$this->alias_field}` = '#alias_or_id'", ['alias_or_id' => $id], 'OR')
                ->orderby("{$this->alias_field} = '#alias_or_id' desc", ['alias_or_id' => $id]);
        }
        $q->closeWGroup();

        return $q->object($this->obj);
    }

    /**
     * Возвращает ORM объект, с которым работает данное API
     *
     * @return AbstractObject
     */
    function getElement()
    {
        return $this->obj_instance;
    }

    /**
     * Устанавливает объект для даннго api
     *
     * @param mixed $object
     * @return EntityList
     */
    function setElement($object)
    {
        $this->obj = '\\' . get_class($object);
        $this->obj_instance = $object;

        if ($this->id_field === null) {
            $obj_primary_field = $this->obj_instance->getPrimaryKeyProperty();
            if ($obj_primary_field) {
                $this->setIdField($obj_primary_field);
            }
        }
        return $this;
    }

    /**
     * Возвращает класс объектов, с которыми работает данное API
     *
     * @return string
     */
    function getElementClass()
    {
        return $this->obj;
    }

    /**
     * Возвращает новый экземпляр объекта, с которым работает данное API
     *
     * @return \RS\Orm\AbstractObject
     */
    function getNewElement()
    {
        return new $this->obj();
    }

    /**
     * Сохраняет элемент, с которым работает данное API, принимая значения из POST
     *
     * @param mixed $id - id элемента, если задано, то будет вызван update, иначе insert
     * @param array $user_post - дополнительные значения, которые необходимо задать элементу
     * @return bool
     */
    function save($id = null, array $user_post = [])
    {
        return $this->obj_instance->save($id, $user_post);
    }


    /**
     * Устанавливает фильтры, от компонента \RS\Html\Filter\Control
     *
     * @param \RS\Html\Filter\Control $filter_control - объект фильтра
     * @return EntityList
     */
    function addFilterControl(\RS\Html\Filter\Control $filter_control)
    {
        $sqlFilter = $filter_control->getSqlWhere();
        if (!empty($sqlFilter)) {
            $this->filter_active = true;
            $this->queryObj()->where($sqlFilter);
        }
        $filter_control->modificateQuery($this->queryObj());
        return $this;
    }

    /**
     * Устанавливает сортировку от компонента \RS\Html\Table\Control
     *
     * @param \RS\Html\Table\Control $table_control - объект таблицы
     * @return EntityList
     */
    function addTableControl(\RS\Html\Table\Control $table_control)
    {
        if (($order = $table_control->getSqlOrderBy()) !== false) {
            $this->queryObj()->orderby($order);
        } else {
            $table_control->modificateSortQuery($this->queryObj());
        }


        return $this;
    }

    /**
     * Удаляет элементы, используя механизм orm-object'ов. удаляем каждый объект персонально.
     * Является механизмом по умолчанию
     *
     * @param array $ids - массив со списком id объектов
     * @return bool - Возвращает true, если удаление всех элементов прошло успешно, иначе false
     */
    function del(array $ids)
    {
        if ($this->noWriteRights($this->getElement()->getRightDelete())) return false;

        $result = true;
        foreach ($ids as $id) {
            if ($this->load_on_delete) {
                $this->obj_instance->load($id);
            } else {
                $this->obj_instance[$this->obj_instance->getPrimaryKeyProperty()] = $id;
            }
            $result = $this->obj_instance->delete() && $result;
        }

        return $result;
    }

    /**
     * Удаление, используя частный механизм.
     * Этот метод нужно перегрузить у наследников, если имеется более быстрый алгоритм удаления.
     *
     * @param array $ids - массив со списком id объектов
     * @return bool - Возвращает true, если удаление всех элементов прошло успешно, иначе false
     */
    function multiDelete($ids)
    {
        return $this->del($ids); //Вызываем стандартный механизм по умолчанию
    }

    /**
     * Возвращает список для отображения в html элементе select
     *
     * @param array $first - значения, которые нужно добавить в начало списка
     * @return array
     */
    function getSelectList($first = [])
    {
        $result = [];
        $list = $this->getList();
        foreach ($list as $v) {
            $result[$v[$this->id_field]] = $v[$this->name_field];
        }

        $first = (array)$first;
        if (!empty($first)) {
            $result = $first + $result;
        }

        return $result;
    }

    /**
     * Аналог getSelectList, только для статичского вызова
     *
     * @param array $first - значения, которые нужно добавить в начало списка
     * @return array
     */
    static function staticSelectList($first = [])
    {
        $_this = new static();
        return $_this->getSelectList((array)$first);
    }

    /**
     * Возвращает html формы группового редактирования объектов
     *
     * @return string
     */
    function multieditFormView($tpl_path, $formfile, array $addparam = [])
    {
        $fullpath = strtolower($tpl_path . $formfile);
        if (!file_exists($fullpath)) {
            $denied_types = ['Core_Type_File'];

            //Нужно создать шаблон исходя из полей объекта
            $make_form = new \RS\View\Engine();
            $make_form->assign('prop', $this->obj_instance->getProperties());
            $make_form->assign('denied_types', $denied_types);
            $content = $make_form->fetch($this->multiedit_template);

            \RS\File\Tools::makePath($fullpath, true);
            file_put_contents($fullpath, $content);
        }

        $inner_form = new \RS\View\Engine();
        $inner_form->assign('elem', $this->obj_instance);
        $inner_form->assign('param', $addparam);
        $inner_form->template_dir = $tpl_path;
        $com_form_content = $inner_form->fetch($formfile);

        return $com_form_content;
    }

    /**
     * Обновляет свойства у группы объектов
     *
     * @param array $data - ассоциативный массив со значениями обновляемых полей
     * @param array $ids - список id объектов, которые нужно обновить
     * @return int - возвращает количество обновленных элементов
     */
    function multiUpdate(array $data, $ids = [])
    {
        if ($this->noWriteRights($this->getElement()->getRightUpdate())) return false;

        if (!empty($data)) {
            $u = clone $this->queryObj();
            $u->update()
                ->set($data)
                ->orderby(false)
                ->exec();
        }
        return DbAdapter::affectedRows();
    }

    /**
     * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
     *
     * @param int $from - id элемента, который переносится
     * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     * @param \RS\Orm\Request $extra_expr - объект с установленными уточняющими условиями, для выборки объектов сортировки
     * @return bool
     */
    function moveElement($from, $to, $flag, \RS\Orm\Request $extra_expr = null)
    {
        if ($this->noWriteRights($this->getElement()->getRightUpdate())) return false;

        $is_up = $flag == 'up';

        $from_obj = $this->getOneItem($from);
        $to_obj = $this->getOneItem($to);

        $from_sort = $from_obj[$this->sort_field];
        $to_sort = $to_obj[$this->sort_field];

        $r = '=';

        if ((!$is_up && $from_sort > $to_sort) || ($is_up && $from_sort < $to_sort)) {
            $r = '';
            $is_up = !$is_up;
        }

        if ($from_sort >= $to_sort) {
            $filter = "{$this->sort_field} >$r '$to_sort' AND {$this->sort_field} <= '$from_sort'";
        } else {
            $filter = "{$this->sort_field} >= '$from_sort' AND {$this->sort_field} <$r '$to_sort'";
        }

        if (!$extra_expr) {
            $extra_expr = \RS\Orm\Request::make();
        }

        $res = $extra_expr
            ->select("{$this->defAlias()}.{$this->id_field}, {$this->defAlias()}.{$this->sort_field}")
            ->from($this->obj_instance)->asAlias($this->defAlias())
            ->where($filter)
            ->orderby($this->sort_field);

        if ($this->is_multisite) {
            $res->where([$this->defAlias() . '.' . $this->site_id_field => $this->getSiteContext() ?: 0]);
        }

        $res = $res->exec();

        if ($res->rowCount() < 2) return true;

        $list = $res->fetchAll();

        if ($is_up) $list = $this->moveArrayUp($list);
        else $list = $this->moveArrayDown($list);

        foreach ($list as $newValues) {
            \RS\Orm\Request::make()
                ->update($this->obj_instance)->asAlias($this->defAlias())
                ->set([$this->defAlias() . '.' . $this->sort_field => $newValues[$this->sort_field]])
                ->where([$this->defAlias() . '.' . $this->id_field => $newValues[$this->id_field]])
                ->exec();
        }
        return true;
    }


    /**
     * Передвигает элемент в начало списка
     *
     * @param array $arr - массив со списком любых элементов
     * @return array
     */
    protected function moveArrayUp($arr)
    {
        $first_sortn = $arr[0][$this->sort_field];
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $arr[$i][$this->sort_field] = $arr[$i + 1][$this->sort_field];
        }
        $last = array_pop($arr);
        $last[$this->sort_field] = $first_sortn;
        array_unshift($arr, $last);
        return $arr;
    }

    /**
     * Передвигает элемент в конец списка
     *
     * @param array $arr - массив со списком любых элементов
     * @return array
     */
    protected function moveArrayDown($arr)
    {
        $last_sortn = $arr[count($arr) - 1][$this->sort_field];
        for ($i = count($arr) - 1; $i > 0; $i--) {
            $arr[$i][$this->sort_field] = $arr[$i - 1][$this->sort_field];
        }
        $first = array_shift($arr);
        $first[$this->sort_field] = $last_sortn;
        array_push($arr, $first);
        return $arr;
    }

    /**
     * Возвращает true, если у текущего класса нет прав на запись.
     * Текст ошибки можно получить через getErrors
     *
     * @return bool
     */
    function noWriteRights($right = DefaultModuleRights::RIGHT_UPDATE)
    {
        if ($error = Rights::CheckRightError($this, $right, true)) {
            $this->addError($error);
            return true;
        }
        return false;
    }


    /**
     * Возвращает id элемента, если он существует, иначе false
     *
     * @param mixed $alias_or_id - id элемента или псевдоним
     * @param string $alias_field - имя поля, по которому искать запись, в случае если в $id передан псевдоним
     * @return integer | false
     * @throws \RS\Db\Exception
     */
    function getIdByAlias($alias_or_id, $alias_field = null)
    {
        if (!isset($this->_cache_alias[$alias_or_id])) {
            if ($alias_field === null) $alias_field = $this->alias_field;

            $q = $this->getCleanQueryObject();

            $q->openWGroup();
            $q->where("`$this->id_field` = '#alias_or_id'", ['alias_or_id' => $alias_or_id])
                ->limit(1);
            if ($alias_field) {
                $q->where("`$alias_field` = '#alias_or_id'", ['alias_or_id' => $alias_or_id], 'OR')
                    ->orderby("$alias_field != '#alias_or_id'", ['alias_or_id' => $alias_or_id]);
            }
            $q->closeWGroup();

            $res = $q->exec();

            $this->_cache_alias[$alias_or_id] = ($row = $res->fetchRow()) ? $row[$this->id_field] : false;
        }
        return $this->_cache_alias[$alias_or_id];
    }

    /**
     * Сохраяет текущие условия выборки в сессии
     *
     * @param mixed $key - ключ для последующего обращения.
     * @return void
     */
    function saveRequest($key)
    {
        if (!isset($_SESSION[self::WHERE_CONDITION_VAR]) || !is_array($_SESSION[self::WHERE_CONDITION_VAR])) {
            $_SESSION[self::WHERE_CONDITION_VAR] = [];
        }
        $_SESSION[self::WHERE_CONDITION_VAR][$key] = clone $this->queryObj();
    }

    /**
     * Возвращает сохраненные раннее в сессии условия выборки
     *
     * @param mixed $key ключ
     * @param mixed $default значение, возвращаемое в случае отсутствия ключа
     * @return mixed
     */
    static function getSavedRequest($key, $default = null)
    {
        return isset($_SESSION[self::WHERE_CONDITION_VAR][$key]) ? $_SESSION[self::WHERE_CONDITION_VAR][$key] : $default;
    }

    /**
     * Возвращает список уникальных идентификаторов
     *
     * @param \RS\Orm\Request $request
     * @return array
     */
    function getIdsByRequest(\RS\Orm\Request $request)
    {
        $q = clone $request;
        $q->select = $this->defAlias() . '.' . $this->id_field;
        $result = $q->limit(null)
          ->orderby(null)
          ->exec();

        return $result->fetchSelected(null, $this->id_field);
    }
}
