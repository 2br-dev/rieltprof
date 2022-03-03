<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Filter\Type;

use RS\Db\Adapter;
use RS\Exception;
use RS\Html\AbstractHtml;
use RS\Http\Request;
use RS\View\Engine;

/**
* Базовый абстрактный класс элементов(полей) фильтра. 
*/
abstract class AbstractType extends AbstractHtml
{
    public
        $wrap_var,
        $emptynull = true; //true - считать пустое значение, не установленнным фильтром
    
    protected
        $modificate_query_callback,
        $type_array_sql = [1 => '<', '2' => '>', 3 => '='],
        $is_prefilter = false,
        $field_prefix = '', //если задан префикс, то в sqlwhere будет дополнять строку префиксом: prefix.key = 'value';
        $search_type = 'eq',
        $title_attr = ['class' => 'standartkey'],
        $attr = [],
        $trim = true,
        $abstract_tpl = 'system/admin/html_elements/filter/type/abstract.tpl',
        $key,
        $title,
        $value = '',
        $prefilter_list = [];
    
    function __construct($key, $title, $options = [])
    {
        $this->key = $key;
        $this->title = $title;
        $this->type_array = [1 => t('меньше, чем'), '2' => t('больше, чем'), 3 => t('равно')];
        
        parent::__construct($options);
    }
    
    /**
    * Устанавливает считать ли пустую строку - неустановленным значением фильтра
    * 
    * @param bool $empty_null true или false
    * @return $this
    */
    function setEmptyNull($empty_null)
    {
        $this->emptynull = $empty_null;
        return $this;
    }
    
    /**
    * Устанавливает тип сравнения значения фильтра с базой данных
    * 
    * @param string 'eq' | '%like' | '%like%' $search_type - Если задано eq, 
    * то будет точное сравнение (=), если задано like - значит будет осуществлен поиск частичного совпадения
    * 
    * @return AbstractType
    */
    function setSearchType($search_type) 
    {
        $this->search_type = $search_type;
        return $this;
    }
    
    /**
    * Устанавливает Псевдоним таблицы, который будет добавлен перед колонкой в SQL запросе.
    * 
    * @param string $prefix - Псевдоним таблицы.
    * @return AbstractType
    */
    function setFieldPrefix($prefix) 
    {
        $this->field_prefix = $prefix;
        return $this;
    }
    
    /**
    * Устанавливает аттрибуты к dom-элементу, содержащему название фильтра
    * 
    * @param array $attr - Массив, где ключ - это аттрибут, значение - значение аттрибута
    * @return AbstractType
    */
    function setTitleAttr(array $attr) 
    {
        $this->title_attr = $attr;
        return $this;
    }
    
    /**
    * Устанавливает аттрибуты для dom-элмента формы.
    * 
    * @param array $attr - Массив, где ключ - это аттрибут, значение - значение аттрибута
    * @return AbstractType
    */
    function setAttr(array $attr) 
    {
        $this->attr = array_merge_recursive($this->attr, $attr);
        return $this;
    }
    
    /**
    * Устанавливает, нужно ли обрезать пробелы по краям поисковой фразы
    * 
    * @param mixed $bool
    * @return AbstractType
    */
    function setTrim($bool)
    {
        $this->trim = $bool;
        return $this;
    }
    
    /**
    * Возвращает, будет ли обрезать пробелы по краям строки данная форма
    * 
    * @return bool
    */
    function getTrim()
    {
        return $this->trim;
    }
    
    /**
    * Устанавливает значение фильтра
    * 
    * @param mixed $value
    * @return AbstractType
    */
    function setValue($value) 
    {
        if ($this->trim && is_string($value)) {
            $value = trim($value);
        }
        $this->value = $value;
        return $this;
    }
    
    /**
    * Возвращает true, если данный фильтр является дополнительным к другому фильтру
    * 
    * @return bool
    */
    function isPrefilter() 
    {
        return $this->is_prefilter;
    }
    
    /**
    * Возвращает список дополнительных фильтров для данного фильтра
    * 
    * @return array
    */
    function getPrefilters() 
    {
        return $this->prefilter_list;
    }
    
    /**
    * Возвращает ключ фильтра. (ключ сответствует колонке в БД)
    * 
    * @return string
    */
    function getKey() 
    {
        return $this->key;
    }
    
    /**
    * Возвращает название фильтра
    * 
    * @return string
    */
    function getTitle() 
    {
        return $this->title;
    }
    
    /**
    * Возвращает значение фильтра
    * 
    * @return mixed
    */
    function getValue() 
    {
        return $this->value;
    }            
    
    /**
    * Возвращает значение фильтра, которое подготовлено для чтения пользователем
    * 
    * @return string
    */
    function getTextValue() 
    {
        return $this->value;
    }
    
    /**
    * Возвращает аттрибуты для элемента, содержащего название фильтра
    * 
    * @return string
    */
    function getTitleAttrString()
    {
        $attr_string = '';
        foreach($this->title_attr as $key=>$val) {
            $attr_string .= " ".$key.'="'.$val.'"';
        }
        return $attr_string;
    }
    
    /**
    * Возвращает аттрибуты для формы фильтра
    * 
    * @return string
    */
    function getAttrString()
    {
        $attr_string = '';
        foreach($this->attr as $key=>$val) {
            $attr_string .= " ".$key.'="'.$val.'"';
        }
        return $attr_string;
    }
    
    /**
    * Добавляет к выводу дополнительную форму с выбором типа проверки < = > (больше, меньше или равно)
    */
    function setShowType()
    {
        $this->addPrefilter(new Select('type_'.$this->key, '', $this->type_array));
    }
    
    /**
    * @param mixed $list
    */
    function addPrefilter(AbstractType $item)
    {
        $item->_setPrefilterFlag();
        $this->prefilter_list[$item->getKey()] = $item;
    }
    
    function _setPrefilterFlag()
    {
        $this->is_prefilter = true;
    }
    
    function getSqlKey()
    {
        if (empty($this->field_prefix)) return $this->key; else return $this->field_prefix.'.'.$this->key;
    }
    
    function getName()
    {
        return $this->wrap_var."[{$this->key}]";
    }

    /**
     * Возвращает null, если филтр не установлен, иначе значение фильтра
     */
    function getNonEmptyValue()
    {
        if ($this->value != '' || !$this->emptynull) {
            return $this->getValue();
        }
        return null;
    }
    
    /**
    * Возвращает ключ-значение, поля в виде ассоциативного массива, если есть значение, иначе пустой массив
    * @return array
    */
    function getKeyVal()
    {
        if ($this->value=='' && $this->emptynull) return [];
        return [$this->key => $this->getValue()];
    }

    /**
     * Устанавливает функцию, которая будет вызываться для модификации запроса к БД
     * В функцию будут переданы аргументы: $this, \RS\Orm\Request $q
     *
     * @param callable $callback
     * @return void
     */
    function setModificateQueryCallback($callback)
    {
        $this->modificate_query_callback = $callback;
    }
    
    function modificateQuery(\RS\Orm\Request $q)
    {
        if ($this->modificate_query_callback) {
            $q = call_user_func($this->modificate_query_callback, $q, $this);
            if (!($q instanceof \RS\Orm\Request)) {
                throw new Exception(t('Callback функция должна возвращать объект \RS\Orm\Request'));
            }
        }

        return $q;
    }
    
    function getWhere()
    {
        if ($this->value=='' && $this->emptynull) return '';

        if (strpos($this->search_type, 'like') !== false) {
            return $this->where_like($this->search_type);
        }

        $func_name = 'where_'.$this->search_type;
        return $this->$func_name();
    }
    
    /**
    * Сравнивает строку используя инструкцию LIKE 'NEEDLE%'
    */
    protected function where_like($likepattern)
    {
        $value = str_replace('like', $this->escape($this->getValue()), $likepattern);
        return "{$this->getSqlKey()} like '{$value}'";
    }
    
    /**
    * Сравнивает используя равенство или если включен showtypes, использует > (больше) или < (меньше)
    */
    protected function where_eq($compare = '=')
    {
        if (!empty($this->prefilter_list)) 
        {
            foreach($this->prefilter_list as $prefilter)
                if ($prefilter->getKey() == 'type_'.$this->key) 
                {
                    $compare = isset($this->type_array_sql[$prefilter->getValue()]) ? $this->type_array_sql[$prefilter->getValue()] : '=';
                    break;
                }
        }
        return "{$this->getSqlKey()} $compare  '{$this->escape($this->getValue())}'";
    }
    
    /**
    * Сравнивает используя равенство или если включен showtypes, использует > (больше) или < (меньше)
    */
    protected function where_noteq()
    {
        return $this->where_eq('!=');
    }    
    
    protected function escape($str)
    {
        return Adapter::escape($str);
    }

    /**
    * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
    * 
    * @param array $current_filter_values - значения установленных фильтров
    * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
    * @return array of array ['title' => string, 'value' => string, 'href_clean']
    */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];
        if ($this->getNonEmptyValue() !== null) {
            
            $without_this = $current_filter_values;
            unset($without_this[$this->getKey()]);
            
            $prefilters = '';
            foreach($this->getPrefilters() as $prefilter) {
                $prefilters .= $prefilter->getTextValue().' ';
            }
            
            $exclude = array_combine($exclude_keys, array_fill(0, count($exclude_keys), null));
            
            $parts[] = [
                'title' => $this->getTitle(),
                'value' => $prefilters.$this->getTextValue(),
                'href_clean' => Request::commonInstance()->replaceKey([$this->wrap_var => $without_this] + $exclude) //Url, для очистки данной части фильтра
            ];
        }
        return $parts;
    }
    
    
    function getView()
    {        
        $wrap = new Engine();
        $wrap->assign('fitem', $this);         
        return $wrap->fetch($this->abstract_tpl);
    }
}

