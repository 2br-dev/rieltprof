<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use RS\Event\Exception as EventException;

/**
* API комплектаций товара
*/
class OfferApi extends \RS\Module\AbstractModel\EntityList
{
    const MASS_SAVE_OFFER_OPERATION = 'mass-save-offer-operation';

    protected
        $filter_parts = [];
        
    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\Offer, [
            'defaultOrder' => 'sortn',
            'sortField' => 'sortn',
            'titleField' => 'title'
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
        $allow_keys = [
            'title' => t('Название'),
            'barcode' => t('Артикул'),
            'num' => t('Остаток')
        ];
                        
        $allow_cmp = ['=', '<', '>'];
        $like_cmp_keys = ['title', 'barcode'];
        $router = \RS\Router\Manager::obj();
        
        foreach($get_filters as $key => $value) {
            if (!in_array($key, array_keys($allow_keys)) || $value === '') continue;
            
            if (in_array($key, $like_cmp_keys)) {
                $cmp = '%like%';
            } else {
                $cmp = isset($get_filters['cmp_'.$key]) ? html_entity_decode($get_filters['cmp_'.$key]) : false;
                if (!in_array($cmp, $allow_cmp)) $cmp = false;
            }
            $this->setFilter($key, $value, $cmp ?: '=');
            
            $without_filter = $get_filters;
            unset($without_filter[$key]);
            unset($without_filter['cmp_'.$key]);
            
            if (in_array($cmp, ['<', '>'])) {
                $value = $cmp.$value;
            }
            
            $this->filter_parts[] = [
                'text' => $allow_keys[$key].': '.$value,
                'clean_url' => $router->getAdminUrl(false, ['offer_filter' => $without_filter] + $url_params, 'catalog-block-offerblock')
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
     * Сохраняет комплектации для товара
     *
     * @param integer $product_id ID товара
     * @param array $offers массив с комплектациями. Если передан один элемент с ключем main, значит
     * передана только основная комплектация. В этом случае Дополнительные комплектации (где sortn>0) не будут затронуты.
     * В противном случае (когда все ключи - числовые), считается, что переданные комплектации должны
     * полностью заместить существующие комплектации.
     * @param bool $use_unconverted_propsdata - Если true, то будет учитываться _propsdata для сохранения характеристик комплектаций
     * @return void
     * @throws EventException
     * @throws \RS\Orm\Exception
     */
    function saveOffers($product_id, $offers, $use_unconverted_propsdata = false)
    {
        //Если true, значит передана только основная комплектация.
        //Это означает, что не следует удалять оставшиеся комплектации
        $is_only_main_offer = (count($offers) == 1 && isset($offers['main']));

        $exclude_items = [];
        $n = 0;
        foreach($offers as $key => $item) {

            if ($item instanceof Orm\Offer) {
                $item = $item->getValues();
            }
            if (!is_array($item)) $item = [];

            $item += [
                'pricedata_arr' => []
            ];

            $item += [
                'photos_arr' => []
            ];

            if ($use_unconverted_propsdata) {
                $item += ['_propsdata' => []];
            }

            $offer = new Orm\Offer();

            //Устанавливаем флаг, который говорит о том, что комплектации сохраняются в рамках
            //более масштабной операции - сохранения всего товара. Проверка данного флага в других местах системы,
            //в некоторых, случаях может предотвратить повторное выполнение операций на сохранении комплектаций и товаров.
            $offer[self::MASS_SAVE_OFFER_OPERATION] = true;

            if ($is_only_main_offer) {
                $offer = Orm\Offer::loadByWhere([
                    'product_id' => $product_id,
                    'sortn' => 0
                ]);

                if ($offer['id']) {
                    unset($item['id']);
                }
            }

            $offer->getFromArray($item);

            if($offer['photos_arr'] == null){
                $offer['photos_arr'] = [];
            }

            $offer['sortn'] = $n;
            $offer['product_id'] = $product_id;

            if ($offer['id']>0){
               $offer->update();
            }else{
               $offer->insert();
            }

            $exclude_items[] = $offer['id'];
            $n++;
        }

        if (!$is_only_main_offer) {
            //Удалим комплектации отсутствующие
            $this->deleteExcludedOffers($product_id, $exclude_items);
        }

        $this->updateProductNum($product_id);
    }

    /**
     * Обновляет общее количество товара, на основе количества комплектаций
     *
     * @param integer $product_id - id товара
     * @return void
     */
    function updateProductNum($product_id)
    {
        $sub_query = \RS\Orm\Request::make()
            ->select('SUM(num)')
            ->from(new Orm\Offer(), 'O')
            ->where('O.product_id = P.id')
            ->toSql();

        \RS\Orm\Request::make()
            ->update(new \Catalog\Model\Orm\Product)
            ->asAlias('P')
            ->set("P.num = ($sub_query)")
            ->set("P.import_hash = NULL")
            ->where([
                'id' => $product_id
            ])
            ->exec();
    }
    
    /**
    * Удаляет комплектации, которые которые удалены у товара 
    * 
    * @param integer $product_id - id товара для которого исключаеются комплектации
    * @param array $offers_ids   - id комлектаций, которые дожны быть не тронуты(остатся) 
    */
    function deleteExcludedOffers($product_id, $offers_ids)
    {
        if ($this->noWriteRights()) return false;
        
        if ($offers_ids) {
            //Удаляем комплектации.
            \RS\Orm\Request::make()
                    ->from(new \Catalog\Model\Orm\Offer())
                    ->where('id NOT IN ('.implode(",",$offers_ids).')')
                    ->where([
                        'product_id' => $product_id
                    ])
                    ->delete()->exec();
            //Удаляем количество товаров комплектаций
            \RS\Orm\Request::make()
                    ->from(new \Catalog\Model\Orm\Xstock())
                    ->where('offer_id NOT IN ('.implode(",",$offers_ids).')')
                    ->where([
                        'product_id' => $product_id
                    ])
                    ->delete()->exec();
        }
        
        return true;
    }
    
    /**
    * Удаляет комплектации по id товара или массиву с id товаров
    * 
    * @param integer|array $product_id - id товара или массив с id товаров
    */
    function deleteOffersByProductId($product_id) 
    {
       if ($this->noWriteRights()) return false;
       
       if (is_array($product_id)){ 
          //Удаляем комлектации
          \RS\Orm\Request::make()->delete()->from(new Orm\Offer())
              ->whereIn('product_id',
                $product_id
              )
              ->where('sortn > 0')
              ->exec();       
       }else{
          //Удаляем комлектации
          \RS\Orm\Request::make()->delete()->from(new Orm\Offer())->where([
            'product_id' => $product_id
          ])->exec();
       } 
       
       //Удаляем остатки по складам 
       $sub_query = \RS\Orm\Request::make()
            ->select('id')
            ->from(new \Catalog\Model\Orm\Offer())
            ->toSql();
       $q = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Xstock())
            ->where('offer_id NOT IN ('.$sub_query.')')
            ->delete()
            ->exec(); 
            
       return true;
       
    }
    
    /**
    * Перестраивает сортировочные индексы у дополнительных комплектаций,
    * т.к. в результате удаления комплектаций, могут появиться пропуски в sortn
    */
    function rebuildSortn($product_id)
    {
        if ($this->noWriteRights()) return false;
        
        $offers_sortn = \RS\Orm\Request::make()
            ->select('id, sortn')
            ->from($this->obj_instance)
            ->where('sortn > 0')
            ->where([
                'product_id' => $product_id
            ])
            ->orderby('sortn')
            ->exec()->fetchSelected('id', 'sortn');
        
        $i = 1;
        foreach($offers_sortn as $id => $sortn) {
            if ($sortn != $i) {
                \rs\Orm\Request::make()
                    ->update($this->obj_instance)
                    ->set([
                        'sortn' => $i
                    ])
                    ->where(['id' => $id])
                    ->exec();
            }
            $i++;
        }
        return true;
    }


    /**
    * Обновляет свойства у группы объектов
    *
    * @param array $data - ассоциативный массив со значениями обновляемых полей
    * @param array $ids - список id объектов, которые нужно обновить
    * @return integer - возвращает количество обновленных элементов
    */
    function multiUpdate(array $data, $ids = [])
    {
        if ($this->noWriteRights()) return false;
        
        if (isset($data['photos_arr'])) {
            $photos = serialize($data['photos_arr']);
            \RS\Orm\Request::make()
                ->update(new Orm\Offer())
                ->set([
                    'photos' => $photos
                ])
                ->whereIn('id', $ids)
                ->exec();
                
            unset($data['photos_arr']);
        }        
        
        if (isset($data['pricedata_arr'])) {
            
            if (empty($data['pricedata_arr']['oneprice']['use'])) { 
                unset($data['pricedata_arr']['oneprice']);
                
                //Отсечем цены, которые не заданы                
                foreach($data['pricedata_arr']['price'] as $key => $one_price) {
                    if ($one_price['original_value'] === '') {
                        unset($data['pricedata_arr']['price'][$key]);
                    }
                }
                
                $data['pricedata_arr'] = $this->obj_instance->convertValues($data['pricedata_arr']);
                
                $q = \RS\Orm\Request::make()
                    ->select('id, pricedata')
                    ->from($this->obj_instance)
                    ->whereIn('id', $ids)
                    ->limit(50);
                
                $offset = 0;
                while($offer_rows = $q->offset($offset)->exec()->fetchAll()) {
                    foreach($offer_rows as $row) {
                        $old_pricedata = @unserialize($row['pricedata']) ?: [];
                        $old_pricedata += ['price' => []];
                        
                        //Дополняем старыми ценами
                        $new_pricedata = $data['pricedata_arr'];
                        $new_pricedata['price'] += $old_pricedata['price'];
                
                        $pricedata = serialize($new_pricedata);
                        \RS\Orm\Request::make()
                            ->update($this->obj_instance)
                            ->set([
                                'pricedata' => $pricedata
                            ])
                            ->where([
                                'id' => $row['id']
                            ])->exec();
                    }
                    $offset +=50;
                }                
            } else {
                //Если одна цена задана, то обновляем одним запросом у всех
                $data['pricedata_arr'] = $this->obj_instance->convertValues($data['pricedata_arr']);
                $pricedata = serialize($data['pricedata_arr']);
                \RS\Orm\Request::make()
                    ->update($this->obj_instance)
                    ->set([
                        'pricedata' => $pricedata
                    ])
                    ->whereIn('id', $ids)->exec();                      
            }
                
            unset($data['pricedata_arr']);
        }        
        
        if (isset($data['_propsdata'])) {
            $props_data_arr = [];
            if (!empty($data['_propsdata'])) {
                foreach($data['_propsdata']['key'] as $n => $val) {
                    if ($val !== '') {
                        $props_data_arr[$val] = $data['_propsdata']['val'][$n];
                    }
                }
            }
            $data['propsdata_arr'] = $props_data_arr;
            unset($data['_propsdata']);
        }
        
        if (isset($data['propsdata_arr'])) {
            $propsdata = serialize($data['propsdata_arr']);
            \RS\Orm\Request::make()
                ->update(new Orm\Offer())
                ->set([
                    'propsdata' => $propsdata
                ])
                ->whereIn('id', $ids)
                ->exec();
                
            unset($data['propsdata_arr']);
        }
        
        if (isset($data['stock_num'])) {
            //Очистим остатки по складам
            \RS\Orm\Request::make()
                ->delete()           
                ->from(new \Catalog\Model\Orm\Xstock())
                ->whereIn('offer_id', $ids)
                ->exec();
                
            $offerId_productId = \RS\Orm\Request::make()
                ->select('id, product_id')
                ->from($this->obj_instance)
                ->whereIn('id', $ids)
                ->exec()->fetchSelected('id', 'product_id');
           
            $data['num'] = 0;
           
            foreach ($data['stock_num'] as $warehouse_id => $stock_num) {
                foreach($ids as $offer_id) {
                    //Добавим остатки по складам  
                    $offer_stock = new \Catalog\Model\Orm\Xstock(); 
                    $offer_stock['product_id']   = $offerId_productId[$offer_id];
                    $offer_stock['offer_id']     = $offer_id;
                    $offer_stock['warehouse_id'] = $warehouse_id;
                    $offer_stock['stock']        = $stock_num;
                    $offer_stock->insert();                    
                }
                $data['num'] += $stock_num;
           }             
           unset($data['stock_num']);
        }
        
        return parent::multiUpdate($data, $ids);
    }    
    
    /**
    * Удаляет несвязанные с товарами комплектации
    * Операция необходима для очистки базы от неиспользуемых записей
    * 
    * @return int возвращает 
    */
    function cleanUnusedOffers()
    {
        return \RS\Orm\Request::make()
            ->delete()
            ->from(new \Catalog\Model\Orm\Offer())
            ->where('product_id < 0')
            ->exec()->affectedRows();
    }
    
    /**
    * Возвращает данные, необходимые для отображения диалога связи комплектаций и фото
    * 
    * @param array $mainoffer - основная комплектация
    * [
    * 
    * ]
    * @param integer|null $product_id - id товара
    * @param integer $photo_id - id фотографии
    * 
    * @return array
    * [
    *   'params' => [ключ => [значение1, значение2, значение3]],
    *   'offers' => [id комплектации => название],
    *   'selected' => [id комплектации, id комплектации]
    * ]
    */
    function getOffersLinkDialogData($mainoffer, $product_id, $photo_id = null)
    {
        $result = [
            'params' => [],
            'offers' => [],
            'selected' => []
        ];

        $offer = new Orm\Offer();
        $mainoffer['propsdata_arr'] = isset($mainoffer['_propsdata']) ? $offer->convertPropsData($mainoffer['_propsdata']) : [];

        $offers = array_merge([$mainoffer],
                    \RS\Orm\Request::make()
                        ->from($this->obj_instance)
                        ->where('sortn>0')
                        ->where(['product_id' => $product_id])
                        ->orderby('sortn')
                        ->exec()->fetchAll());


        foreach($offers as $offer_arr) {
            $title = [];
            $offer->clear();
            $offer->getFromArray($offer_arr,null,null,true);

            foreach($offer['propsdata_arr'] as $key => $value) {
                $result['params'][$key][$value] = $value;
                $title[] = $value;
            }

            $title = trim(implode(', ', $title));
            $result['offers'][$offer['id']] = [
                'title' => ($title == '') ? $offer['title'] : $title,
                'params' => $offer['propsdata_arr']
            ];

            if ($photo_id) {
                if (in_array($photo_id, $offer['photos_arr'] ?: [])) {
                    $result['selected'][] = $offer['id'];
                }
            }
        }
        return $result;
    }
    
    /**
    * Привязывает/отвязывает фото к комплектациям
    * 
    * @param array $photos_id 
    * @param array $offers_id
    * @return array возвращает массив выбранных фото для Основной комплектации
    */
    function linkPhotosToOffers($product_id, $photos_id, $offers_id, $mainoffer)
    {
        if ($this->noWriteRights()) return false;
        
        //Все комплектации товара
        $all_offers = array_merge([$mainoffer],
            \RS\Orm\Request::make()
            ->select('id, photos')
            ->from($this->obj_instance)
            ->where([
                'product_id' => $product_id
            ])
            ->where('sortn>0')
            ->exec()->fetchAll());
        
        $result = [];
        
        foreach($all_offers as $n => $offer) {
            if (isset($offer['photos'])) {
                $photos_arr = @unserialize($offer['photos']) ?: [];
            } else {
                $photos_arr = isset($offer['photos_arr']) ? $offer['photos_arr'] : [];
            }
            
            if (in_array($offer['id'], $offers_id)) {
                //Если фотографии назначены комплектации, то включаем их в список
                $photos_arr = array_unique( array_merge($photos_arr, $photos_id) );
            } else {
                //Если фотографии не назначены комплектации, то исключаем их
                $photos_arr = array_diff($photos_arr, $photos_id);
            }
            
            //Обновляем комплектации
            \RS\Orm\Request::make()
                ->update($this->obj_instance)
                ->set([
                    'photos' => serialize($photos_arr)
                ])
                ->where([
                    'id' => $offer['id']
                ])->exec();
            
            if ($n == 0) $result = array_values($photos_arr);
        }
        
        return $result;
    }
    
    /**
    * Делает комплектацию offer_id основной, а основную комплектацию перемещает на место offer_id
    * 
    * @param integer $offer_id - ID комплектации, которую нужно сделать основной
    * @param array $main_offer_data - массив с данными основной комплектации (которая может быть еще не присутствует в базе)
    * @param array $excost - цены основной комплектации, которые могут еще не присутствовать в базе
    * @return array | false
    */
    function setOfferAsMain($offer_id, $main_offer_data, $main_barcode, $excost, $main_sku = null)
    {
        $offer = new Orm\Offer($offer_id);
        $product_exist = $offer['product_id'] > 0;
        if($product_exist) {
            $product = new Orm\Product($offer['product_id']);
            $main_offer = new Orm\Offer($main_offer_data['id']);
        } else {
            $main_offer = new Orm\Offer();
            $main_offer['product_id'] = $offer['product_id'];
            $main_offer['pricedata_arr'] = [];
            $main_offer->insert();
        }

        if($offer['sortn'] != 0){
            $offer_price_data = unserialize($offer['pricedata']);
            $product_price_data = [];
            foreach($excost as $cost_id => $item){
                $product_price_data['price'][$cost_id]['original_value'] = $item['cost_original_val'];
                $product_price_data['price'][$cost_id]['unit'] = $item['cost_original_currency']; 
                $product_price_data['price'][$cost_id]['znak'] = '=';
            }
            // устанавливаем товару цены от перемещаемой комплектации
            if($product_exist){
                $product->fillCost();
                $product_excost = $product['excost'];
            } else {
                $product_excost = [];
            }
            
            if (!empty($offer_price_data['oneprice']['use'])) {
                //Разворачиваем цены
                $cost_api = new CostApi();
                $cost_api->setFilter([
                    'type' => Orm\Typecost::TYPE_MANUAL
                ]);
                $costs = $cost_api->getAssocList('id', 'id');
                foreach($costs as $cost_id) {
                    $offer_price_data['price'][$cost_id] = $offer_price_data['oneprice'];
                }
                unset($offer_price_data['oneprice']);
            }
            if (!empty($offer_price_data['price'])) {
                foreach($offer_price_data['price'] as $cost_id => $item) {
                    if($item['znak'] == '=') {
                         $product_excost[$cost_id]['cost_original_val'] = $item['original_value'];
                         $product_excost[$cost_id]['cost_original_currency'] = $item['unit'];
                    }                   
                }    
            }
            
            // установим новые значения для основной комплектации
            $main_offer['title'] = $main_offer_data['title'];
            if(isset($main_offer_data['stock_num'])){
                $main_offer['stock_num'] = $main_offer_data['stock_num'];
            }
            if (isset($main_offer_data['photos_arr'])){
                $main_offer['photos_arr'] = $main_offer_data['photos_arr'];
            }
            $main_offer['sortn'] = $offer['sortn'];
            $main_offer['pricedata_arr'] = $product_price_data;
            $main_offer['barcode'] = $main_barcode;
            $main_offer['sku'] = $main_sku;
            
            // делаем перемещаемую комплектацию основной
            $offer['sortn'] = 0;
            
            // обновляем объекты
            if($product_exist) {
                $product['excost'] = $product_excost;
                $product['barcode'] = $offer['barcode'];
                $product['sku'] = $offer['sku'];
                $product->update();
            }    
            $main_offer->update();
            $offer->update();
            
            return [
                'excost' => $product_excost, 
                'barcode' => $offer['barcode'],
                'sku' => $offer['sku']
            ];
        }
        return false;
    }
    
    /**
    * Обновляет значения у связанной с комлектацией характеристики товара.
    * В случае, если остаток комплектации положительный, связанная характеристика 
    * будет отображаться в фильтре в списке товаров
    * 
    * @return void
    */
    function updateLinkedProperties($product_id)
    {
        //Загружаем связь уровней многомерной комплектации с характеристиками
        $mo_property = \RS\Orm\Request::make()
            ->select('prop_id, title')
            ->from(new Orm\MultiOfferLevel())
            ->where([
                'product_id' => $product_id
            ])->exec()->fetchSelected('title', 'prop_id');
        
        if (!$mo_property) return;
            
        //Загружаем характеристики всех комплектаций
        $offer_props = \RS\Orm\Request::make()
            ->select('propsdata, num')
            ->from(new Orm\Offer())
            ->where([
                'product_id' => $product_id
            ])->exec()->fetchAll();
            
        //Подсчитываем количество каждого значения у всех комплектаций
        $property_values = [];
        foreach($offer_props as $data) {
            $propsdata_arr = @unserialize($data['propsdata']) ?: [];
            foreach($propsdata_arr as $key => $value) {
                if (isset($mo_property[$key])) {
                    @$property_values[$mo_property[$key]][$value] += $data['num'];
                }
            }
        }
        
        $link = new Orm\Property\Link();
        //Обновляем доступность характеристики
        foreach($property_values as $prop_id => $values) {
            foreach($values as $value => $num) {
                \RS\Orm\Request::make()
                    ->update($link)
                    ->set([
                        'available' => (int)$num > 0
                    ])
                    ->where([
                        'prop_id' => $prop_id,
                        'product_id' => $product_id,
                        'val_list_id' => Orm\Property\ItemValue::getIdByValue($prop_id, $value)
                    ])
                    ->exec();
            }
        }
    }    
    
    /**
    * Обновляет сведения по доступности характеристик, связанных с характеристиками товара
    * 
    * @return void
    */
    function updateLinkedPropertiesForAllProducts()
    {
        $res = \RS\Orm\Request::make()
            ->select('id')
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'site_id' => \RS\Site\Manager::getSiteId()
            ])
            ->exec();
        
        while($row = $res->fetchRow()) {
            $this->updateLinkedProperties($row['id']);
        }
        
    }
    
}