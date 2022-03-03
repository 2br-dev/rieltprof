<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Filter;

/**
* Фильтр по характеристикам в административной панели.
*/
class PropertyFilter extends \RS\Html\Filter\Type\AbstractType
{
    protected
        $property_api,
        $properties = [],
        $abstract_tpl = '%catalog%/filter/property_filter.tpl';
    
    /**
    * Конструктор для фильтра по полю характеристика
    * 
    * @param array of \Catalog\Orm\Property\Item $property_ids
    * @param array $options
    * @return PropertyFilter
    */
    function __construct($properties = [], $options = [])
    {
        $this->properties = $properties;
        $this->property_api = new \Catalog\Model\PropertyApi();
        parent::__construct('_p', '', $options);
    }
    
    /**
    * Возвращает свойства, которые должны отображаться в фильтре
    * @return array();
    */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
    * Возвращает массив установленных фильтров
    * 
    * @return array
    */
    function getValue() {
        return (array)$this->value;
    }
    
    /**
    * Не возвращает условие для выборки, т.к. это делает modificateQuery
    * 
    * @return string возвращает пустую строку
    */
    function getWhere()
    {
        return '';
    }
    
    /**
    * Модифицирует запрос с учетом выбранных фильтров
    * 
    * @param \RS\Orm\Request $q - объект выборки данных из базы
    * @return \RS\Orm\Request
    */
    function modificateQuery(\RS\Orm\Request $q)
    {
        $q = $this->property_api->getFilteredQuery($this->getValue(), 'A', $q);
    }
    
    /**
    * Возвращает количество активных фильтров
    * 
    * @return int;
    */
    function isActiveFilter()
    {
        return count($this->property_api->cleanNoActiveFilters($this->getValue()));
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
        
        $filters = $this->property_api->cleanNoActiveFilters($this->getValue());
        
        foreach($filters as $prop_id => $value) {

            $without_this = $current_filter_values;
            unset($without_this[$this->getKey()][$prop_id]);
            
            $property = $this->properties[$prop_id];
            $view_parts = (isset($value['empty'])) ? [t('-Не задана-')] : [];
            
            if ($property->isListType()) {
                $view_parts += $property->valuesArr($value);
            } 
            elseif ($property['type'] == 'int') {
                $tmp = '';
                if (!empty($value['from'])) {
                    $tmp = t('от ').$value['from'].' ';
                }
                if (!empty($value['to'])) {
                    $tmp .= t('до ').$value['to'];
                }
                if (!empty($tmp)) {
                    $view_parts[] = $tmp;
                }
            }
            elseif ($property['type'] == 'bool') {
                if (isset($value['value']) && $value['value'] !== '') {
                    $view_parts[] = !empty($value['value']) ? t('Есть') : t('Нет');
                }
            }
            else {
                if (!empty($value['value'])) {
                    $view_parts[] = $value['value'];
                }
            }
            
            $value = implode(t(' или '), $view_parts);
            
            $parts[] = [
                'title' => $property->title,
                'value' => $value,
                'href_clean' => \RS\Http\Request::commonInstance()->replaceKey([$this->wrap_var => $without_this]) //Url, для очистки данной части фильтра
            ];
        }
        return $parts;
    }    
    
}

