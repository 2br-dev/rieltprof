<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;

use \RS\Html\Table\Type as TableType;
use \RS\Html\Toolbar\Button as ToolbarButton;
use \RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Контроллер. тип цен
*/
class CostCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(\Catalog\Model\Costapi::getInstance());
    }
    
    function helperIndex()
    {        
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Справочник типов цен'));
        $helper->setTopHelp($this->view->fetch('help/costctrl_index.tpl'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить цену')]));
        $helper->setBottomToolbar($this->buttons(['delete']));
        $helper->addCsvButton('catalog-typecost');
        $helper->setListFunction('getTableList');
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),            
                new TableType\Text('title', t('Название'), ['LinkAttr' => ['class' => 'crud-edit'], 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('_type_text', t('Тип')),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'TdAttr' => ['class' => 'cell-sgray'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать тип цены'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('установить по умолчанию'),
                            'attr' => [
                                '@data-url' => $this->router->getAdminPattern('setDefaultCost', [':id' => '@id']),
                                'class' => 'crud-get'
                            ]
                        ],
                    ]),

                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));
        return $helper;
    }
    
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать цену {title}') : t('Добавить цену'));
        
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
    /**
    * AJAX
    */
    function actionSetDefaultCost()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }        
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->api->setDefaultCost($id);
        return $this->result->setSuccess(true);
    }
    
}


