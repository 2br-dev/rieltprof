<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;
use RS\Helper\Tools;
use \RS\Orm\Type;

/**
* API для значений характеристик
*/
class PropertyValueApi extends \RS\Module\AbstractModel\EntityList
{
    protected
        $filter_parts;
    
    function __construct()
    {
        parent::__construct(new Orm\Property\ItemValue, [
            'sortField' => 'sortn',
            'id_field' => 'id',
            'defaultOrder' => 'sortn',
            'alias_field' => 'alias',
            'multisite' => true
        ]);
    }
    
    /**
    * Устанавливает фильтры по комплектациям для карточки товара
    * 
    * @return void
    */
    function applyFormFilter($get_filters, $url_params = [])
    {
        $this->filter_parts = [];
        $allow_keys = ['value' => t('Значение')];
                        
        $router = \RS\Router\Manager::obj();
        
        foreach($get_filters as $key => $value) {
            if (!in_array($key, array_keys($allow_keys)) || $value === '') continue;
            
            $this->setFilter($key, $value, '%like%');
            
            $without_filter = $get_filters;
            unset($without_filter[$key]);
            
            $this->filter_parts[] = [
                'text' => $allow_keys[$key].': '.$value,
                'clean_url' => $router->getAdminUrl(false, ['pvl_filter' => $without_filter] + $url_params, 'catalog-block-propertyvaluesblock')
            ];
        }
    }
    
    /**
    * Возвращает установленные фильтры в карточке товара
    * 
    * @return array
    */
    function getFormFilterParts()
    {
        return $this->filter_parts;
    }    
    
    /**
    * Возвращает объект для построения формы быстрого добавления значений для списковых характеристик
    * 
    * @return \RS\Orm\FormObject
    */
    function getMultiValuesForm()
    {
        $form = new \RS\Orm\FormObject(new \RS\Orm\PropertyIterator([
            'prop_id' => new Type\Integer([
                'description' => t('Характеристика'),
                'visible' => false
            ]),
            'values' => new Type\Text([
                'description' => t('Значения (каждое с новой строки)'),
                'checker' => ['ChkEmpty', t('Укажите хотя бы одно значение')],
                'hint' => t('Если значение уже существует, то оно не будет добавлено.')
            ])
        ]));
        
        return $form;
    }
    
    /**
    * Массово создает значения
    * 
    * @param mixed $multi_value_form
    * @return integer возвращет количество добавленных характеристик
    */
    function addSomeValues($multi_value_form)
    {
        $values = preg_split('/[\n]/', $multi_value_form['values']);
        if ($values) {
            //Загружаем предыдущие значения характеристик
            $current_values = \RS\Orm\Request::make()
                ->select('id, value')
                ->from(new Orm\Property\ItemValue())
                ->where([
                    'prop_id' => $multi_value_form['prop_id'],
                ])->exec()->fetchSelected('value', 'id');
            foreach($values as $value) {
                if (isset($current_values[$value])) continue;
                $item_value = new Orm\Property\ItemValue();
                $item_value['prop_id'] = $multi_value_form['prop_id'];
                $item_value['value'] = trim($value);
                $item_value->insert();
            }
        }
        return true;        
    }
    
    /**
    * Конвертирует значения характеристик товара при смене типа характеристики
    * 
    * @return bool
    */
    function convertPropertyType($property, $old_type, $new_type)
    {
        $types = Orm\Property\Item::getAllowTypeData();
        
        $old_field = $types[$old_type]['save_field'];
        $new_field = $types[$new_type]['save_field'];
        
        if ($old_field == $new_field) return false;
        
        //Перенос число <-> строка
        if ($old_field != 'val_list_id' && $new_field != 'val_list_id') {
            $this->convertFromToIntStr($property, $old_field, $new_field);
        }
        
        //Перенос справочник -> число, строка
        if ($old_field == 'val_list_id') {
            $this->convertFromList($property, $new_field);
        }
        
        //Перенос число, строка -> справочник
        if ($new_field == 'val_list_id') {
            $this->convertToList($property, $old_field);
        }
        
        return true;
    }
    
    /**
    * Конвертирует значение при смене типа характеристики число <-> строка
    * 
    * @param integer $property_id - ID характеристики
    * @param string $old_field - поле со старым значением характеристики
    * @param string $new_field - поле для нового значения характеристики
    * @return void
    */
    private function convertFromToIntStr($property, $old_field, $new_field)
    {
        \RS\Orm\Request::make()
            ->update(new Orm\Property\Link())
            ->set("$new_field = $old_field, $old_field = NULL")
            ->where([
                'prop_id' => $property['id']
            ])->exec();
    }
    
    /**
    * Конвертирует значения при смене типа характеристики Список -> число, строка
    * 
    * @param integer $property_id - ID характеристики
    * @param string $new_field - поле для нового значения характеристики
    * @return void
    */
    private function convertFromList($property, $new_field)
    {
        $link = new Orm\Property\Link();
        $item_value = new Orm\Property\ItemValue();
        
        \RS\Orm\Request::make()
            ->update($link)->asAlias('L')
            ->join($item_value, 'IV.id = L.val_list_id', 'IV')
            ->set('L.val_str = IV.value')
            ->where([
                'L.prop_id' => $property['id'],
            ])
            ->exec();
        
        foreach(['product_id', 'group_id'] as $link_field) {
            //Оставляем только одну запись, удаляем список
            //Помечаем записи, которые не следует удалять (по одной для хар-ки в рамках каждого товара и категории)
            \RS\Orm\Request::make()
                ->update($link)->asAlias('L')
                ->update( 
                    '('.\RS\Orm\Request::make() 
                    ->select("prop_id, {$link_field}, val_list_id")
                    ->from($link)
                    ->where([
                        'prop_id' => $property['id']
                    ])
                    ->where('val_list_id > 0')
                    ->groupby("prop_id, {$link_field}")
                    ->toSql().')'
                )->asAlias('T')
                ->set([
                    'L.val_list_id' => null
                ])
                ->where("L.prop_id = T.prop_id 
                         AND L.{$link_field} = T.{$link_field} 
                         AND L.val_list_id = T.val_list_id")
                ->exec();
        }
        
        //Удаляем неотмеченные записи и значения
        \RS\Orm\Request::make()
            ->delete('L, V')
            ->from($link, 'L')
            ->join($item_value, 'L.val_list_id = V.id', 'V')
            ->where([
                'L.prop_id' => $property['id'],
            ])
            ->where('L.val_list_id > 0')
            ->exec();
    }
    
    /**
    * Конвертирует значения при смене типа характеристики на список
    * 
    * @param integer $property_id - ID характеристики
    * @param string $old_field - поле со старым значением характеристики
    * @return void
    */
    private function convertToList($property, $old_field)
    {
        static 
            $cache = [];
        
        $link = new Orm\Property\Link();
        
        $res = \RS\Orm\Request::make()
            ->select("prop_id, product_id, group_id, $old_field")
            ->from($link)
            ->where([
                'prop_id' => $property['id'],
            ])
            ->where(
                "$old_field IS NOT NULL"
            )
            ->exec();
        
        while($row = $res->fetchRow()) {
            if (!isset($cache[$property['id']][ $row[$old_field] ])) {
                if ($row[$old_field] !== '') {
                    $data = [
                        'prop_id' => $property['id'],
                        'value' => $row[$old_field]
                    ];
                    $item_value = Orm\Property\ItemValue::loadByWhere($data);
                    
                    if (!$item_value['id']) {
                        $item_value['site_id'] = $property['site_id'];                    
                        $item_value->getFromArray($data);
                        $item_value->insert();
                    }
                    
                    $value_id = $item_value['id'];
                } else {
                    //Не вставляем пустые значения
                    $value_id = 0;
                }
                
                $cache[$property['id']][ $row[$old_field] ] = $value_id;
            }
            
            $q = \RS\Orm\Request::make()
                ->update($link)
                ->set([
                    'val_list_id' => $cache[$property['id']][ $row[$old_field] ],
                ]);
            
            if ($old_field == 'val_int') {
                $q->set('val_str = val_int');
            }
            
            $q->where([
                'prop_id' => $row['prop_id'], 
                'product_id' => $row['product_id'], 
                'group_id' => $row['group_id'],
                $old_field => $row[$old_field]
            ])
            ->exec();            
                
        }
    }


    /**
     * Добавляет символьные идентификаторы значение характеристикам, у которых они не установлены
     *
     * @return integer
     */
    function addTranslitAliases()
    {
        $count = 0;
        $this->queryObj()->where("(alias IS NULL OR alias='')");
        $res = $this->getListAsResource();
        while($row = $res->fetchRow()) {
            $count++;
            $property_value = new Orm\Property\ItemValue();
            $property_value->getFromArray($row);
            $i = 0;
            $ok = false;
            while(!$ok && $i<15) {
                $property_value[$this->alias_field] = \RS\Helper\Transliteration::str2url(Tools::unEntityString($property_value['value'])).(($i>0) ? "-$i" : '');
                $ok = $property_value->update();
                $i++;
            }
        }
        return $count;
    }

}
