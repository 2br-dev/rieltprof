<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

/**
* Api виртуальных многомерных комплектаций
* 
*/
class VirtualMultiOffersApi
{
    
    /**
    * Возвращает виртуальные многомерные комплектации для определённого товара
    * 
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * 
    * @return array
    */
    function getVirtualMultiOffersByProduct(\Catalog\Model\Orm\Product $product)
    {
        $arr = [];
        
        //Найдём нулевые комплектации подходящих товаров для нужной группы
        $product_sql = \RS\Orm\Request::make()
                            ->select('P.id')
                            ->from(new \Catalog\Model\Orm\Product(), "P")
                            ->where([
                                'P.public' => 1,
                                'P.group_id' => $product['group_id'],
                                'P.site_id' => \RS\Site\Manager::getSiteId()
                            ])->toSql();
        
        //Подгрузим многомерные комплектации
        $offers = \RS\Orm\Request::make()
            ->select('O.*')
            ->from(new \Catalog\Model\Orm\Offer(), 'O')
            ->where([
                'O.sortn' => 0
            ])
            ->where('O.product_id IN('.$product_sql.')')
            ->objects('\Catalog\Model\Orm\Offer', 'product_id');
            
        if (!empty($offers)){
            
            //Получим предварительно все товары из группы для дальнейшей генерации адреса
            $product_ids         = array_keys($offers);
            //Подготовим массив для формирования url
            $alias_by_product_id = \RS\Orm\Request::make()
                            ->from(new \Catalog\Model\Orm\Product())
                            ->whereIn('id', $product_ids)
                            ->exec()
                            ->fetchSelected('id', ['alias']);
                       
            
            //Переберём комплектации и занёсем данные в результирующий массив
            foreach ($offers as $offer){
                $props = (array)$offer['propsdata_arr'];
                $item  = [];
                if (!empty($props)){
                    foreach ($props as $key=>$val){
                        $item[$key] = $val;
                    }    
                }
                $arr[$offer['product_id']]['values'] = $item;
                //Получим url для переключения
                $arr[$offer['product_id']]['url']    = \RS\Router\Manager::obj()->getUrl('catalog-front-product', [
                    'id' => $alias_by_product_id[$offer['product_id']]['alias'] ? $alias_by_product_id[$offer['product_id']]['alias'] : $offer['product_id'],
                ]);
            }
        }
        return $arr;
    }
    
    
    /**
    * Возвращает виртуальные многомерные комплектации, где в ключи идут ключи из параметров со множеством возможных значений
    * 
    * @param array $virtual_multioffers - массив виртуальных многомерных комплектаций
    * 
    * @return array
    */
    function prepareVirtualMultiOffersByKeys($virtual_multioffers)
    {
        $arr = [];
        foreach ($virtual_multioffers as $product_id=>$items){
            if (!empty($items['values'])){
                foreach ($items['values'] as $key=>$value){
                    if (isset($arr[$key]) && !in_array($value, $arr[$key])){ //Если такого значения ещё небыло
                        $arr[$key][] = $value;       
                    }elseif (!isset($arr[$key])){
                        $arr[$key][] = $value;  
                    }
                }
            }
        }    
        
        //Отсортируем значения в ключах
        if (!empty($arr)){
            foreach ($arr as $key=>$values){
                sort($arr[$key]);
            }
        }
        return $arr;
    }
    
    
    /**
    * Возвращает виртуальные многомерные комплектации, где в ключи идут ключи из параметров со множеством возможных значений
    * 
    * @param array $virtual_multioffers - массив виртуальных многомерных комплектаций
    * 
    * @return array
    */
    function prepareVirtualMultiForMultioffer($virtual_multioffers)
    {
        $levels = [];
        
        foreach ($virtual_multioffers as $product_id => $items){
            if (!empty($items['values'])){
                $values = [];
                
                foreach ($items['values'] as $key => $value){
                    
                    if (!isset($levels[$key])) {
                        $levels[$key] = new Orm\MultiOfferLevel();
                        $levels[$key]['prop_id']    = str_replace(' ', '', $key);
                        $levels[$key]['title']      = $key;
                        $levels[$key]['is_virtual'] = true;
                        $levels[$key]['values'] = [];
                    }
                    
                    $array = $levels[$key]['values'];
                    $array[$value] = new Orm\Property\ItemValue();
                    $array[$value]['val_str'] = $value;
                    $array[$value]['value'] = $value;
                    
                    $levels[$key]['values'] = $array;
                }
            }
        }
        
        //Отсортируем значения в ключах
        if (!empty($levels)){
            foreach ($levels as $key=>$values){
                $ordered_values = $levels[$key]['values'];
                uksort($ordered_values, "strnatcasecmp");
                $levels[$key]['values'] = $ordered_values;
            }
        }
        
        $levels = $this->mergeExistedItemValues($levels);
        
        return $levels;
    }
    
    /**
    * Дополняет значения свойствами "цвета" и "картики" от существующих списковых значений
    * Значения сопоставляются по совпадению названия характеристики и value
    * 
    * @param array $levels
    * @return array
    */
    protected function mergeExistedItemValues($levels)
    {
        // Выбираем из базы списковые значения, от характеристик, названия которых фигурируют в уровнях виртуальных многомерках
        if ($levels) {
            $exist_prop_values = \RS\Orm\Request::make()
                ->select('V.*, I.title')
                ->from(new \Catalog\Model\Orm\Property\ItemValue(), 'V')
                ->join(new \Catalog\Model\Orm\Property\Item(), 'V.prop_id = I.id', 'I')
                ->where([
                    'I.site_id' =>\RS\Site\Manager::getSiteId(),
                ])
                ->whereIn('I.type', \Catalog\Model\Orm\Property\Item::getListTypes())
                ->whereIn('I.title', array_keys($levels))
                ->exec()->fetchSelected('title', null, true);
            
            // Всем списковым значениям, которые присутствуют в виртуальных многомерках, дописываем "цвет" и "картинку"
            foreach ($exist_prop_values as $prop_title=>$values) {
                $max_sortn = 0;
                foreach ($values as $value) {
                    if (isset($levels[$prop_title]['values'][$value['value']])) {
                        $multiofferlevel_values = $levels[$prop_title]['values'];
                        $multiofferlevel_values[$value['value']]['color'] = $value['color'];
                        $multiofferlevel_values[$value['value']]['image'] = $value['image'];
                        $multiofferlevel_values[$value['value']]['sortn'] = $value['sortn'];
                        $levels[$prop_title]['values'] = $multiofferlevel_values;
                        if ($max_sortn < $value['sortn']) {
                            $max_sortn = $value['sortn'];
                        }
                    }
                }
                // отсортируем значения
                $sorted = [];
                foreach ($levels[$prop_title]['values'] as $val => $item) {
                    if (isset($item['sortn'])) {
                        $sorted[$item['sortn']] = $val;
                    } else {
                        $sorted[++$max_sortn] = $val;
                    }
                }
                ksort($sorted);
                $new_values = [];
                foreach ($sorted as $val) {
                    $new_values[$val] = $levels[$prop_title]['values'][$val];
                }
                $levels[$prop_title]['values'] = $new_values;
            }
        }
        
        return $levels;
    }
}
