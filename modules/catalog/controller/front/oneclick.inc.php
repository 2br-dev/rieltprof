<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

/**
* Фронт контроллер Купить в один клик
*/
class OneClick extends \RS\Controller\Front
{
    public
        $id,
        /**
        * @var \Catalog\Model\OneClickApi $clickApi
        */
        $clickApi;
        
    function init()
    {
       $this->api = new \Catalog\Model\Api();
    }
    
    
    /**
    * Показ формы купить в один клик и её обработка
    */
    function actionIndex()
    { 
       $api = new \Catalog\Model\OneClickApi();
       $this->id = $this->url->request('product_id', TYPE_INTEGER);
       /**
       * @var \Catalog\Model\Orm\Product
       */
       $product  = $this->api->getById($this->id); //Получим сам объект товара
       if (!$product) $this->e404(t('Такого товара не существует'));
       if (!$product['public']) $this->e404();
       
       
       //Если используются комплектации добавим их
       $offer_id    = $this->url->request('offer_id', TYPE_INTEGER, null);
       $multioffers = $this->url->request('multioffers', TYPE_ARRAY, []);
       
       //Ставим хлебные крошки используемые при открытии в отдельной странице
       $this->app->breadcrumbs
            ->addBreadCrumb($product['title'], $this->router->getUrl('catalog-front-product', ['id' => $product['_alias']]))
            ->addBreadCrumb(t('Купить в один клик'));
       
       //Значения полей по умолчанию
       $click               = new \Catalog\Model\Orm\OneClickItem();
       
       $errors  = false;
       $display_errors = [];
       $request = $this->url;
       
       //Получим дополнительные поля для формы покупки в один
       /**
       * @var \RS\Config\UserFieldsManager
       */
       $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
       $click_fields_manager->setErrorPrefix('clickfield_');
       $click_fields_manager->setArrayWrapper('clickfields');

       $offer_fields = $api->prepareProductOfferFields($product, $offer_id, $multioffers);
       if(!$offer_id) { // совместимость со старыми шаблонами, не передающими offer_id
           $offer_fields['offer'] = $this->url->request('offer', TYPE_STRING, null);
       }
       
       if ($this->isMyPost()){ //Если пришёл запрос
           
           $product['offer_fields'] = $offer_fields;
           
           //Отсылаем письмо или уведомление на телефон если всё в порядке
           if ($api->checkFieldsFromPostToSend($click_fields_manager, true)) { //OK
               $api->send([$product]);
               $this->view->assign('success', t('Спасибо, в ближайшее время с Вами свяжется наш менеджер.'));
           } else { //Если есть ошибки
               $errors = $api->getErrors();
               $display_errors = $api->getDisplayErrors();
           }
       } else {
           $click['user_fio']   = $this->user['id'] ? $this->user->getFio() : "";
           $click['user_phone'] = $this->user['phone'];
           $click['currency']   = $this->url->cookie('currency', TYPE_STRING);
       }

       $this->view->assign([
            'click'                   => $click,
            'offer_fields'            => $offer_fields,
            'oneclick_userfields'     => $click_fields_manager,
            'product'                 => $product,
            'request'                 => $request,
            'error_fields'            => $errors,
            'display_errors'          => $display_errors
       ]);

       return $this->result->setTemplate('oneclick.tpl');
    }
}