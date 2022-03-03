<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Admin;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\BrandApi;
use Catalog\Model\CostApi;
use Catalog\Model\DirApi;
use Catalog\Model\Filter\PropertyFilter;
use Catalog\Model\Inventory\InventoryTools;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Inventory;
use Catalog\Model\Orm\Property\ItemValue as PropertyItemValue;
use Catalog\Model\PropertyApi;
use Catalog\Model\SeoReplace;
use Catalog\Model\VirtualDir;
use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Tree;
use RS\Orm\Request as OrmRequest;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use RS\View\Engine as ViewEngine;

/**
 * Контроллер каталога товаров
 */
class Ctrl extends Crud
{
    const SHOW_CHILDS_VAR = 'showchild';

    public $dir;
    public $brandlist;
    public $brandapi;
    /** @var \Catalog\Model\Api $api */
    public $api;
    public $showchilds;
    public $me_form_tpl;
    public $me_form_tpl_dir = 'me_form_dir.tpl';

    public function __construct()
    {
        parent::__construct(new ProductApi());
        $this->setTreeApi(new Dirapi(), t('категорию'));
    }

    public function actionIndex()
    {
        if ($this->dir > 0) {
            if (!$this->getTreeApi()->getOneItem($this->dir)) {
                $this->dir = 0; //Если категории не существует, то выбираем пункт "Все"
            } else {
                if ($this->showchilds) {
                    $child_dir_ids = DirApi::getInstance()->getChildsId($this->dir);
                    $this->api->setFilter('dir', $child_dir_ids, 'in');
                } else {
                    $this->api->setFilter('dir', $this->dir);
                }
            }
        }
        $this->getHelper()->setTopHelp($this->view->fetch('help/ctrl_index.tpl'));

        return parent::actionIndex();
    }

    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Каталог товаров'));
        $this->dir = $this->url->get('dir', TYPE_INTEGER, 0);

        $this->showchilds = $this->url->get(self::SHOW_CHILDS_VAR, TYPE_INTEGER, false);
        if ($this->showchilds === false) {
            $this->showchilds = $this->url->cookie(self::SHOW_CHILDS_VAR, TYPE_INTEGER, 0);
        } else {
            $this->app->headers->addCookie(self::SHOW_CHILDS_VAR, $this->showchilds, time() + (60 * 60 * 365 * 10));
        }

        //Загружаем информацию о характеристиках текущей группы
        $property_api = new PropertyApi();
        $group_properties_list = $property_api->getGroupProperty($this->dir);

        $group_properties = [];
        foreach ($group_properties_list as $items) {
            $group_properties += $items['properties'];
        }

        $helper->viewAsTableTree();
        $dir = $this->dir;
        $this->api->queryObj()->select = 'DISTINCT A.*';

        //Добавляем в колонки с типами цен
        $cost_api = new CostApi();
        $cost_api->setFilter('type', 'manual');
        $cost_types = $cost_api->getList();
        $cost_columns = [];
        foreach ($cost_types as $cost_type) {
            $cost_columns[] = new TableType\Text('cost_' . $cost_type['id'], t('Цена ') . $cost_type['title'], [
                'Sortable' => SORTABLE_BOTH,
                'hidden' => true,
                'cost_type' => $cost_type,
            ]);
        }
        $config = ConfigLoader::byModule($this);
        $inventory_control_fields = [
            new TableType\Userfunc('remains', t('Остаток'), function ($value) {
                return (float)$value;
            }, ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
            new TableType\Userfunc('reserve', t('Резерв'), function ($value) {
                return (float)$value;
            }, ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
            new TableType\Userfunc('waiting', t('Ожидание'), function ($value) {
                return (float)$value;
            }, ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
        ];
        $columns = [
            new TableType\Checkbox('id', ['showSelectAll' => true]),
            new TableType\Text('title', t('Название'), [
                'LinkAttr' => [
                    'class' => 'crud-edit'
                ],
                'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $dir]), 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC]),
            new TableType\Image('images', t('Фото'), 30, 30, 'xy', [
                'LinkAttr' => [
                    'class' => 'crud-edit',
                ],
                'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC,
                'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $dir]),
                'TdAttr' => [
                    'style' => 'padding-top:0; padding-bottom:0;'
                ]
            ]),
            new TableType\Text('barcode', t('Артикул'), ['Sortable' => SORTABLE_BOTH]),
            new TableType\Text('dateof', t('Дата поступления'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
            new TableType\Text('brand_id', t('Бренд'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
            new TableType\Text('weight', t('Вес'), ['Sortable' => SORTABLE_ASC, 'hidden' => true]),
            new TableType\Text('sortn', t('Сорт. вес'), ['Sortable' => SORTABLE_ASC]),
            new TableType\Text('group_id', t('Группа'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
            new TableType\Yesno('public', t('Видим.'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])
            ]),
            new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray'], 'ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
            new TableType\Text('num', $config['inventory_control_enable'] ? t('Доступно') : t('Остаток'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
        ];
        $helper->setListFunction('getTableList');
        $product_actions = [
            [
                'title' => t('удалить'),
                'attr' => [
                    'class' => 'crud-get',
                    'data-confirm-text' => t('Вы действительно хотите удалить данный товар?'),
                    '@href' => $this->router->getAdminPattern('del', [':chk[]' => '@id']),
                ]
            ],
            [
                'title' => t('показать товар на сайте'),
                'attr' => [
                    'target' => '_blank',
                    '@href' => $this->router->getUrlPattern('catalog-front-product', [':id' => '@_alias'], false),
                ]
            ],
            [
                'title' => t('клонировать товар'),
                'attr' => [
                    'class' => 'crud-add',
                    '@href' => $this->router->getAdminPattern('clone', [':id' => '@id']),
                ]
            ]
        ];
        $inventory_product_actions = [
            [
                'title' => t('статистика по складам'),
                'attr' => [
                    'class' => 'crud-add',
                    '@href' => $this->router->getAdminPattern('productStatistics', [':product_id' => '@id'], 'catalog-inventorystatisticsctrl'),
                ]
            ]
        ];
        $helper->setTable(new Table\Element([
            'Columns' => array_merge(
                $config['inventory_control_enable'] ? array_merge($columns, $inventory_control_fields) : $columns,
                $cost_columns,
                [
                    new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~', 'dir' => $dir]), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]
                        ]),
                        new TableType\Action\DropDown($config['inventory_control_enable'] ? array_merge($product_actions, $inventory_product_actions) : $product_actions)],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ])
        ]));

        //Добавляем условия для выборки цен, если колонки отображаются
        /** @var Table\Control $table */
        $table = $helper['table'];
        $table->fill();
        $add_cost_types = [];
        foreach ($table->getTable()->getColumns() as $n => $col) {
            if (isset($col->property['cost_type'])) {
                if (!$col->isHidden()) {
                    $add_cost_types[] = $col->property['cost_type'];
                }
            }
        }
        $this->api->addCostQuery($add_cost_types);

        $helper->setTreeListFunction('listWithAll');

        $helper->setTree($this->getIndexTreeElement(), $this->getTreeApi());

        $helper->setTreeFilter($this->getIndexTreeFilterControl());

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('id', '№'),
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('barcode', t('Артикул'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('num', t('Общий остаток'), ['Attr' => ['class' => 'w60'], 'showType' => true]),
                        new Filter\Type\Select('public', t('Публичный'), ['' => t('Неважно'), '1' => t('Да'), '0' => t('Нет')])
                    ]
                    ])
                ],
                'SecContainers' => [
                    new Filter\Seccontainer([
                        'Lines' => [
                            new Filter\Line([
                                'Items' => [
                                    new Filter\Type\Date('dateof', t('Дата поступления'), ['showtype' => true]),
                                    new Filter\Type\Select('brand_id', t('Бренд'), ['' => t('Любой')] + BrandApi::staticSelectList([])),
                                    new Filter\Type\Text('group_id', t('Группа'), ['SearchType' => '%like%']),
                                    new Filter\Type\Text('xml_id', t('Идентификатор в системе 1С'), ['SearchType' => '%like%'])
                                ]
                            ]),
                            new Filter\Line([
                                'Items' => [
                                    new PropertyFilter($group_properties)
                                ]
                            ])
                        ]
                    ])
                ]]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
            'AddParam' => ['hiddenfields' => ['dir' => $dir]],
            'Caption' => t('Поиск по товарам')
        ]));

        $dir_count = $this->getTreeApi()->getListCount();

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Dropdown([
                    $dir_count > 0 ?
                        [
                            'title' => t('добавить товар'),
                            'attr' => [
                                'href' => $this->router->getAdminUrl('add', ['dir' => $dir]),
                                'class' => 'btn-success crud-add'
                            ]
                        ] : null,

                    [
                        'title' => t('добавить категорию'),
                        'attr' => [
                            'href' => $this->router->getAdminUrl('treeAdd', ['pid' => $dir]),
                            'class' => 'crud-add' . ($dir_count == 0 ? ' btn-success' : '')
                        ]
                    ],
                    [
                        'title' => t('добавить спецкатегорию'),
                        'attr' => [
                            'href' => $this->router->getAdminUrl('TreeAdd', ['spec' => 1]),
                            'class' => 'crud-add'
                        ]
                    ]
                ]),
            ]
        ]));

        $helper['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Импорт/Экспорт')
            ],
            [
                'title' => t('Экспорт категорий в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'catalog-dir', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Экспорт товаров в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'catalog-product', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Экспорт комплектаций в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'catalog-offer', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Экспорт остатков и цен в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'catalog-simplepricestockupdate', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт категорий из CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'catalog-dir', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт товаров из CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'catalog-product', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт комплектаций из CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'catalog-offer', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт остатков и цен из CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'catalog-simplepricestockupdate', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт изображений из ZIP-архива'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl(false, ['referer' => $this->url->selfUri()], 'catalog-importphotos'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт товаров из YML'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl(false, ['referer' => $this->url->selfUri()], 'catalog-importyml'),
                    'class' => 'crud-add'
                ]
            ],
        ]), 'import');

        $helper->addHiddenFields(['dir' => $dir]);

        $helper->setTreeBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\DropUp([
                    [
                        'title' => t('редактировать'),
                        'attr' => [
                            'data-url' => $this->router->getAdminUrl('treeMultiEdit'),
                            'class' => 'btn-alt btn-primary crud-multiedit'
                        ],
                    ]
                ], ['attr' => ['class' => 'edit']]),

                new ToolbarButton\Delete(null, null, ['attr' =>
                    ['data-url' => $this->router->getAdminUrl('treeDel')]
                ]),
            ]
        ]));
        $window_width = 500;

        $add_to_document = [new ToolbarButton\DropUp([
            [
                'title' => t('добавить в документ'),
            ],
            [
                'title' => t('Оприходование'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Document::DOCUMENT_TYPE_ARRIVAL], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
            [
                'title' => t('Списание'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Document::DOCUMENT_TYPE_WRITE_OFF], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
            [
                'title' => t('Ожидание'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Document::DOCUMENT_TYPE_WAITING], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
            [
                'title' => t('Резервирование'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Document::DOCUMENT_TYPE_RESERVE], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
            [
                'title' => t('Инвентаризация'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Inventorization::DOCUMENT_TYPE_INVENTORY], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
            [
                'title' => t('Перемещение'),
                'attr' => [
                    'data-url' => $this->router->getAdminUrl('AddProductsFromCatalog', ['document_type' => Inventory\Movement::DOCUMENT_TYPE_MOVEMENT], 'catalog-inventoryctrl'),
                    'class' => 'crud-multiaction',
                    'data-crud-dialog-height' => 400,
                    'data-crud-dialog-width' => $window_width,
                ],
            ],
        ], ['attr' => ['class' => 'edit']])];
        $bottom_toolbar_items = [
            new ToolbarButton\DropUp([
                [
                    'title' => t('редактировать'),
                    'attr' => [
                        'data-url' => $this->router->getAdminUrl('multiEdit'),
                        'class' => 'crud-multiedit'
                    ],
                ],
            ], ['attr' => ['class' => 'edit']]),
            new ToolbarButton\Delete(null, null, ['attr' =>
                ['data-url' => $this->router->getAdminUrl('del')]
            ]),
        ];

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => $config['inventory_control_enable'] ? array_merge($bottom_toolbar_items, $add_to_document) : $bottom_toolbar_items,
        ]));
        return $helper;
    }

    /**
     * Возвращает объект с настройками отображения дерева
     *
     * @return Tree\Element
     */
    protected function getIndexTreeElement()
    {
        $tree = new Tree\Element([
            'disabledField' => 'public',
            'classField' => '_class',
            'disabledValue' => '0',
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->dir,
            'rootItem' => [
                'id' => 0,
                'name' => t('Все'),
                '_class' => 'root noDraggable',
                //Устанавливаем собственные инструменты
                'treeTools' => new TableType\Actions('id', [
                    new TableType\Action\Edit(RouterManager::obj()->getAdminPattern('treeEdit', ['id' => 0])),
                ]),
                'noDraggable' => true,
                'noCheckbox' => true,
                'noRedMarker' => true
            ],
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('treeMove'),
            'mainColumn' => new TableType\Usertpl('name', t('Название'), '%catalog%/tree_item_cell.tpl', [
                'linkAttr' => ['class' => 'call-update'],
                'href' => $this->router->getAdminPattern(false, [':dir' => '@id', 'c' => $this->url->get('c', TYPE_ARRAY)])
            ]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('treeEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id'
                    ]
                ]),
                new TableType\Action\DropDown([
                    [
                        'title' => t('добавить дочернюю категорию'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('treeAdd', [':pid' => '~field~']),
                            'class' => 'crud-add'
                        ]
                    ],
                    [
                        'title' => t('клонировать категорию'),
                        'attr' => [
                            'class' => 'crud-add',
                            '@href' => $this->router->getAdminPattern('treeClone', [':id' => '~field~', ':pid' => '@parent']),
                        ]
                    ],
                    [
                        'title' => t('показать на сайте'),
                        'attr' => [
                            '@href' => $this->router->getUrlPattern('catalog-front-listproducts', [':category' => '@_alias']),
                            'target' => 'blank'
                        ]
                    ],
                    [
                        'title' => t('удалить'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('treeDel', [':chk[]' => '~field~']),
                            'class' => 'crud-remove-one'
                        ]
                    ],
                ])
            ]),
            'headButtons' => [
                [
                    'attr' => [
                        'title' => t('Создать категорию'),
                        'href' => $this->router->getAdminUrl('treeAdd', ['pid' => $this->dir]),
                        'class' => 'add crud-add'
                    ]
                ],
                [
                    'attr' => [
                        'title' => t('Создать спец. категорию'),
                        'href' => $this->router->getAdminUrl('treeAdd', ['spec' => 1]),
                        'class' => 'addspec crud-add'
                    ]
                ],
                $this->showchilds ?
                    [
                        'attr' => [
                            'title' => t('Включено отображение товаров в подкатегориях. Нажмите, чтобы отключить'),
                            'href' => $this->url->replaceKey([self::SHOW_CHILDS_VAR => 0]),
                            'class' => 'showchilds-on call-update'
                        ]
                    ] : [
                    'attr' => [
                        'title' => t('Отключено отображение товаров в подкатегориях. Нажмите, чтобы включить'),
                        'href' => $this->url->replaceKey([self::SHOW_CHILDS_VAR => 1]),
                        'class' => 'showchilds-off call-update'
                    ]
                ]
            ],
        ]);

        return $tree;
    }

    /**
     * Возвращает объект с настройками фильтра дерева
     * Перегружается у наследника
     *
     * @return Filter\Control
     */
    protected function getIndexTreeFilterControl()
    {
        $filter = new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('name', t('Название'), ['SearchType' => '%like%', 'attr' => ['style' => 'width:300px']]),
                    ]])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->getTreeApi()->defAlias()],
            'filterVar' => 'c',
            'Caption' => t('Поиск по категориям')
        ]);

        return $filter;
    }

    /**
     * Метод переключения флага публичности
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    public function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $product = $this->api->getOneItem($id);
        if ($product) {
            $product['public'] = !$product['public'];
            $product->update();
        }
        return $this->result->setSuccess(true);
    }

    public function actionAjaxPropertySearchListValues()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_READ)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $prop_id = $this->url->request('prop_id', TYPE_INTEGER);
        $disabled = $this->url->request('disabled', TYPE_BOOLEAN);
        $query = $this->url->request('query', TYPE_STRING);
        $page = $this->url->request('page', TYPE_INTEGER, 1);
        $page_size = $this->url->request('page_size', TYPE_INTEGER, 20);
        $offset = ($page - 1) * $page_size;

        if (!$prop_id) {
            return $this->result->setSuccess(false)->addEMessage('Не передан id характеристики');
        }

        $q = OrmRequest::make()
            ->from(new PropertyItemValue())
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'prop_id' => $prop_id,
            ])
            ->orderby('sortn');
        if ($query) {
            $q->where("value like '%$query%'");
        }
        $count_values = $q->count();
        $count_pages = ceil($count_values / $page_size);
        $q->limit($offset, $page_size);
        $list = $q->exec()->fetchSelected('id', 'value');

        $view = new ViewEngine();
        $view->assign([
            'list' => $list,
            'page' => $page,
            'count_values' => $count_values,
            'count_pages' => $count_pages,
            'disabled' => $disabled,
        ]);
        $html = $view->fetch('%catalog%/property_val_big_list_items.tpl');

        return $this->result->setSuccess(true)->addSection('html', $html);
    }

    /**
     * Удаление товаров по параметру chk
     *
     */
    public function actionDel()
    {
        @set_time_limit(200);
        $ids = $this->modifySelectAll($this->url->request('chk', TYPE_ARRAY, [], false));
        $dir = $this->url->request('dir', TYPE_INTEGER, 0);

        $success = $this->api->multiDelete($ids, $dir);

        if (!$success) {
            foreach ($this->api->getErrors() as $error) {
                $this->result->addEMessage($error);
            }
        }
        return $this->result->setSuccess($success)->getOutput();
    }

    /**
     * AJAX
     */
    public function actionGetPropertyList()
    {
        $propapi = new Propertyapi();
        $list = $propapi->getList();
        $this->view->assign('list', $list);
        return $this->view->fetch('property_full_list.tpl');
    }

    /**
     * Открытие окна добавления и редактирования категории
     *
     * @param integer $primaryKeyValue - первичный ключ записи, передаётся для редактирования
     * @return string
     * @throws \RS\Exception
     */
    public function actionTreeAdd($primaryKeyValue = null)
    {
        $spec = $this->url->request('spec', TYPE_INTEGER); //действие со спец категорией.        
        /** @var Dir $elem */
        $elem = $this->getTreeApi()->getElement();
        //Для SEO генерации подсказок заменяем HINT надписи
        $seoGen = new SeoReplace\Dir();
        $seoGen->replaceORMHint($elem);

        $seoGenProduct = new SeoReplace\Product();
        $seoGenProduct->hint_fields = [
            'product_meta_title',
            'product_meta_keywords',
            'product_meta_description',
            'default_product_meta_title',
            'default_product_meta_keywords',
            'default_product_meta_description',
        ];
        $seoGenProduct->replaceORMHint($elem);

        //Проверка на спецкатегорию
        if ($primaryKeyValue !== null && $elem['is_spec_dir'] == 'Y') $spec = 1;

        if ($spec) {
            $elem['is_spec_dir'] = 'Y';
            $elem['parent'] = 0;
            $elem['__alias']->setChecker('chkEmpty', t('Необходимо указать Псевдоним'));
        }

        if ($primaryKeyValue === null) {
            $elem['parent'] = $this->url->get('pid', TYPE_INTEGER, 0);
            $elem['public'] = 1;
        } else {
            if (empty($_POST)) $elem->fillProperty();
        }

        if ($elem['id'] == 0) {
            $config = ConfigLoader::byModule($this);
            $elem['default_product_meta_title'] = $config['default_product_meta_title'];
            $elem['default_product_meta_keywords'] = $config['default_product_meta_keywords'];
            $elem['default_product_meta_description'] = $config['default_product_meta_description'];
        }

        return parent::actionTreeAdd($primaryKeyValue);
    }

    public function helperTreeAdd()
    {
        $helper = parent::helperTreeAdd();
        $spec = $this->url->request('spec', TYPE_INTEGER); //действие со спец категорией.        
        $elem = $this->getTreeApi()->getElement();
        if ($elem['id'] && $elem['is_spec_dir'] == 'Y') $spec = 1;

        if ($spec) {
            $helper->setFormSwitch('spec');
        }

        return $helper;
    }

    public function helperTreeEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);
        if ($id) {
            $this->getTreeApi()->getElement()->load($id);
        } else {
            $tree_element = $this->getTreeApi()->getElement();
            $tree_element['id'] = '0'; //Необходимо всвязи с редактирование категории - 0
            $tree_element['name'] = t('Все');
        }
        $helper = $this->helperTreeAdd()->setBottomToolbar($this->buttons(['saveapply', 'cancel']));
        if (!$id) $helper->setFormSwitch('root');

        return $helper;
    }

    public function actionTreeMultiEdit()
    {
        $dir = $this->getTreeApi()->getElement();
        //Для SEO генерации подсказок заменяем HINT надписи
        $seoGen = new SeoReplace\Dir();
        $seoGen->replaceORMHint($dir);

        $this->me_form_tpl = $this->me_form_tpl_dir;

        return parent::actionTreeMultiEdit();
    }

    /**
     * Открытие окна добавления и редактирования товара
     *
     * @param integer $primaryKeyValue - первичный ключ товара(если товар уже создан)
     * @param boolean $returnOnSuccess - Если true, то будет возвращать true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текущий хелпер
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        /** @var \Catalog\Model\Orm\Product $obj */
        $obj = $this->api->getElement();
        $obj->useOffersUnconvertedPropsdata(true);
        //Для SEO генерации подсказок заменяем HINT надписи
        $seoGen = new SeoReplace\Product();
        $seoGen->replaceORMHint($obj, "%catalog%/hint/seohint.tpl");

        if ($primaryKeyValue <= 0) {
            if ($primaryKeyValue == 0) {
                $dir = $this->url->get('dir', TYPE_INTEGER);
                $spec_dirs = $obj->getSpecDirs();
                if (isset($spec_dirs[$dir])) {
                    $obj['xspec'] = [$dir];
                } else {
                    $obj['xdir'] = [$dir];
                }
                $obj['barcode'] = $this->api->genereteBarcode();

                $obj->setTemporaryId();
                $obj['dateof'] = date('Y-m-d H:i:s');
                $obj['public'] = 1;
            }
            $this->getHelper()->setTopTitle(t('Добавить товар'));
        } else {
            $obj->fillCategories();
            $obj->fillCost();
            $obj->fillOffers();
            $obj->fillOffersStock();
            $this->getHelper()->setTopTitle(t('Редактировать товар ') . '{title}');
        }
        if (!$this->url->isPost()) $obj->fillProperty();

        if ($this->url->isPost() && $this->url->request('prop', TYPE_ARRAY, null) === null) {
            $this->user_post_data = ['prop' => []]; //На случай, когда удалены все характеристики
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    public function helperEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);
        $product = $this->api->getElement();
        $product->load($id);

        $helper = parent::helperEdit();
        $helper['bottomToolbar']
            ->addItem(
                new ToolbarButton\Button($this->router->getUrl('catalog-front-product', ['id' => $product['_alias']], false), t('Посмотреть на сайте'), [
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]), 'view'
            )
            ->addItem(
                new ToolbarButton\delete($this->router->getAdminUrl('delProd', ['id' => $id, 'dialogMode' => $this->url->request('dialogMode', TYPE_INTEGER)]), null, [
                    'noajax' => true,
                    'attr' => [
                        'class' => 'btn-alt btn-danger delete crud-get crud-close-dialog',
                        'data-confirm-text' => t('Вы действтельно хотите удалить данный товар из всех категорий?')
                    ]
                ]), 'delete'
            );

        return $helper;
    }

    public function actionDelProd()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        if (!empty($id)) {
            $obj = $this->api->getElement();
            $obj['id'] = $id;
            $obj->delete();
        }

        if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
            $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
        }

        return $this->result
            ->setSuccess(true)
            ->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
    }

    /**
     * Вызывает окно мультиредактирования
     *
     */
    public function actionMultiEdit()
    {
        $costapi = new Costapi();
        $this->param['addparam']['cost_list'] = $costapi->getList();
        //Для SEO генерации подсказок заменяем HINT надписи
        $elem = $this->api->getElement();
        $seoGen = new SeoReplace\Product();
        $seoGen->replaceORMHint($elem, "%catalog%/hint/seohint.tpl");

        $doedit = $this->url->request('doedit', TYPE_ARRAY, []);
        $xdir = $this->url->post('xdir', TYPE_ARRAY);
        if (in_array('xdir', $doedit) && !isset($xdir['notdelbefore'])) $doedit[] = 'maindir';
        if (in_array('num', $doedit)) $doedit[] = 'unit';
        $this->url->set('doedit', $doedit, REQUEST);

        return parent::actionMultiEdit();
    }

    /**
     * Клонирование товара
     *
     */
    public function actionClone()
    {
        $this->setHelper($this->helperAdd());
        $id = $this->url->get('id', TYPE_INTEGER);

        $elem = $this->api->getElement();
        $config = ConfigLoader::byModule($this);
        if ($elem->load($id)) {
            if ($config['inventory_control_enable']) {
                $elem['remains'] = 0;
                $elem['waiting'] = 0;
                $elem['reserve'] = 0;
                $elem['num'] = 0;
            }
            $clone_id = null;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->api->setElement($clone);
                $clone_id = $clone['id'];
                if ($config['inventory_control_enable']) {
                    $tools = new InventoryTools();
                    $tools->setToZeroStocks($clone_id);
                }
            }
            unset($elem['alias']);
            unset($elem['xml_id']);
            unset($elem['comments']);
            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }

    /**
     * Клонирование директории
     *
     */
    public function actionTreeClone()
    {
        $this->setHelper($this->helperTreeAdd());
        $id = $this->url->get('id', TYPE_INTEGER);

        $elem = $this->getTreeApi()->getElement();

        if ($elem->load($id)) {
            $clone_id = null;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->getTreeApi()->setElement($clone);
                $clone_id = $clone['id'];
            }
            unset($elem['id']);
            unset($elem['alias']);
            unset($elem['xml_id']);
            unset($elem['sortn']);

            return $this->actionTreeAdd($clone_id);
        } else {
            return $this->e404();
        }
    }

    /**
     * Возвращает форму одной характеристики для виртуальной категории
     */
    public function actionAddVirtualDirPropery()
    {
        $prop_id = $this->url->request('prop_id', TYPE_INTEGER);

        $virtual_dir = new VirtualDir();
        $form = $virtual_dir->getPropertyFilterForm($prop_id);

        if (!$form) {
            $this->e404(t('Характеристика не найдена'));
        }

        return $this->result->setSuccess(true)->setHtml($form);
    }

    /**
     * Ищет товар/комплектацию по штрихкоду
     * Передаёт id товара, если нйден товар
     * Передаёт id товара, порядковый номер и id комплектации, если найдена комплектация
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Orm\Exception
     * @throws \RS\Exception
     */
    function actionGetProductBySku()
    {
        $sku = $this->url->get('sku', TYPE_STRING);
        if ($sku) {
            $data = $this->api->getDataByBarcode($sku); // Получаем товар/комплектацию по штрихкоду
            if ($data) {
                $this->result->addSection($data);
                $message = t("Добавлен товар: ") . $data['title'] . ($data['offer_title'] ? t(" в комплектации \"") . $data['offer_title'] . "\"" : "");
                $this->result->addMessage($message);
                $this->result->setSuccess(true);
            } else { // Если по штрихкоду ничего не найдено
                $this->result->addEMessage('Товар со штрихкодом: "' . $sku . '" не найден');
                $this->result->setSuccess(false);
            }
        } // Если пустой запрос, то ничего не делаем
        return $this->result;
    }
}
