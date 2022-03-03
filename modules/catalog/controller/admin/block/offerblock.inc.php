<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin\Block;

use Catalog\Model\OfferApi;
use Catalog\Model\Orm\Product;
use RS\Controller\Admin\Block as AdminBlock;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;

/**
* Блок комплектаций в карточке товара
*/
class OfferBlock extends AdminBlock
{
    const PAGE_SIZE = 50;
        
    protected $action_var = 'odo';
    
    public $product;
    /** @var OfferApi */
    public $offer_api;
    
    function init()
    {
        $this->product = $this->getParam('product', new Product());
        $this->product['id'] = $this->url->request('product_id', TYPE_INTEGER, $this->product['id']);
        $this->offer_api = new OfferApi();
    }

    /**
     * Возвращает список комплектаций
     *
     * @return ResultStandard
     * @throws RSException
     * @throws OrmException
     * @throws \SmartyException
     */
    function actionIndex()
    {
        $filter    = $this->url->request('offer_filter', TYPE_ARRAY);
        $page      = $this->url->request('offer_page', TYPE_INTEGER, 1);
        $page_size = $this->url->request('offer_page_size', TYPE_INTEGER, self::PAGE_SIZE);
        $render_type = $this->url->request('offer_render_type', TYPE_STRING, 'all'); //Может быть all, all-offers, ext-offers
        
        $this->app->headers->addCookie('offer_page_size', $page_size, time()+3600*700, '/');
        
        //Загружаем основную комплектацию
        $main_offer = \Catalog\Model\Orm\Offer::loadByWhere([
            'product_id' => $this->product['id'],
            'sortn' => 0
        ]);
        $main_offer->getPropertyIterator()->arrayWrap('offers[main]');
        $main_offer->fillStockNum();
        
        if (!$this->getModuleConfig()->use_offer_unit) {
            $main_offer['__unit']->setVisible(false, 'main');
        }
        
        //Загружаем дополнительные комплектации
        $this->offer_api->setFilter([
            'sortn:>' => 0,
            'product_id' => $this->product['id']
        ]);
        
        $this->offer_api->applyFormFilter($filter, [
            'product_id' => $this->product['id'],
            'offer_page_size' => $page_size
        ]);
        
        //Пагинация
        $total = $this->offer_api->getListCount();
        $url_pattern = $this->router->getAdminPattern(false, [
            ':offer_page' => '%PAGE%', 
            'product_id' => $this->product['id'],
            'offer_page_size' => $page_size,
            'offer_filter' => $filter
        ], 'catalog-block-offerblock');
        
        $paginator = new \RS\Html\Paginator\Element($total, $url_pattern);
        $paginator->setPageKey('offer_page');
        $paginator->setPageSizeKey('offer_page_size');
        $paginator->setTotal($total);
        $paginator->setPageSize($page_size);
        $paginator->setPage($page);
        
        $this->offer_api->saveRequest('offers-list');
        $main_form_other_fields = $main_offer->getForm(null, 'main', false, null, '%catalog%/adminblocks/offerblock/offer_main_form_maker.tpl');
        
        $this->config = \RS\Config\Loader::byModule($this);
        if ($this->config['use_offer_unit']) {
            $this->view->assign([
                'units' => \Catalog\Model\UnitApi::selectList(),
            ]);
        }
        $this->view->assign([
            'elem' => $this->product,
            'filter_parts' => $this->offer_api->getFormFilterParts(),
            'product_id' => $this->product['id'],
            'filter' => $filter,
            'main_offer' => $main_offer,
            'other_fields_form' => $main_form_other_fields,
            'offer_page_size' => $page_size,
            'paginator' => $paginator,
            'offers_total' => $total,
            'render_type' => $render_type,
            'offers' => $this->offer_api->getList($paginator->page, $paginator->page_size),
            'default_currency' => \Catalog\Model\CurrencyApi::getBaseCurrency(),
            'warehouses' => \Catalog\Model\WareHouseApi::getWarehousesList(),
            'all_props' => \Catalog\Model\PropertyApi::getListTypeProperty(),
        ]);
        
        switch($render_type) {
            case 'all': $template = 'adminblocks/offerblock/offerblock.tpl'; break;
            case 'all-offers': $template = 'adminblocks/offerblock/offer_all.tpl'; break;
            default: $template = 'adminblocks/offerblock/offer_ext.tpl';
        }
        
        return $this->result->setTemplate($template);
    }
    
    /**
    * Меняет комплектацию местами с основной
    */
    function actionOfferChangeWithMain()
    {
        $offer_id = $this->url->request('offer_id', TYPE_INTEGER);
        $main_offer_data = $this->url->request('offers', TYPE_ARRAY); 
        $main_barcode = $this->url->request('barcode', TYPE_STRING);
        $main_sku = $this->url->request('sku', TYPE_STRING);
        $main_excost = $this->url->request('excost', TYPE_ARRAY);

        $custom_sections = $this->offer_api->setOfferAsMain($offer_id, $main_offer_data['main'], $main_barcode, $main_excost, $main_sku);
        
        if ($custom_sections) {
            return $this->result->setSuccess(true)->addSection($custom_sections);
        }
        return $this->result->setSuccess(false);
    }
    
    /**
    * Удаляет комплектации
    */
    function actionOfferDelete()
    {
        $offers_id = $this->getSelectedOffersId();
                
        if ($this->offer_api->multiDelete($offers_id)) {
            //Пересчитываем сортировочные индексы
            $this->offer_api->rebuildSortn($this->product['id']);
            $this->offer_api->updateProductNum($this->product['id']);
            return $this->result->setSuccess(true);
        } else {
            return $this->result->setSuccess(false)->addEMessage($this->offer_api->getErrorsStr());
        }
    }
    
    /**
    * Возвращает форму редактирования комплектации
    */
    function actionOfferEdit()
    {
        //offer_id = 0, означает создание комплектации
        $offer_id = $this->url->request('offer_id', TYPE_INTEGER);
        $offer = $this->offer_api->getElement();
        $offer->first_sortn = 1;
        if ($offer_id && !$offer->load($offer_id)) return $this->e404(t('Комплектация не найдена'));
        
        if ($this->url->isPost()) {
            //Сохранение
            
            $this->result->setSuccess( $this->offer_api->save($offer_id ?: null, ['id' => $offer_id]) );
            if (!$this->result->isSuccess()) {
                //Ошибка сохранения формы
                $this->result->setErrors($this->offer_api->getElement()->getDisplayErrors());
            }
            
            return $this->result;
        } else {
            //Чтение
            $offer->fillStockNum();
            if (!$offer_id) { //Создание комплектации
                $product_barcode = $this->url->request('product_barcode', TYPE_STRING);                        
                $offer['product_id'] = $this->product['id'];
                $offer['title'] = t('Новая комплектация');
                $offer['pricedata_arr'] = [
                    'oneprice' => [
                        'use' => true,                 
                        'znak' => '+',
                        'original_value' => 0,
                        'unit' => \Catalog\Model\CurrencyApi::getBaseCurrency()->id
                    ]
                ];
                $offer->setNextBarcode($product_barcode.'-');
            }
            

            if (!$this->getModuleConfig()->use_offer_unit) {
                $offer['__unit']->setVisible(false);
            }            
            $form = $offer->getForm(null, null, false, null, '%catalog%/adminblocks/offerblock/offer_form_maker.tpl');
            return $this->result->setHtml($form);
        }
    }
    
    /**
    * Редактирует массово комплектации
    */
    function actionOfferMultiEdit()
    {
        $ids = $this->getSelectedOffersId();
        $offer = $this->offer_api->getElement();
        $offer['product_id'] = $this->product['id'];
        $doedit = $this->url->request('doedit', TYPE_ARRAY, []);

        if ($this->url->isPost() && !empty($ids)) {
                
            
            $allow_keys = $offer->getProperties()->getMultieditKeys();    
            $post       = array_intersect_key($_POST, $allow_keys);
            
            //Устанавливаем checkBox'ы
            foreach($allow_keys as $key=>$val) {
                if (isset($offer['__'.$key])) {       
                    $property = $offer->getProp($key);
                    if (count($property->getCheckboxParam())) {
                        $post[$key] = isset($post[$key]) ? $property->getCheckboxParam('on') : $property->getCheckboxParam('off');
                    }
                    if ($property instanceof \RS\Orm\Type\ArrayList && !isset($post[$key])) $post[$key] = [];
                }
            }

            $post = array_intersect_key($post, array_flip($doedit));
            $this->result->setSuccess(empty($post));

            if (!empty($post)) {
                $offer->setCheckFields($doedit);
                if ($offer->checkData($post, [], [], $doedit)) {
                    
                    $this->offer_api->clearFilter();
                    $this->offer_api->setFilter($this->offer_api->getIdField(), $ids, 'in');                    
                    $this->offer_api->multiUpdate($post, $ids);
                    
                    return $this->result->setSuccess(true);
                } else {
                    return $this->result->setSuccess(false)->setErrors($offer->getDisplayErrors());
                }
            }
        
        }
        
        $hidden_fields = [];
        if ($this->url->request('selectAll', TYPE_STRING) == 'on') {
            $hidden_fields['selectAll'] = 'on';
        } else {
            foreach($ids as $key=>$id)
                $hidden_fields["offers[$key]"] = $id;
        }        
        
        $this->view->assign([
            'param' => [
                'hidden_fields' => $hidden_fields,
                'doedit' => $doedit, 
                'sel_count' => count($ids)
            ]
        ]);

        if (!$this->getModuleConfig()->use_offer_unit) {
            $offer['__unit']->setVisible(false);
        }            
        $form = $offer->getForm($this->view->getTemplateVars(), null, true, null, '%catalog%/adminblocks/offerblock/offer_me_form_maker.tpl');
        return $this->result->setHtml($form);        
    }
    
    /**
    * Перетаскивает комплектацию
    */
    function actionOfferMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING);
        
        //Определяем контекст сортировки
        $q = \RS\Orm\Request::make()
                ->where([
                    'product_id' => $this->product['id']
                ]);
        
        $this->result->setSuccess($this->offer_api->moveElement($from, $to, $flag, $q));
        
        if (!$this->result->isSuccess()) {
            $this->result->addEMessage($this->offer_api->getErrorsStr());
        }
        
        return $this->result;
    }
    
    /**
    * Создает простые комплектации из многомерных
    */
    function actionOfferMakeFromMultioffers()
    {
        $props = $this->url->request('prop', TYPE_ARRAY);
        $product_barcode = $this->url->request('product_barcode', TYPE_STRING);
        
        $multioffer_api = new \Catalog\Model\MultiOfferLevelApi();
        
        $props = $multioffer_api->convertPropValues($props);
        if (!$multioffer_api->createOffersFromParams($this->product['id'], $product_barcode, $props)) {
            return $this->result
                            ->setSuccess(false)
                            ->addEMessage($multioffer_api->getErrorsStr());
        }
        
        $this->result->setSuccess(true);
        return $this->actionIndex();
    }
    
    /**
    * Возвращает диалог связи фото с комплектациями
    */
    function actionOfferLinkPhoto()
    {
        $photos_id = $this->url->request('photos_id', TYPE_ARRAY);
        $offers = $this->url->request('offers', TYPE_ARRAY);
        
        $mainoffer = $offers['main'];
        $photo_id = count($photos_id)==1 ? $photos_id[0] : null;
        
        $dialog_data = $this->offer_api->getOffersLinkDialogData($mainoffer, $this->product['id'], $photo_id);
        
        $this->view->assign([
            'photos_id' => $photos_id,
            'dialog_data' => $dialog_data
        ]);
        
        return $this->result->setTemplate('adminblocks/offerblock/offer_photo_dialog.tpl');
    }    
    
    /**
    * Связывает фото с комплектациями
    */
    function actionOfferLinkPhotoSave()
    {
        if ($this->url->isPost()) {
            $photos_id = $this->url->request('photos_id', TYPE_ARRAY);
            $offers_id = $this->url->request('offers_id', TYPE_ARRAY);
            $offers = $this->url->request('offers', TYPE_ARRAY);
            $mainoffer = $offers['main'];
            
            $main_offer_photos_arr = $this->offer_api->linkPhotosToOffers($this->product['id'], $photos_id, $offers_id, $mainoffer);
            if ($main_offer_photos_arr === false) {
                return $this->result->setSuccess(false)->addEMessage($this->offer_api->getErrorsStr());
            }
            
            return $this->result
                            ->setSuccess(true)
                            ->addSection('main_offer_photos', $main_offer_photos_arr);
        }
    }
    
    /**
    * Возвращает список id комплектаций с учетом флага "Отметить на всех страницах"
    * 
    * @return array
    */
    private function getSelectedOffersId()
    {
        $offers_id = $this->url->request('offers', TYPE_ARRAY);
        $all_offers = $this->url->request('selectAll', TYPE_BOOLEAN);
        
        if ($all_offers) {
            $q = $this->offer_api->getSavedRequest('offers-list');
            if (!$q) 
                return $this->result->setSuccess(false);
                
            $offers_id = $this->offer_api->getIdsByRequest($q);
        }
        
        return $offers_id;
    }    
}