<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin\Widget;

use Main\Model\BestSellersApi;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Admin\Widget;

class BestSellers extends Widget
{
    protected
        $info_title = 'Лучшие предложения', //Определить у наследников название виджета.
        $info_description = 'Отображает лучшие предложения от компании ReadyScript'; //Определить у наследников описание виджета

    protected
        $action_var = 'bsdo';

    /**
     * @var BestSellersApi
     */
    public $api;

    function init()
    {
        $this->api = new BestSellersApi();
    }

    function actionIndex()
    {
        $items = $this->api->getCachedBestSellers();

        $this->view->assign([
            'items' => $items,
            'need_show_dialog' => (int)$this->api->needShowBestSellerDialog()
        ]);

        return $this->result->setTemplate('widget/bestsellers.tpl');
    }

    function actionGetDialog()
    {
        $items = $this->api->getCachedBestSellers();
        $this->api->disableShowBestSellerDialog();

        if (!$items) {
            $items = $this->api->updateCacheBestSellers();
            if (is_string($items)) {
                $items = false;
            }
        }

        if ($items) {

            $this->view->assign([
                'items' => $items
            ]);

            $helper = new CrudCollection($this);
            $helper
                ->viewAsForm()
                ->setForm($this->view->fetch('widget/bestsellers_dialog.tpl'));

            return $this->result->setTemplate($helper['template']);
        } else {
            return $this->result->addSection('close_dialog', true);
        }
    }

    function actionGetItems()
    {
        $items = $this->api->updateCacheBestSellers();

        $this->view->assign([
            'error' => is_string($items) ? $items : null,
            'items' => $items
        ]);

        return $this->result->setSuccess(true)
            ->setTemplate('widget/bestsellers_items.tpl');
    }
}