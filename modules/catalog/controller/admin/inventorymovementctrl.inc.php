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
 *  Контроллер документа перемещения
 *
 * Class InventoryMovement
 * @package Catalog\Controller\Admin
 */
class InventoryMovementCtrl extends \RS\Controller\Admin\Crud
{
    public $config;

    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule($this);
        parent::__construct(new \Catalog\Model\Inventory\MovementApi());
    }

    /**
     * Отображение списка документов
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Перемещения товаров'));
        $config = \RS\Config\Loader::byModule($this);
        $helper->setTopHelp(t('Этим документом фиксируются перемещения товаров между складами. При создании документа появляются 2 связанных документа: списание и ожидание.'));
        if(!$config['inventory_control_enable']){
            $helper->getTopToolbar()->removeItem('add');
            $helper->removeSection('bottomToolbar');
            $smarty = new \RS\View\Engine();
            $notice = $smarty->fetch('%catalog%/inventory/inventory_disabled_notice.tpl');
            $helper->setBeforeTableContent($notice);
        }
        $helper -> setTable(new Table\Element([
                'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Usertpl('title', t('Документ'), '%catalog%/form/inventory/field_movement_title.tpl'),
                    new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray'], 'ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('warehouse_from', t('Со склада'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('warehouse_to', t('На склад'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\StrYesno('applied', t('Проведен'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('linked_document', t('Связанный документ'), '%catalog%/form/inventory/field_linked_document.tpl'),
                    new TableType\Datetime('date', t('Дата оформления'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'noajax' => true,
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ],
            ]
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
                                        ->join(new \Catalog\Model\Orm\Inventory\MovementProducts(), 'A.id = item.document_id', 'item');
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
                            new Filter\Type\Select('warehouse_from', t('Со склада'),  ['' => t('Неважно')] + \Catalog\Model\WareHouseApi::staticSelectList()),
                            new Filter\Type\Select('warehouse_to', t('На склад'),  ['' => t('Неважно')] + \Catalog\Model\WareHouseApi::staticSelectList()),
                        ]
                        ]),
                    ]
                ])
            ]),
            'Caption' => t('Поиск')
        ]));
        return $helper;
    }

    /**
     * Форма добавления документа
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении,
     *                                   иначе будет вызов стандартного _successSave метода
     * @param null|\RS\Controller\Admin\Helper\CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $orm = $this->api->getElement();
        if(!$primaryKeyValue){
            $orm['date'] = date('Y-m-d H:i:s');
        }
        $helper = $this->getHelper();
        $helper->setTopTitle($primaryKeyValue ? t('Редактировать документ перемещения') : t('Добавить перемещение товаров'));
        if(!$this->config['inventory_control_enable']){
            $helper->removeSection('bottomToolbar');
            $orm['inventory_disabled'] = true;
        }
        $this->router->getCurrentRoute()->addExtra('type', \Catalog\Model\Orm\Inventory\Movement::DOCUMENT_TYPE_MOVEMENT);
        if($this->url->isPost()) {
            $refresh_mode = $this->url->request('refresh', TYPE_BOOLEAN);
            $warehouse_id = $this->url->request('warehouse', TYPE_INTEGER, 0);
            $recalculate = $this->url->request('recalculate', TYPE_BOOLEAN, false);
            $items = $this->url->request('items', TYPE_ARRAY, null);
            $products = $this->api->prepareProductsArray($items, $warehouse_id, $recalculate);
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
}