<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm;

/**
* Класс отвечает за список свойств Orm объекта. 
* Позволяет выполнять массовые операции со всеми свойствами.
* Помещать свойства в группы, которые в дальнейшем будут представлены как закладки в форме
*/
class PropertyIterator implements \ArrayAccess, \Iterator
{
    private 
        $multiedit_keys = [],
        $data = [],
        $valid = false,
        $group_set,
        $groups = [],
        $currentgroup = null;

    /**
     * Конструктор
     *
     * @param array $properties - массив элементов \RS\Orm\AbstractType | string  со свойствами или именем группы
     */
    function __construct(array $properties = null) {
        $this->currentgroup = t('Основные'); //Группа по умолчанию
        if ($properties) {
            $this->append($properties);
        }
    }
    
    /**
    * Дополняет список свойств
    * 
    * @param mixed $properties
    * @return PropertyIterator
    */
    public function append(array $properties)
    {
        foreach($properties as $key => $value) {
            if (is_object($value)) {
                $this[$key] = $value;
            } else {
                $this->group($value);
            }
        }
        return $this;
    }

    /**
     * Устанавливает текущую группу, для отображения
     *
     * @param string $name - Название группы
     * @return PropertyIterator
     */
    public function group($name)
    {
        $this->currentgroup = $name;
        return $this;
    }   
    
    //начало свойств интерфейса итератора

    /**
     * Возвращает true, если поле с ключем $offset присутствует. ArrayAccess
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Возвращает значение по ключу $offset. ArrayAccess
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        return null;
    }

    /**
     * Устанавливает значение ключу $offset. ArrayAccess
     *
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Type\AbstractType)) throw new Exception(t("Неправильно установлено свойство %0", [$offset]));
        $this->data[$offset] = $value;
        
        //Помещаем в группу (будет отображаться в отдельной закладке)
        if (isset($this->currentgroup)) {
            $this->groups[$this->currentgroup][$offset] = $value;
        }
        
        //Устанавливаем свойста, если задан groupSet
        if (isset($this->group_set)) {
            foreach($this->group_set as $property => $val) {
                $value->processOptions([$property => $val]);
            }
        }
        return $value;
    }

    /**
     * Удаляет значение для ключа $offset. ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Возвращает значение по текущему указателю. Iterator
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Возвращает ключ текущего элемента. Iterator
     *
     * @return int|mixed|string|null
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Переводит на следующий элемент. Iterator
     *
     * @return mixed
     */
    public function next()
    {
        return $this->valid = next($this->data);
    }

    /**
     * Переводит итератор на первый элемент. Iterator
     *
     * @return bool
     */
    public function rewind()
    {
        return $this->valid = reset($this->data);
    }

    /**
     * Проверяет корректность текущей позиции. Iterator
     *
     * @return bool
     */
    public function valid()
    {
        return $this->valid;
    }
    
    //конец свойств интерфейса итератора

    /**
     * Вызывается перед сохранением ORM объекта, через метод $orm->save()
     */
    public function beforeSave()
    {
        foreach ($this->data as $key=>$property) $property->beforeSave();
    }

    /**
     * Возвращает заданные раннее группы свойств
     *
     * @param bool | null $hide_empty_multiedit - Если задано true, то скрывать пустые закладки для режима мультиредактирования, false - скрывать для режима обычнго редктирования, null - возвращать все.
     * @param string | null $switch - Контекст отображения
     * @param bool $use_default_visible - Использовать свойство Visible, если не задан отдельный <switch>Visible
     * для контекста switch
     * @return array
     */
    public function getGroups($hide_empty_multiedit = null, $switch = null, $use_default_visible = true)
    {
        $index = array_flip(array_keys($this->groups));
        $groups = $this->groups;
        
        $check_func = $hide_empty_multiedit ? 'isMeVisible' : 'isVisible';
        if ($hide_empty_multiedit === null) $check_func = '-';

        $cache_id = (int)$hide_empty_multiedit.$switch.(int)$use_default_visible;

        if (!isset($this->cache_groups[$cache_id])) {
            $result = [];
            foreach($groups as $name => $items) {
                foreach($items as $k => $item) {
                    if ($check_func == '-' || $item->$check_func($switch, $use_default_visible)) {
                        $result[$index[$name]]['group'] = $name;
                        $result[$index[$name]]['items'][$k] = $item;
                    }
                }
            }
            $this->cache_groups[$cache_id] = $result;
        }
        
        return $this->cache_groups[$cache_id];
    }

    /**
    * Возвращает список групп
    * 
    * @param bool | null $hide_empty_multiedit - Если задано true, то скрывать пустые закладки для режима мультиредактирования, false - скрывать для режима обычнго редктирования, null - возвращать все.
    * @return array
    */    
    public function getGroupList($hide_empty_multiedit = null)
    {
        return array_keys($this->getGroups($hide_empty_multiedit));
    }

    /**
     * Возвращает имя группы
     *
     * @param integer $n Порядковый номер группы
     * @return array
     */
    public function getGroupName($n)
    {
        $list = array_keys($this->groups);
        return $list[$n];
    }

    /**
     * Возвращает значение свойств групп
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        foreach($this->data as $key=>$property) {
            $values[$key] = $property->get();
        }
        return $values;
    }

    /**
     * Устанавливает значения свойствам
     *
     * @param array $values
     * @return $this
     */
    public function setValues(array $values)
    {
        foreach($this->data as $key=>$property)
        {
            if (isset($values[$key])) {
                $this->data[$key]->set($values[$key]);
            } else {
                $this->data[$key]->set(null);
            }
        }
        return $this;
    }

    /**
     * Возвращает ключи свойств
     *
     * @return array|false
     */
    public function getKeys()
    {
        $keys = array_keys($this->data);
        return array_combine($keys, $keys);
    }

    /**
     * Возвращает ключи видимых свойств
     *
     * @return array
     */
    public function getVisibleKeys()
    {
        $tmp = [];
        foreach ($this->data as $key=>$prop) {
            if ($prop->isVisible()) $tmp[$key] = $key;
        }
        return $tmp;
    }

    /**
     * Возвращает ключи свойств, которые видимы в мультиредактировании
     *
     * @return array
     */
    public function getMultieditKeys()
    {
        $result = [];
        foreach ($this->data as $key => $prop) {
            if ($prop->isUseToSave()) $result[$key] = $key;
        }
        return $result + $this->multiedit_keys;
    }

    /**
     * Добавляет в список ключи свойств, которые должны быть видимы при мультиредактировании
     *
     * @param $key
     * @return $this
     */
    public function addMultieditKey($key)
    {
        if (is_array($key)) {
            $this->multiedit_keys = array_combine($key, $key);
        } else {
            $this->multiedit_keys[$key] = $key;
        }
        return $this;
    }

    /**
     * Установит свойству $property значение $value
     * при вызове offsetSet
     *
     * @param string $property
     * @param mixed $value
     * @return self
     */
    public function groupSet($property, $value)
    {
        if ($value === null) {
            unset($this->group_set[$property]);
        } else {
            $this->group_set[$property] = $value;
        }
        return $this;
    }

    /**
    * Отменяет установку свойств
    */
    public function cancelGroupSet()
    {
        $this->group_set = null;
        return $this;
    }

    /**
     * Оборачивает имена форм всех свойств в массив
     *
     * @param string $array_wrap_name
     * @return PropertyIterator
     */
    public function arrayWrap($array_wrap_name)
    {
        foreach($this->data as $key=>$property) {
            $property->setArrayWrap($array_wrap_name);
        }
        return $this;
    }
    
    /**
    * Возвращает массив с объектами свойств
    * 
    * @return array
    */
    public function export()
    {
        return $this->data;
    }

    /**
     * Возвращает список всех групп
     *
     * @return array
     */
    public function exportGroups()
    {
        return $this->groups;
    }

    /**
     * Дополняет свойства новыми полями из $property_iterator
     *
     * @param PropertyIterator $property_iterator
     * @param bool $overwrite - Если true, то добавляемые свойства будут заменять имеющиеся
     * @return $this
     */
    public function appendPropertyIterator(PropertyIterator $property_iterator, $overwrite = false)
    {
        $this->groups = array_replace_recursive($this->groups, $property_iterator->exportGroups() );
        if ($overwrite) {
            $this->data = $property_iterator->export() + $this->data;
        } else {
            $this->data += $property_iterator->export();
        }

        return $this;
    }

    /**
     * Устанавливает параметры для всего набора свойств
     *
     * @param $options
     * @return PropertyIterator
     */
    public function setPropertyOptions($options)
    {
        foreach($this->data as $key=>$property) {
            $property->processOptions($options);
        }
        return $this;        
    }
}