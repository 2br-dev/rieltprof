<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
    
/**
* Контроллер Управление налогами
*/
class TaxCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new \Shop\Model\TaxApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Налоги, указанные в данном разделе будут рассчитываться при оформлении заказа. Налог может быть привязан к определенному региону покупателя. Налог может иметь различную ставку, в зависимости от региона покупателя. После создания налога, необходимо присвоить его либо категории продукции, либо конкретным товарам.'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить налог')]));
        $helper->setTopTitle(t('Налоги'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('description', t('Описание')),
                new TableType\Yesno('enabled', t('Включен'), ['toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])]),
                new TableType\Text('sortn', t('Приоритет')),
                
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                        new TableType\Action\DropDown([
                                [
                                    'title' => t('Клонировать налог'),
                                    'attr' => [
                                        'class' => 'crud-add',
                                        '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                    ]
                                ],
                        ]),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));
        
        return $helper;
    }
    
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKey) {
            $this->api->getElement()->fillRates();
        }        
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
    function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);
        
        $product = $this->api->getOneItem($id);
        if ($product) {
            $product['enabled'] = !$product['enabled'];
            $product->update();
        }
        return $this->result->setSuccess(true);
    }
}
