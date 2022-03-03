<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

use RS\Db\Adapter as DbAdapter;
use RS\Db\Result as DbResult;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Exception as OrmException;

/**
 * Класс предназначен для формирования запроса к БД и получения
 * списка объектов или ссылки на ресурс результата запроса.
 */
class Request
{
    public $select = '';
    public $ignore = false;
    public $delete;
    public $having;
    public $groupby;
    public $from = '';
    public $joins;
    public $where;
    public $limit;
    public $offset;
    public $orderby;
    public $set;
    public $columns;
    public $values = '';
    public $on_duplicate_key_update;
    public $map_obj; //какое поле какому объекту соответствует  

    protected $return_class;
    protected $last_action;
    protected $action = 'select';

    /**
     * Возвращает новый экземпляр текущего класса. Предназначен для краткой записи ::make()->from()->exec();
     *
     * @return Request
     */
    public static function make()
    {
        return new self();
    }

    /**
     * заполняет поле FROM запроса
     *
     * @param AbstractObject|string|array $table - имя таблицы или объект ORM
     * @param string $alias - псевдоним таблицы
     * @return self
     */
    public function from($table, $alias = null)
    {
        $args = is_array($table) ? $table : [$table];
        foreach ($args as $table) {
            if ($table instanceof AbstractObject) {
                $this->return_class = get_class($table); //По умолчанию устанавливаем класс возвращаемых объектов                
                $table = $table->_getTable();
            }
            $table .= ($alias !== null) ? ' as ' . $alias : '';
            $this->from .= empty($this->from) ? $table : ', ' . $table;
        }
        return $this;
    }

    /**
     * Добавляет alias к последней добавленной таблице.
     * Используется, когда в from передается core объект
     *
     * @param string $alias
     * @return Request
     */
    public function asAlias($alias)
    {
        $this->from .= ' as ' . $alias;
        return $this;
    }

    /**
     * Добавляет секцию JOIN к запросу
     *
     * @param mixed $table таблица или Core - объект
     * @param mixed $condition условие ON
     * @param mixed $table_alias alias к таблице
     * @param mixed $type тип join'а
     * @return self
     */
    public function join($table, $condition, $table_alias = null, $type = 'INNER')
    {
        if ($table instanceof AbstractObject) {
            $table = $table->_getTable();
        }
        $as_alias = ($table_alias) ? " as $table_alias" : '';
        $this->joins[] = "$type JOIN $table $as_alias ON $condition";

        return $this;
    }

    /**
     * Возвращает true, если указанная таблица присутствует в части from или join
     *
     * @param string|AbstractObject $table - имя таблицы или orm объект
     * @return bool
     */
    public function issetTable($table)
    {
        if ($table instanceof AbstractObject) {
            $table = $table->_getTable();
        }

        if (strpos($this->from, $table) !== false) return true;

        if (is_array($this->joins)) {
            foreach ($this->joins as $join) {
                if (strpos($join, $table) !== false) return true;
            }
        }
        return false;
    }

    /**
     * Добавляет секцию LEFT JOIN
     *
     * @param mixed $table
     * @param mixed $condition
     * @param mixed $table_alias
     * @return self
     */
    public function leftjoin($table, $condition, $table_alias = null)
    {
        return $this->join($table, $condition, $table_alias, 'LEFT');
    }

    /**
     * Дополняет секцию WHERE
     *
     * @param array|string $expr выражение WHERE.
     * @param array $values массив со значениям, заменит "-КЛЮЧ-" из expr на ЗНАЧЕНИЕ
     * @param string $prefix будет подставлено перед текущим выражением AND, OR,...
     * @param string $in_prefix будет подставлено между выражениями, в случае если expr - массив AND, OR,...
     * @return Request
     */
    public function where($expr, array $values = null, $prefix = 'AND', $in_prefix = 'AND')
    {
        if ($expr !== null && (!is_array($expr) || $expr)) {
            if ($this->last_action == 'openwgroup') {
                $prefix = '';
                $this->last_action = 'where';
            }

            if (is_array($expr)) {
                $str = [];
                foreach ($expr as $key => $val) {
                    $key = str_replace('.', '`.`', ltrim($key));
                    if ($val === null) {
                        $str[] = "`$key` IS NULL";
                    } else {
                        $val = DbAdapter::escape($val);
                        $str[] = "`$key` = '$val'";
                    }
                }
                $expr = '(' . implode(" $in_prefix ", $str) . ')';
            }

            if ($values !== null) {
                foreach ($values as $key => $val) {
                    $expr = str_replace("#$key", DbAdapter::escape($val), $expr);
                }
            }

            $this->where .= empty($this->where) ? $expr : " $prefix " . $expr;
        }
        return $this;
    }

    /**
     * Дополняет секцию WHERE условием "WHERE IN"
     *
     * @param string $field название колонки
     * @param array $values значения
     * @param string $prefix будет подставлено перед текущим выражением AND, OR,...
     * @param bool $not - отрицание
     * @return Request
     */
    public function whereIn($field, array $values, $prefix = 'AND', $not = false)
    {
        if ($this->last_action == 'openwgroup') {
            $prefix = '';
            $this->last_action = 'whereIn';
        }
        $field = str_replace('.', '`.`', ltrim($field));
        $particle = $not ? 'NOT IN' : 'IN';
        $expr = "`$field` $particle (" . implode(",", HelperTools::arrayQuote($values)) . ')';
        $this->where .= empty($this->where) ? $expr : " $prefix " . $expr;

        return $this;
    }

    /**
     * Открывает скобку перед началом условия
     *
     * @param mixed $prefix
     * @return Request
     */
    public function openWGroup($prefix = 'AND')
    {
        $this->where .= empty($this->where) ? ' ( ' : " $prefix ( ";
        $this->last_action = 'openwgroup';
        return $this;
    }

    /**
     * Закрывает скобку после окончания условий
     * @return $this;
     */
    public function closeWGroup()
    {
        $this->where .= ')';
        return $this;
    }

    /**
     * Задает секцию LIMIT
     * можно передать 2 параметра, первый задает смещение, второй количество результатов
     *
     * @param mixed $value
     * @return Request
     */
    public function limit($value)
    {
        $args = func_get_args();
        if (count($args) == 2) {
            $this->offset($args[0]);
            $value = $args[1];
        }
        $this->limit = $value;
        return $this;
    }

    /**
     * Задает параметр offset секции LIMIT
     *
     * @param mixed $value
     * @return Request
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * Задает секцию ORDER BY
     *
     * @param string $expr
     * @param array $values массив со значениям, заменит "-КЛЮЧ-" из expr на ЗНАЧЕНИЕ
     * @return Request
     */
    public function orderby($expr, array $values = null)
    {
        if ($values !== null) {
            foreach ($values as $key => $val) {
                $expr = str_replace("#$key", DbAdapter::escape($val), $expr);
            }
        }

        $this->orderby = $expr;
        return $this;
    }

    /**
     * Выполняет запрос к базе
     *
     * @return DbResult
     * Исключение \RS\Db\Exception оставлено на ручной контроль
     */
    public function exec()
    {
        $sql = $this->toSql();
        return DbAdapter::sqlExec($sql);
    }

    /**
     * Перегрузка возврата в представлении в виде строки
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toSql();
    }

    /**
     * Возвращает количество результатов звпроса. Подставляет COUNT(*) в секцию SELECT
     *
     * @return int
     */
    public function count()
    {
        $q = clone $this;
        $q->select = 'COUNT(*) cnt';
        return $q->exec()->getOneField('cnt', 0);
    }

    /**
     * Заполняет секцию select у запроса
     *
     * @param mixed $expression , mixed $expression, ....
     * @return Request
     */
    public function select($expression = null)
    {
        $this->action = 'select';
        if ($expression !== null) {
            $args = func_get_args();
            foreach ($args as $fields) {
                //Если используется формат, разбивающий результат по объектам
                if (preg_match('/\{(.*)\}:(.*)/u', $fields, $match)) {
                    $obj_class = $match[2];
                    $start = substr_count($this->select, ',');
                    $this->map_obj[] = [
                        'start' => ($start > 0) ? $start + 1 : 0,
                        'length' => substr_count($match[1], ',') + 1,
                        'class' => $obj_class,
                        'fields' => $this->parseFields($match[1])
                    ];
                    $fields = $match[1];
                }
                $this->select .= empty($this->select) ? $fields : ', ' . $fields;
            }
        }
        return $this;
    }

    /**
     * Возвращает только имена колонок по порядку без лишних знаков
     * Может парсить поля вида:
     * V.field => field
     * `base`.`table`.`field` => field
     * COUNT(*) as field2 => field2
     * original as 'newfield' => newfield
     *
     * @param string $str - строка со списком колонок
     * @return string[]
     */
    protected function parseFields($str)
    {
        $ret = [];
        $arr = explode(',', $str);
        foreach ($arr as $field) {
            if (preg_match('/^.*? as (.*)/iu', $field, $match)) {
                $field = $match[1];
            } elseif (preg_match('/\.([^.]+)$/', $field, $match)) {
                $field = $match[1];
            }
            $ret[] = trim($field, " `'");
        }
        return $ret;
    }

    /**
     * Добавляет выражение в секцию HAVING
     *
     * @param mixed $value
     * @param mixed $prefix
     * @return Request
     */
    public function having($value, $prefix = 'AND')
    {
        $this->having .= empty($this->having) ? $value : " $prefix " . $value;
        return $this;
    }

    /**
     * Задает секцию GROUP BY
     *
     * @param mixed $value
     * @return Request
     */
    public function groupby($value)
    {
        $this->groupby = $value;
        return $this;
    }

    /**
     * Задает класс возвращаемых объектов
     *
     * @param string|AbstractObject $class
     * @return Request
     */
    public function setReturnClass($class)
    {
        $this->return_class = is_object($class) ? get_class($class) : $class;
        return $this;
    }

    /**
     * Выполняет запрос и возвращает список объектов.
     * Если ничеко не найдено, то возврщает пустой список.
     *
     * @param mixed $class_name - класс возвращаемых объектов
     * @param string $key - имя поля которое будет использоватся в качестве ключа результирующего массива объектов
     * @param bool $allow_sublist - разрешает более одного элемета для одного и того же ключа.
     * @return AbstractObject[]
     * Исключение \RS\Orm\Exception оставлено на ручной контроль
     */
    public function objects($class_name = null, $key = null, $allow_sublist = false)
    {
        $class_name = $this->getReturnClassName($class_name);

        $resource = $this->exec();

        $ret = [];
        while ($row = $resource->fetchRow()) {
            /** @var AbstractObject $object */
            $object = new $class_name();
            $object->getFromArray($row, null, false, true);

            if ($key === null) {
                $ret[] = $object;
            } else {
                if ($allow_sublist) {
                    $ret[$object[$key]][] = $object;
                } else {
                    $ret[$object[$key]] = $object;
                }
            }
        }
        return $ret;
    }

    /**
     * Возвращает первый объект в выборке
     *
     * @param mixed $class_name
     * @return AbstractObject|bool
     */
    public function object($class_name = null)
    {
        $ret = $this->objects($class_name);
        return (count($ret) > 0) ? $ret[0] : false;
    }

    /**
     * Возвращает имя класса объектов для выдачи результата запроса
     *
     * @param object|string|null $class_name - запрошеный класс возвращаемых объектов
     * @return string
     * @throws OrmException
     */
    protected function getReturnClassName($class_name = null)
    {
        if (!$class_name) {
            $class_name = $this->return_class;
        }
        if (is_object($class_name)) {
            $class_name = get_class($class_name);
        }
        if (!$class_name) {
            throw new OrmException(t('Не задан класс возвращаемых объектов'));
        }
        return $class_name;
    }

    /**
     * Возвращает SQL запрос SELECT в текстовом виде
     */
    public function selectToSql()
    {
        $select = ($this->select) ? $this->select : "*";
        $ret = "SELECT {$select} FROM {$this->from}";
        if ($this->joins) {
            foreach ($this->joins as $join) {
                $ret .= ' ' . $join;
            }
        }
        if ($this->where) {
            $ret .= " WHERE {$this->where}";
        }
        if ($this->groupby) {
            $ret .= " GROUP BY {$this->groupby}";
        }
        if ($this->having) {
            $ret .= " HAVING {$this->having}";
        }
        if ($this->orderby) {
            $ret .= " ORDER BY {$this->orderby}";
        }
        if ($this->limit) {
            $limit = (int)$this->limit;
            $offset = (int)$this->offset;
            $ret .= " LIMIT $offset, $limit";
        }
        return $ret;
    }

    /**
     * Задает секцию UPDATE
     *
     * @param string | array $table
     * @param boolean | null $ignore
     * @return Request
     */
    function update($table = null, $ignore = null)
    {
        $this->action = 'update';
        if ($ignore != null) {
            $this->ignore = $ignore;
        }
        if ($table !== null) {
            $this->from($table);
        }
        return $this;
    }

    /**
     * Добавляет секцию SET (в UPDATE)
     *
     * @param mixed $value
     * @return Request
     */
    function set($value)
    {
        if (is_array($value)) {
            $str = [];
            foreach ($value as $key => $val) {
                $key = str_replace('.', '`.`', ltrim($key));
                if ($val === null) {
                    $str[] = "`$key` = NULL";
                } else {
                    $val = DbAdapter::escape($val);
                    $str[] = "`$key` = '$val'";
                }
            }
            $value = implode(",", $str);
        }
        $this->set .= empty($this->set) ? $value : ", " . $value;
        return $this;
    }

    protected function getIgnore()
    {
        return $this->ignore ? 'IGNORE' : '';
    }

    /**
     * Возвращает SQL запрос UPDATE в текстовом виде
     */
    public function updateToSql()
    {
        $ret = "UPDATE {$this->getIgnore()} {$this->from}";
        if ($this->joins) {
            foreach ($this->joins as $join) {
                $ret .= ' ' . $join;
            }
        }
        $ret .= " SET {$this->set}";

        if ($this->where) {
            $ret .= " WHERE {$this->where}";
        }
        if ($this->orderby) {
            $ret .= " ORDER BY {$this->orderby}";
        }
        if ($this->limit) {
            $limit = (int)$this->limit;
            $ret .= " LIMIT $limit";
        }
        return $ret;
    }

    /**
     * Добавляет секцию DELETE
     *
     * @param mixed $table
     * @param bool $ignore - флаг "IGNORE"
     * @return Request
     */
    function delete($table = null, $ignore = false)
    {
        $this->action = 'delete';
        if ($ignore != null) {
            $this->ignore = $ignore;
        }

        $args = is_array($table) ? $table : [$table];
        foreach ($args as $table) {
            if ($table instanceof AbstractObject) {
                $table = $table->_getTable();
            }
            $this->delete .= empty($this->delete) ? $table : ', ' . $table;
        }
        return $this;
    }

    /**
     * Возвращает SQL запрос DELETE в текстовом виде
     */
    function deleteToSql()
    {
        $ret = "DELETE {$this->getIgnore()} {$this->delete} FROM {$this->from}";
        if ($this->joins) {
            foreach ($this->joins as $join) {
                $ret .= ' ' . $join;
            }
        }
        if ($this->where) {
            $ret .= " WHERE {$this->where}";
        }
        if ($this->orderby) {
            $ret .= " ORDER BY {$this->orderby}";
        }
        if ($this->limit) {
            $limit = (int)$this->limit;
            $ret .= " LIMIT $limit";
        }
        return $ret;
    }

    /**
     * Добавляет секцию INSERT
     *
     * @param AbstractObject|string|array $table - имя таблицы или объект ORM
     * @param array|string $columns - список столбцов
     * @param array|string $on_duplicate_key_update - список полей, которые будут обновлены если запись существует
     * @return self
     */
    public function insert($table, $columns = null, $on_duplicate_key_update = null): self
    {
        $this->action = 'insert';
        if ($table !== null) {
            $this->from($table);
        }
        if ($columns !== null) {
            $this->columns = is_array($columns) ? implode(',', $columns) : $columns;
        } else {
            $this->columns = null;
        }
        if ($on_duplicate_key_update !== null) {
            if (!is_array($on_duplicate_key_update)) {
                $on_duplicate_key_update = explode(',', $on_duplicate_key_update);
            }
            $parts = [];
            foreach ($on_duplicate_key_update as $field) {
                $parts[] = "$field = values($field)";
            }
            $this->on_duplicate_key_update = implode(', ', $parts);
        } else {
            $this->on_duplicate_key_update = null;
        }

        return $this;
    }

    /**
     * Добавляет секцию VALUES
     *
     * @param array $values - значения
     * @param bool $two_dimensional - флаг того что передан двумерный массив значений
     * @return $this
     */
    public function values(array $values, bool $two_dimensional = false): self
    {
        $escaped_values = [];
        foreach ($values as $key => $value) {
            if ($two_dimensional) {
                foreach ($value as $sub_key => $sub_value) {
                    $escaped_values[$key][$sub_key] = ($sub_value === null) ? 'NULL' : DbAdapter::escape($sub_value);
                }
            } else {
                $escaped_values[$key] = ($value === null) ? 'NULL' : DbAdapter::escape($value);
            }
        }

        if ($two_dimensional) {
            $new_values = [];
            foreach ($escaped_values as $item) {
                $new_values[] = '("' . implode('","', $item) . '")';
            }
            $new_values = implode(',', $new_values);
        } else {
            $new_values = '("' . implode('","', $escaped_values) . '")';
        }

        if ($this->values) {
            $this->values .= ',';
        }
        $this->values .= $new_values;

        return $this;
    }

    /**
     * Возвращает SQL запрос INSERT в текстовом виде
     */
    public function insertToSql(): string
    {
        $ret = "INSERT INTO {$this->from}";
        if ($this->columns) {
            $ret .= " ({$this->columns})";
        }
        if ($this->values) {
            $ret .= " VALUES {$this->values}";
        }
        if ($this->on_duplicate_key_update) {
            $ret .= " ON DUPLICATE KEY UPDATE {$this->on_duplicate_key_update}";
        }
        return $ret;
    }

    /**
     * Возвращает SQL зарос исходя из заданного раннее типа SELECT, UPDATE, DELETE,...
     */
    public function toSql()
    {
        $action = $this->action . 'ToSql';
        return $this->$action();
    }
}
