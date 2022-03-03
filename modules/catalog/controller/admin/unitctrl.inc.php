<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;

class UnitCtrl extends \RS\Controller\Admin\Crud
{
    protected 
        $api;

    function __construct($param = [])
    {
        parent::__construct(new \Catalog\Model\UnitApi());
    }
    
    function helperIndex()
    {  
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Создайте в данном разделе единицы измерения, которые будут использоваться в ваших товарах.'));
        $helper->setTopTitle(t('Единицы измерения'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить единицу измерения')]));
        $helper->addCsvButton('catalog-unit');
        $helper->setTable(new Table\Element([
            'Columns' => [
                    new TableType\Checkbox('id'),
                    new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC,'CurrentSort' => SORTABLE_ASC,'ThAttr' => ['width' => '20']]),
                    new TableType\Text('title', t('Полное название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('stitle', t('Сокращенное название'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                            new TableType\Action\DropDown([
                                [
                                    'title' => t('Клонировать'),
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
        ]]));
        
        
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                                            new Filter\Type\Text('title',t('Полное наименование'), ['SearchType' => '%like%']),
                                                            new Filter\Type\Text('stitle',t('Короткое обозначение'), ['SearchType' => '%like%']),
                                    ]
                                    ])
                                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));
        
        return $helper;
    }    
    
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать единицу измерения {title}') : t('Добавить единицу измерения'));
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
    /**
    * Сортировка в списке
    * 
    */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING); //Указывает выше или ниже элемента to находится элемент from
        
        if ($this->api->moveElement($from, $to, $flag)) {
            $this->result->setSuccess(true);
        } else {
            $this->result->setSuccess(true)->setErrors($this->api->getErrorsStr());
        }
        
        return $this->result->getOutput();
    }
    
}


