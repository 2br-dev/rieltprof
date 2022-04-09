<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table,
    \Shop\Model;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use Shop\Config\ModuleRights;
use RS\Orm\Type;

/**
* Контроллер Управление чеками
*/
class ReceiptsCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new Model\ReceiptApi());
        $this->setCrudActions('index', 'add', 'tableOptions');
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();        
        $helper->setBottomToolbar(null);
        $helper->setTopTitle(t('Электронные чеки'));
        $helper->setTopHelp(t('В этом разделе отображаются электронные чеки, пробитые с помощью online касс. Здесь отображаются чеки продаж, чеки возвратов, чеки корректировки.'));

        $filter = $this->url->request('f', TYPE_ARRAY);
        $transaction_id = isset($filter['transaction_id']) ? (int)$filter['transaction_id'] : null;

        $helper->setTopToolbar(new Toolbar\Element( [
             'Items' => [
                 new ToolbarButton\Dropdown([
                     [
                         'title' => t('Повторно выбить чек прихода'),
                         'attr' => [
                             'href' => $this->router->getAdminUrl('makeReceipt', ['operation' => 'sell', 'transaction_id' => $transaction_id, 'referer' => $this->url->selfUri()]),
                             'class' => 'crud-add crud-sm-dialog'
                         ]
                     ],
                     [
                         'title' => t('Повторно выбить чек возврата'),
                         'attr' => [
                             'href' => $this->router->getAdminUrl('makeReceipt', ['operation' => 'sell_refund', 'transaction_id' => $transaction_id, 'referer' => $this->url->selfUri()]),
                             'class' => 'crud-add crud-sm-dialog'
                         ]
                     ],
                     [
                         'title' => t('Добавить чек корректировки'),
                         'attr' => [
                             'href' => $this->router->getAdminUrl(false, ['transaction_id' => $transaction_id, 'referer' => $this->url->selfUri()], 'shop-correctionreceiptctrl'),
                             'class' => 'crud-add crud-sm-dialog'
                         ]
                     ],
                 ]),
             ]]
        ));
        
        //Список доступных провайдеров
        $cash_regiter_api = new \Shop\Model\CashRegisterApi();
        $providers_list   = $cash_regiter_api->getTypesAssoc();
        
        $edit_href = $this->router->getAdminPattern('edit', [':id' => '@id']);
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Text('uniq_id', t('Уникальный id')),
                new TableType\Text('transaction_id', t('Транзакция')),
                new TableType\Userfunc('provider', t('Провайдер'), function($provider_key) use ($providers_list) {
                    if (isset($providers_list[$provider_key])){
                        return $providers_list[$provider_key]; 
                    }
                    return $provider_key;
                }),
                new TableType\Datetime('dateof', t('Дата'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('type', t('Тип чека')),
                new TableType\Text('total', t('Сумма чека')),
                new TableType\Usertpl('status', t('Статус'), '%shop%/admin/receipt_status_cell.tpl'),
                new TableType\Text('error', t('Ошибка'), ['hidden' => true]),
                new TableType\Usertpl('__actions__', t('Действия'), '%shop%/admin/receipt_actions_cell.tpl'),
            ]
        ]));
        
        $receipt = new \Shop\Model\Orm\Receipt();
        //Статусы
        $status_list = ['' => t('Любой')] + $receipt->__status->getList();
        $type_list = ['' => t('Любой')] + $receipt->__type->getList();
        
        $helper->setFilter(new Filter\Control( [
             'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['Items' => [
                                            new Filter\Type\Text('id', '№'),                                    
                                            new Filter\Type\Text('uniq_id', t('ID транзакции от провайдера')),
                                            new Filter\Type\Text('transaction_id', t('ID транзакции ReadyScript')),
                                            new Filter\Type\Select('status', t('Статус'), $status_list),
                                            new Filter\Type\Select('type', t('Тип'), $type_list),
                                            new Filter\Type\DateRange('dateof', t('Дата')),
                                    ]
                                    ]),
                                ]
             ]),
            'Caption' => t('Поиск по чекам')
        ]));
        
        return $helper;
    }
    
    /**
    * Показывает ошибки чека в окне
    * 
    */
    function actionGetErrors()
    {
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_READ)) {
            return $this->result->setSuccess(false)
                                ->addEMessage($error);
        }
        
        $receipt_id = $this->url->get('id', TYPE_INTEGER);
        $receipt    = new \Shop\Model\Orm\Receipt($receipt_id);
        
        $this->view->assign([
            'error' => $receipt['error']
        ]);
        
        $this->wrapOutput(false);
        
        return $this->result->setTemplate('%shop%/admin/receipt_error.tpl');
    }
    
    
    /**
    * Показывает информацию о успешно выбитом чека в окне
    * 
    */
    function actionGetSuccessInfo()
    {
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_READ)) {
            return $this->result->setSuccess(false)
                                ->addEMessage($error);
        }
        
        $receipt_id       = $this->url->get('id', TYPE_INTEGER);
        $receipt          = new \Shop\Model\Orm\Receipt($receipt_id);

        $this->view->assign([
            'receipt' => $receipt,
        ]);
        
        $this->wrapOutput(false);
        
        return $this->result->setTemplate('%shop%/admin/receipt_info.tpl');
    }
    
    /**
    * Выбивает чек в ККТ
    * 
    */
    function actionGetReport()
    {
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)
                                ->addEMessage($error);
        }
        
        $receipt_id = $this->url->get('id', TYPE_INTEGER);
        $receipt    = new \Shop\Model\Orm\Receipt($receipt_id);
        
        try{
            $cashregister_api = new \Shop\Model\CashRegisterApi();
            /**
            * @var \Shop\Model\CashRegisterType\AbstractType
            */
            $provider = $cashregister_api->getTypeByShortName($receipt['provider']);
            if (($result = $provider->getReceiptStatus($receipt)) === true){
                return $this->result->setSuccess(true)
                                    ->addMessage(t('Статус успешно получен. Данные обновлены.'))
                                    ->setNoAjaxRedirect($this->url->selfUri());
            }else{
                if ($result === 0){ //Если чек ещё не зарегистрирован
                    $return_result = $this->result->setSuccess(true);
                    if ($provider->hasError()){ //Если во время запроса появились ошибки, например если сервер не доступен
                        $return_result->addEMessage($provider->getErrorsStr());
                    }else{
                        $return_result->addMessage(t('Чек всё ещё в статусе ожидает регистрации.'));
                    }
                    return $return_result;   
                }
                return $this->result->setSuccess(false)
                                    ->addEMessage($result);
            }
        }
        catch(\Exception $e){
            $this->result->setSuccess(false);
            $this->result->addEMessage($e->getMessage());
            return $this->result;
        }
    }

    /**
     * Выбивает чек продажи
     */
    function actionMakeReceipt()
    {
        if ($error = Rights::CheckRightError($this, ModuleRights::RIGHT_CORRECTION_RECEIPT)) {
            return $this->result->setSuccess(false)
                ->addEMessage($error);
        }
        $referer        = $this->request('referer', TYPE_STRING);
        $transaction_id = $this->request('transaction_id', TYPE_INTEGER, 0);
        $operation = $this->request('operation', TYPE_STRING, 'sell');

        $operation_flag = ($operation === 'sell' ? Model\CashRegisterType\AbstractType::OPERATION_SELL :
            Model\CashRegisterType\AbstractType::OPERATION_SELL_REFUND);

        $helper = new CrudCollection($this);
        $helper
            ->setTopTitle(t('Выбить чек %type', ['type' => $operation_flag == 'sell' ? t('прихода') : t('возврата')]))
            ->viewAsForm();

        $form_object = new FormObject(new PropertyIterator([
            'transaction_id' => new Type\Integer([
                'description' => t('ID транзакции'),
                'hint' => t('Чек будет выбит на всю сумму транзакции'),
                'checker' => ['chkEmpty', t('Укажите ID транзакции')]
            ])
        ]));

        $form_object['transaction_id'] = $transaction_id;

        if ($this->url->isPost()) {

            $cashregister_api = new \Shop\Model\CashRegisterApi();
            try{
                if (!$form_object->checkData()) {
                    return $this->result->setSuccess(false)->setErrors($form_object->getDisplayErrors());
                }
                $transaction = new Model\Orm\Transaction($form_object['transaction_id']);

                if (!$transaction['id']) {
                    return $this->result->setSuccess(false)->setErrors(t('Транзакция не найдена'));
                }

                $result = $cashregister_api->createReceipt($transaction, $operation_flag);

                if ($result === true) {
                    return $this->result->setSuccess(true)
                        ->addMessage(t('Чек создан'))
                        ->setNoAjaxRedirect($referer);
                } else {
                    return $this->result->setSuccess(false)
                        ->addEMessage($result);
                }
            }
            catch(\Exception $e){
                $this->result->setSuccess(false);
                $this->result->addEMessage($e->getMessage());
                return $this->result;
            }
        }

        $helper
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

        $helper->setFormObject($form_object);

        return $this->result->setTemplate($helper['template']);
    }
}