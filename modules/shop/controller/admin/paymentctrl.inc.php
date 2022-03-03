<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
    
/**
* Контроллер Управление скидочными купонами
*/
class PaymentCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $api;
    
    function __construct()
    {
        parent::__construct(new \Shop\Model\PaymentApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В этом разделе можно задать способы оплаты, доступные для покупателей в вашем интернет-магазине. Это может быть оплата по счету, оплата через агрегатора платежей (ЮКасса, Robokassa, и т.д.), через квитанцию ПД-4, с лицевого счета покупателя и другие. Устовия отображения и доступности каждого варианта оплаты можно гибко настраивать.'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить способ оплаты')]));
        $helper->addCsvButton('shop-payment');
        $helper->setTopTitle(t('Способы оплаты'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC,'CurrentSort' => SORTABLE_ASC,'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH,'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Image('picture', t('Логотип'), 30, 30, 'xy', ['Hidden' => true, 'Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('description', t('Описание'), ['Hidden' => true]),
                new TableType\Text('class', t('Тип рассчета')),
                new TableType\Text('user_type', t('Доступен для')),
                new TableType\StrYesno('default_payment', t('По умолчанию')),
                new TableType\Yesno('public', t('Видим.'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])
                ]),
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                        new TableType\Action\DropDown([
                                    [
                                        'title' => t('Клонировать способ оплаты'),
                                        'attr' => [
                                            'class' => 'crud-add',
                                            '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                        ]
                                    ],
                        ]),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));
        
        return $helper;
    }
    
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKey === null) {
            $type_keys = array_keys($this->api->getTypes());
            if ($first = reset($type_keys)) {
                $this->api->getElement()->class = $first;
            }
        }
        if ($primaryKey == 0 ) $primaryKey = null;
        
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
    /**
    * AJAX
    */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }
    
    function actionGetTypeForm()
    {
        $type = $this->url->request('type', TYPE_STRING);
        if ($type_object = $this->api->getTypeByShortName($type)) {
            $this->view->assign('type_object', $type_object);
            $this->result->setTemplate( 'form/payment/type_form.tpl' );
        }
        return $this->result;
    }
    
    /**
    * Выполняет пользовательский метод оплаты, возвращая полученный ответ
    * 
    */
    function actionUserAct(){                                        
       $act          = $this->request('userAct',TYPE_STRING,false); 
       $payment_obj  = $this->request('paymentObj',TYPE_STRING,false); 
       $params       = $this->request('params',TYPE_ARRAY, []);
       $module       = $this->request('module',TYPE_STRING, 'Shop');
       
       if ($act && $payment_obj){
          $delivery = '\\'.$module.'\Model\PaymentType\\'.$payment_obj;
          $data = $delivery::$act($params);
          
          return $this->result->setSuccess(true)
                    ->addSection('data',$data); 
       }else{
          return $this->result->setSuccess(false)
                    ->addEMessage(t('Не установлен метод или объект доставки')); 
       }
    }
    
    /**
    * Включает/выключает флаг "публичный"
    */
    function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);
        
        $payment = $this->api->getOneItem($id);
        if ($payment) {
            $payment['public'] = !$payment['public'];
            $payment->update();
        }
        return $this->result->setSuccess(true);
    }  
    
    /**
    * Метод для клонирования
    * 
    */ 
    function actionClone()
    {
        $this->setHelper( $this->helperAdd() );
        $id = $this->url->get('id', TYPE_INTEGER);
        
        $elem = $this->api->getElement();
        
        if ($elem->load($id)) {
            $clone_id = 0;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->api->setElement($clone);
                $clone_id = (int)$clone['id']; 
            }
            unset($elem['id']);
            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }  
}
