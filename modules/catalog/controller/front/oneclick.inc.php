<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use Catalog\Model\Api;
use Catalog\Model\OneClickItemApi;
use RS\Controller\Front;

/**
* Фронт контроллер Купить в один клик
*/
class OneClick extends Front
{
    public $id;
    /**
    * @var $click_api \Catalog\Model\OneClickItemApi
    */
    public $click_api;
        
    function init()
    {
       $this->api = new Api();
       $this->click_api = new OneClickItemApi();
    }

    function actionIndex()
    {
        $this->id = $this->url->request('product_id', TYPE_INTEGER);
        $currency = $this->url->cookie('currency', TYPE_STRING);

        //Если используются комплектации добавим их
        $offer_id    = $this->url->request('offer_id', TYPE_INTEGER, null);
        $multioffers = $this->url->request('multioffers', TYPE_ARRAY, []);

        $product  = $this->api->getById($this->id); //Получим сам объект товара
        if (!$product) $this->e404(t('Такого товара не существует'));
        if (!$product['public']) $this->e404();

        //Ставим хлебные крошки используемые при открытии в отдельной странице
        $this->app->breadcrumbs
            ->addBreadCrumb($product['title'], $this->router->getUrl('catalog-front-product', ['id' => $product['_alias']]))
            ->addBreadCrumb(t('Купить в один клик'));

        $click = $this->click_api->getElement();

        if ($this->user->id <= 0) {
            $click->__kaptcha->setEnable(true);
        }

        $offer_fields = $this->click_api->prepareProductOfferFields($product, $offer_id, $multioffers);

        $product['offer_fields'] = $offer_fields;
        $click->products = [$product];

        //@deprecated Переменные для совместимости со старыми шаблонами, до версии ReadyScript 6
        $old_values = [];
        if ($name = $this->url->post('name', TYPE_STRING)) {
            $old_values['user_fio'] = $name;
        }
        if ($phone = $this->url->post('phone', TYPE_STRING)) {
            $old_values['user_phone'] = $phone;
        }
        //---

        if ($this->isMyPost()) { //Если пришёл запрос
            if ($this->click_api->save(null, [
                'currency' => $currency
            ] + $old_values)) {
                $this->view->assign([
                    'success' => t('Спасибо, в ближайшее время с Вами свяжется наш менеджер.')
                ]);

            }
        } else {
            $click['user_fio']   = $this->user['id'] ? $this->user->getFio() : "";
            $click['user_phone'] = $this->user['phone'];
        }

        $this->view->assign([
            'click' => $click,
            'product' => $product,
        ]);

        //@deprecated Переменные для совместимости со старыми шаблонами, до версии ReadyScript 6
        $this->view->assign([
            'offer_fields'            => $offer_fields,
            'oneclick_userfields'     => $click->getFieldsManager(),
            'product'                 => $product,
            'request'                 => $this->url,
            'error_fields'            => $click->getErrors(),
            'display_errors'          => $click->getDisplayErrors()
        ]);
        //---

        return $this->result->setTemplate('oneclick.tpl');
    }
}