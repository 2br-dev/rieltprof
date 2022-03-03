<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

/**
* Класс приводит базу данных в соответствие со структурой объекта
*/
class DbMap
{
    private 
        $dbfields = [],
        $dbindexes = [],
        $properties = null,
        $indexes = [],
        $skip_update_indexes = [],
        $no_table = false,
        $db = null,
        $table = null,
        $engine = null,
        $charset = null;
        
    /**
    * Конструктор
    * 
    * @param PropertyIterator $properties - свойства ORM Объекта
    * @param array $indexes - индексы ORM объекта
    * @param string $db - имя базы данных
    * @param string $table - имя таблицы
    * @param string $engine - тип таблицы
    * @param string $charset - кодировка таблицы
    * @return DbMap
    */
    public function __construct(PropertyIterator $properties, array $indexes, $db, $table, $engine="MyISAM", $charset="utf8")
    {
        $this->properties = $properties;
        $this->indexes = $indexes;
        $this->table = $table;
        $this->db = $db;
        $this->engine = $engine;
        $this->charset = $charset;
        $dbresult = \RS\Db\Adapter::sqlExec("show tables from `$db` like '$table'");
        if ($dbresult -> rowCount() == 0) {
            $this->no_table = true;
        } else
        {
            $this->dbfields = $this->getDbFields();
            $this->dbindexes = $this->getDbIndexes();
        }
    }
    
    /**
    * Возвращает поля, которые сейчас присутствуют в системе
    * 
    * @return array
    */
    private function getDbFields()
    {
        $fields = [];
        $dbresult = \RS\Db\Adapter::sqlExec("show fields from `{$this->db}`.`{$this->table}`");
        while($row = $dbresult->fetchRow()) {
            $fields[$row['Field']] = $row;
        }
        
        return $fields;
    }
    
    /**
    * Возвращает индексы, которые в данный момент присутствуют в таблице
    * 
    * @return array
    */
    private function getDbIndexes()
    {
        $indexes = [];
        $dbresult = \RS\Db\Adapter::sqlExec("show indexes from `{$this->db}`.`{$this->table}`");
        while($row = $dbresult->fetchRow()) {
            
            if (!isset($indexes[$row['Key_name']])) {
                $indexes[$row['Key_name']] = [
                    'unique' => $row['Non_unique'],
                    'type' => $row['Index_type']
                ];
            }
            
            $indexes[$row['Key_name']]['fields'][$row['Seq_in_index']-1] = $row['Column_name'];
        }
        
        foreach($indexes as $index) {
            ksort($index['fields']);
        }
        
        return $indexes;
    }
    
    /**
    * Возвращает списокзапросов, необходимых для создания таблицы
    * 
    * @return array
    */
    private function createTable()
    {
        $queries = [];
        $fields = []; //директивы sql полей
        
        foreach ($this->properties as $name => $prop) //обработка полей
        {
            if ($prop->isRuntime()) continue;
            $fields[] = $this->FieldFormat($name, $prop, true);
        }

        foreach ($this->properties as $name=>$prop) //обработка индексов
        {
            if ($prop->isPrimaryKey()) {
                $fields[] = "PRIMARY KEY (`$name`)";
                
                foreach($this->indexes as $key => $index) {
                    if ($index['type'] == \RS\Orm\AbstractObject::INDEX_PRIMARY) {
                        unset($this->indexes[$key]);
                    }
                }
            }
        }
        
        $queries[] = "CREATE TABLE `{$this->db}`.`{$this->table}` (".implode(",", $fields).") ENGINE={$this->engine} DEFAULT CHARSET={$this->charset}";
        
        $queries = array_merge($queries, $this->updateIndexes());
        return $queries;
    }
    
    
    /**
    * Возвращает SQL вид одного поля
    * 
    * @param string $name - имя поля
    * @param Type\AbstractType $prop - объект поля
    * @param integer $is_create - если true, значит будет формироваться строка 
    *        для запроса на создание таблицы, иначе на обновление
    * 
    * @return string
    */
    private function FieldFormat($name, \RS\Orm\Type\AbstractType $prop, $is_create = false)
    {   
        $len = ($prop->hasLength()) ? $prop->getMaxLength() : null;
        $autoinc = ($prop->isAutoincrement()) ? "AUTO_INCREMENT" : "";
        $allowempty = ($prop->isAllowEmpty()) ? "NULL" : "NOT NULL";
        
        $default = $prop->getDefault();
        if ($default !== null) {
            $default_value = $prop->isDefaultFunc() ? $default : "'".$default."'";
            $default = 'DEFAULT '.$default_value;
        }
        
        $comment = "";
        if( $prop->getDescription()) {
            $comment = "COMMENT '".\RS\Db\Adapter::escape($prop->getDescription())."'";
        }
        
        //Если это ALTER запрос для поля с primary_key и индекс раннее существовал,
        //то не нужно дописывать ADD PRIMARY KEY
        $skip_add_primary_index = $prop->isPrimaryKey() 
            && !$is_create 
            && isset($this->dbindexes['PRIMARY']) 
            && $this->dbindexes['PRIMARY']['fields'][0] == $name;
        
        $addprimary = '';
        if ($autoinc && $prop->isPrimaryKey() && !$is_create && !$skip_add_primary_index) {
            $addprimary = ", ADD PRIMARY KEY (`$name`)"; 
            $this->skip_update_indexes[] = "PRIMARY";
        }
        
        return "`$name` {$prop->getSQLNotation()}{$prop->getSQLTypeParameter()} $allowempty $default $autoinc $comment $addprimary";
    }
    
    /**
    * Возвращает список SQL запросов, необходимых для обновления таблицы в базе данных
    * 
    * @return array
    */
    public function UpdateTable()
    {
        $queries = [];
        foreach ($this->properties as $name=>$prop)
        {
            if (!$this->isSyncField($name))
            {
                if (isset($this->dbfields[$name]) === false) {
                    $queries[] = $this->addField($name);
                } else {
                    $queries[] = $this->alterField($name);
                }
            }
        }
        
        $queries = array_merge($queries, $this->updateIndexes());
        return $queries;
    }
    
    /**
    * Возвращает список SQL запросов, необходимых для обновления индексов в таблице
    * 
    * @return array
    */
    public function updateIndexes()
    {
        $queries = [];
        $delete_list = array_flip(array_keys($this->dbindexes));
        foreach($this->indexes as $index) {
            if (!$this->isSyncIndex($index) && !in_array($index['name'] ,$this->skip_update_indexes)) {
                $queries[] = $this->alterIndex($index['name']);
            } else {
                unset($delete_list[ $index['name'] ]);
            }
        }
        
        //Список индексов на удаление
        foreach($delete_list as $index => $n) {
            array_unshift($queries, $this->dropIndex($index));
        }
        return $queries;
    }
    
    /**
    * Приводит в соответствие структуру таблицы в базе данных.
    * Создает или обновляет таблицу в зависимости от ситуации
    * 
    * @return bool
    */
    public function sync()
    {
        $queries = $this->getSqlQueries();
        foreach($queries as $sql) {
            \RS\Db\Adapter::sqlExec($sql);
        }
        return true;
    }
    
    /**
    * Возвращает список SQL запросв, необходимых для приведения в соответствие структуры таблицы в базе данных
    * 
    * @return array
    */
    public function getSqlQueries()
    {
        if ($this->no_table) {
            return $this->createTable();
        } else {
            return $this->UpdateTable();
        }
    }
    
    /**
    * Возвращает true, если имеющийся индекс в БД полностью соответствует требуемому, иначе - false
    * 
    * @param array $index - требуемый индекс
    * @return bool
    */
    private function isSyncIndex($index)
    {
        $name = $index['name'];
        if ($index['type'] == \RS\Orm\AbstractObject::INDEX_PRIMARY) {
            $name = 'PRIMARY';
        }
        
        if (!isset($this->dbindexes[$name])) {
            return false;
        }
        $fields = $index['fields'];
        $dbfields = $this->dbindexes[$name]['fields'];
        asort($dbfields);
        asort($fields);
        
        return  implode('', $fields) === implode('', $dbfields);
    }
    
    /**
    * Возвращает SQL запрос для удаления индекса
    * 
    * @param string $name - имя индекса
    * @return string
    */
    private function dropIndex($name)
    {
        return "alter table `$this->db`.`$this->table` drop index `$name`";
    }
    
    /**
    * Возвращает SQL запрос для изменения индекса
    * 
    * @param string $name - имя индекса
    * @return string
    */
    private function alterIndex($name)
    {
        $index = $this->indexes[$name];
        $fields = implode(',', \RS\Helper\Tools::arrayQuote($index['fields'], null, '`'));
        $using = $index['using'] ? 'USING '.$index['using'] : '';
        $name_index = $index['type'] == \RS\Orm\AbstractObject::INDEX_PRIMARY ? '' : "`$name`";
        return "alter table `$this->db`.`$this->table` ADD {$index['type']} $name_index $using ($fields)";
    }
    
    /**
    * Возвращает SQL запрос, необходимый для обновления поля
    * 
    * @param string $name - имя поля
    * @return string
    */
    private function alterField($name)
    {
        $property = $this->properties[$name];
        return "alter table `$this->db`.`$this->table` change `$name` " . $this->FieldFormat($name,$property);
    }
    
    /**
    * Возвращает SQL запрос, необходимый для добавления поля
    * 
    * @param string $name - имя поля
    * @return string
    */
    private function addField($name)
    {
        $property = $this->properties[$name];
        return "alter table `$this->db`.`$this->table` add " . self :: FieldFormat($name,$property);
    }
    
    /**
    * Возвращает SQL запрос, необходимый для удаления поля
    * 
    * @param string $name - имя поля
    * @return string
    */
    private function dropField($name)
    {
        return "alter table `$this->db`.`$this->table` drop `$name`";
    }
    
    /**
    * Возврщает массив с данными о имеющемуся в базе полю, или false - в случае его отсутствия
    * 
    * @param string $name - имя поля
    * @return array | bool(false)
    */
    private function getDBFieldByName($name)
    {
        return isset($this->dbfields[$name]) ? $this->dbfields[$name] : null;
    }
    
    /**
    * Возвращает true, если свойсто в базе данныхпоностью соответствует требуемому
    * 
    * @param string $name - Имя поля
    * @return bool
    */
    private function isSyncField($name)
    {
        /** @var \RS\Orm\Type\AbstractType $property */
        $property = $this->properties[$name];
        if ($property->isRuntime()) return true;

        $dbfield = $this->getDBFieldByName($name);
        if ($dbfield === null) return false;

        $autoincrement = ($dbfield["Extra"]=="auto_increment")==$property->isAutoincrement();
        $null = ($dbfield["Null"]=="YES") == $property->isAllowEmpty();
        $type = ($dbfield["Type"]==$property->getSQLNotation() . $property->getSQLTypeParameter());
        $property_default = $property->getDefault(true);
        $default = ($property_default === false) || $property_default === $dbfield['Default'];
        
        return $autoincrement && $type && $null && $default;
    }
}
