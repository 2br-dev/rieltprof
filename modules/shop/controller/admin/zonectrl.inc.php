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
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер Управление скидочными купонами
*/
class ZoneCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $parent,
        $api;
    
    function __construct()
    {
        parent::__construct(new \Shop\Model\ZoneApi());
    }
    
    function helperIndex()
    {
        $this->parent = $this->url->request('pid', TYPE_INTEGER, 0);
        
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Зона - это пользовательское объединение географических регионов, например, Москву, Санкт-Петербург, Краснодар можно объеденить в зону "Крупные города". Зоны используются для формирования правил и формул расчета доставки, для определения видимости способов доставок и оплат и в других разделах.'));
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Add($this->router->getAdminUrl('add', ['pid' => $this->parent]), t('Добавить'))
            ]
        ]));
        $helper->addCsvButton('shop-region');
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'linkAttr' => ['class' => 'crud-add']]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                        new TableType\Action\DropDown([
                                [
                                    'title' => t('Клонировать зону'),
                                    'attr' => [
                                        'class' => 'crud-add',
                                        '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                    ]
                                ],
                        ]),
                    ]
                ),
            ]
        ]));
        
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['Items' => [
                            new Filter\Type\Text('id','№', ['attr' => ['class' => 'w100']]),
                            new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ]
            ]),
            'Caption' => t('Поиск по зонам'),
            'AddParam' => ['hiddenfields' => ['pid' => $this->parent]]
        ]));
        
        $helper['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Импорт/Экспорт'),
                'attr' => [
                    'class' => 'button',
                    'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                ]
            ],
            [
                'title' => t('Экспорт в CSV'),
                'attr' => [
                    'href' => \RS\Router\Manager::obj()->getAdminUrl('exportCsv', ['schema' => 'shop-zone', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт из CSV'),
                'attr' => [
                    'href' => \RS\Router\Manager::obj()->getAdminUrl('importCsv', ['schema' => 'shop-zone', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
        ]), 'import');
        
        
        return $helper;
    }
    
    function actionIndex()
    {
        $helper = $this->getHelper();
        $helper->setTopTitle(t('Зоны'));            
        return parent::actionIndex();
    }
    
    /**
    * Добавление купонов
    */
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if (!$primaryKey) {
            $this->getHelper()->setTopTitle(t('Добавить зону'));
        } else {            
            $this->api->getElement()->fillRegions();
            $this->getHelper()->setTopTitle(t('Редактировать зону').' {title}');        
        }
        
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
}