<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Table,
    \RS\Html\Filter;

/**
 *    Класс от которовго наследуются контроллеры резервирования, перемещения, оприходования, списания
 *
 * Class AbstractDocument
 * @package Catalog\Controller\Admin
 */
abstract class AbstractDocument extends \RS\Controller\Admin\Crud
{
    public
        $template_title,
        $top_title,
        $type,
        $top_help,
        $config;

    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule($this);
        parent::__construct(new \Catalog\Model\Inventory\DocumentApi());
    }

    /**
     * Отображение списка документов
     */
    function actionIndex()
    {
        $this->api->setFilter('type', $this->type);
        return parent::actionIndex();
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        if($this->top_help){
            $helper->setTopHelp($this->top_help);
        }
        $helper->setTopTitle($this->top_title);
        if(!$this->config['inventory_control_enable']){
            $smarty = new \RS\View\Engine();
            $notice = $smarty->fetch('%catalog%/inventory/inventory_disabled_notice.tpl');
            $helper->setBeforeTableContent($notice);
            $helper->getTopToolbar()->removeItem('add');
            $helper->removeSection('bottomToolbar');
        }
        $helper -> setTable(new Table\Element([
                'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Usertpl('title', t('Документ'), $this->template_title),
                    new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray'], 'ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('warehouse', t('Склад'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\StrYesno('applied', t('Проведен'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\StrYesno('archived', t('Заархивирован'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('linked_document', t('Связанный документ'), '%catalog%/form/inventory/field_linked_document.tpl'),
                    new TableType\Datetime('date', t('Дата'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('provider', t('Поставщик'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'noajax' => true,
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ]]
        ));
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' => [
                    new Filter\Line( ['Items' => [
                        new Filter\Type\Product('product_id', t('Товар'), [
                            'ModificateQueryCallback' => function($q, $filter_type) {
                                $product_id = $filter_type->getValue();
                                if($product_id){
                                    $q  ->select('A.id')
                                        ->where("item.product_id = $product_id")
                                        ->join(new \Catalog\Model\Orm\Inventory\DocumentProducts(), 'A.id = item.document_id', 'item');
                                }
                                return $q;
                            }
                        ]),
                    ]]),
                ],
                'SecContainer' => new Filter\Seccontainer( [
                    'Lines' =>  [
                        new Filter\Line( ['Items' => [
                            new Filter\Type\Text('A.id', '№'),
                            new Filter\Type\DateRange('date', t('Дата оформления')),
                            new Filter\Type\Select('applied', t('Проведен'), [''=>t('Неважно'),'1' => t('Да'),'0'=>t('Нет')]),
                            new Filter\Type\Select('warehouse', t('Склад'),  ['' => t('Неважно')] + \Catalog\Model\WareHouseApi::staticSelectList()),
                            new Filter\Type\Select('provider', t('Поставщик'),  ['' => t('Неважно')] + \Catalog\Model\Inventory\InventoryTools::staticSelectProviders()),
                            new Filter\Type\Select('archived', t('Заархивирован'),  ['' => t('Неважно'), '0' => t('Нет'), '1' => t('Да')]),
                        ]
                        ]),
                    ],
                ])
            ]),
            'Caption' => t('Поиск')
        ]));
        $helper['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Экспорт в CSV'),
                'attr' => [
                    'data-url' => \RS\Router\Manager::obj()->getAdminUrl('exportCsv', [
                        'params' => ['ctrl' => $this->getControllerName()],
                        'schema' => 'catalog-inventorydocument',
                        'referer' => $this->url->selfUri()],
                        'main-csv'),
                    'class' => 'crud-add'
                ]
            ],])
        );
        return $helper;
    }

    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $orm = $this->api->getElement();
        if(!$primaryKeyValue){
            $orm['date'] = date('Y-m-d H:i:s');
        }
        $helper = $this->getHelper();
        if($this->config['inventory_control_enable'] && $orm['archived']){
            $this->disableOrmEdit($orm, $helper, t('(Документ в архиве, редактирование не доступно)'));
        }
        if(!$this->config['inventory_control_enable']){
            $this->disableOrmEdit($orm, $helper, t('(Складской учет отключен, редактирование не доступно)'));
            $orm['inventory_disabled'] = true;
        }
        if($orm->getLinkedDocuments()){
            $orm['has_links'] = true;
            $this->disableOrmEdit($orm, $helper, t('(Документ имеет зависимости, редактирование не доступно)'));
        }
        $this->router->getCurrentRoute()->addExtra('type', $this->type);
        if($this->url->isPost()) {
            $refresh_mode = $this->url->request('refresh', TYPE_BOOLEAN);
            $items = $this->url->request('items', TYPE_ARRAY, null);
            $products = $this->api->prepareProductsArray($items);

            if (!$refresh_mode) {
                $orm['items'] = $items;
                $orm['type'] = $this->url->request('type', TYPE_STRING);
                return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
            } else {
                $this->wrap_output = false;
                $warehouses = \Catalog\Model\WareHouseApi::staticSelectList();
                $this->view->assign([
                    'api' => $this->api,
                    'products' => $products,
                    'warehouses' => $warehouses,
                ]);
                return $this->result->setTemplate("%catalog%form/inventory/products_in_table.tpl");
            }
        }
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    function disableOrmEdit($orm, $helper, $message)
    {
        $properties = $orm->getPropertyIterator();
        $properties->setPropertyOptions([
            'readonly' => true
        ]);
        $helper->removeSection('bottomToolbar');
        $helper->setTopTitle($helper->getFormTitle()." ".$message);
    }
}