<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Orm\Type;
use RS\Orm\PropertyIterator;
use RS\AccessControl\Rights;
use Shop\Config\ModuleRights;

/**
* Контроллер Управление чеками корректировки
*/
class CorrectionReceiptCtrl extends \RS\Controller\Admin\Front
{
     public 
        $api,
        $post_data,
        $helper;       
        
    function init()
    {
        $this->api = new \Shop\Model\ReceiptApi();
        $this->post_data = [
            'field' => $this->url->request('field', TYPE_STRING),
            'separator' => $this->url->request('separator', TYPE_STRING)
        ];
        
        $this->helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $this->helper
            ->setTopTitle(t('Создание чека корректировки'))
            ->viewAsForm();        
    }

    /**
     * Обработака отправки чека коррекции или показа его формы
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        if ($error = Rights::CheckRightError($this, ModuleRights::RIGHT_CORRECTION_RECEIPT)) {
            return $this->result->setSuccess(false)
                             ->addEMessage($error);
        }
        $referer        = $this->request('referer', TYPE_STRING);
        $transaction_id = $this->request('transaction_id', TYPE_INTEGER, 0);

        $cashregister_api = new \Shop\Model\CashRegisterApi();
        /**
         * @var \Shop\Model\CashRegisterType\AbstractType $provider
         */
        $provider = $cashregister_api->getCurrentCashRegisterClass();

        $form_object = $provider->getCorrectionReceiptFormObject();
        if (!$form_object) {
            return $this->result->setSuccess(false)
                ->addSection('close_dialog', true)
                ->addEMessage(t('Кассовый модуль не поддерживает чеки коррекции'));
        }

        $form_object['transaction_id'] = $transaction_id;

        if ($this->url->isPost()) {           
             try{

                 if (!$form_object->checkData()) {
                     return $this->result->setSuccess(false)->setErrors($form_object->getDisplayErrors());
                 }

                 if (($result = $provider->createCorrectionReceipt($transaction_id, $form_object)) === true) {

                     return $this->result->setSuccess(true)
                                         ->addMessage(t('Чек корректировки поставлен в очередь на получение.'))
                                         ->setNoAjaxRedirect($referer);
                 } else {
                     return $this->result->setSuccess(false)
                                         ->addEMessage($provider->getErrorsStr());
                 }
             }
             catch(\Exception $e){
                 $this->result->setSuccess(false);
                 $this->result->addEMessage($e->getMessage());
                 return $this->result;
             }
        }

        $this->helper
            ->setBottomToolbar(new Toolbar\Element( [
            'Items' => [
                'save' => new ToolbarButton\SaveForm(null, t('Создать')),
                'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
            ]
            ]));
        
        $this->view->assign([
            'transaction_id' => $transaction_id,
            'referer' => $referer
        ]);

        $this->helper->setFormObject($form_object);
        
        return $this->result->setTemplate($this->helper['template']);        
    }
}