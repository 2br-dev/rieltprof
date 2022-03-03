<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Controller\Admin;

use Article\Model\Api as ArticleApi;
use Article\Model\CatApi;
use Article\Model\Orm\Article;
use RS\Application\Application;
use RS\Controller\Admin\Crud;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Tree;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Orm\Exception as OrmException;

class Ctrl extends Crud
{
    /** @var ArticleApi */
    protected $api;
    protected $dir;

    public function __construct()
    {
        parent::__construct(new ArticleApi());
        $this->setTreeApi(new Catapi(), t('категорию статей'));
    }

    public function actionIndex()
    {
        //Если категории не существует, то выбираем пункт "Все"
        if ($this->dir > 0 && !$this->getTreeApi()->getById($this->dir)) $this->dir = 0;
        if ($this->dir > 0) $this->api->setFilter('parent', $this->dir);
        $this->getHelper()->setTopTitle(t('Статьи по тематикам'));

        return parent::actionIndex();
    }

    public function helperIndex()
    {
        $collection = parent::helperIndex();

        $this->dir = $this->url->request('dir', TYPE_STRING);
        //Параметры таблицы
        $collection->setTopHelp(t('В этом разделе вы можете размещать текстовую информацию (контент). Если у вас на сайте есть раздел «Новости», то все материалы закрепляются в соответствующей рубрике, здесь также можно размещать информацию.'));
        $collection->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Dropdown([
                    [
                        'title' => t('добавить статью'),
                        'attr' => [
                            'href' => $this->router->getAdminUrl('add', ['dir' => $this->dir]),
                            'class' => 'btn-success crud-add',
                        ]
                    ],
                    [
                        'title' => t('добавить категорию статей'),
                        'attr' => [
                            'href' => $this->router->getAdminUrl('treeAdd'),
                            'class' => 'crud-add',
                        ]
                    ]
                ]),
            ]
        ]));
        $collection->addCsvButton('article-article');
        $collection->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('short_content', t('Краткий текст'), ['hidden' => true]),
                new TableType\Datetime('dateof', t('Размещено'), ['hidden' => true, 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'TdAttr' => ['class' => 'cell-sgray'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('показать статью на сайте'),
                            'attr' => [
                                'target' => '_blank',
                                '@href' => function ($row) {
                                    /** @var Article $row */
                                    return $row->getUrl();
                                }
                            ]
                        ]
                    ])
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        //Параметры фильтра
        $collection->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('id', '№', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => 'like%']),

                        new Filter\Type\Date('dateof', t('Дата'), ['ShowType' => true]),
                    ]])
                ],
                'SecContainers' => [
                    new Filter\Seccontainer([
                        'Lines' => [
                            new Filter\Line(['items' => [
                                new Filter\Type\Text('alias', t('Псевдоним'), ['SearchType' => 'like%'])
                            ]])
                        ]
                    ])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        $collection->setTree($this->getIndexTreeElement(), $this->getTreeApi());

        $collection->setTreeBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Multiedit($this->router->getAdminUrl('treeMultiEdit')),
                new ToolbarButton\Delete(null, null, [
                    'attr' => ['data-url' => $this->router->getAdminUrl('treeDel')]
                ]),
            ],
        ]));

        $collection->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $collection->viewAsTableTree();
        return $collection;
    }

    /**
     * Возвращает объект с настройками отображения дерева
     *
     * @return Tree\Element
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    protected function getIndexTreeElement()
    {
        $tree = new Tree\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->dir,
            'pathToFirst' => $this->getTreeApi()->getPathToFirst($this->dir),
            'rootItem' => [
                'id' => 0,
                'title' => t('Все'),
                '_class' => 'root noDraggable',
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noFullValue' => true,
            ],
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('treeMove'),
            'mainColumn' => new TableType\Text('title', t('Название'), [
                'linkAttr' => ['class' => 'call-update'],
                'href' => $this->router->getAdminPattern(false, [':dir' => '@id']),
            ]),
            'tools' => new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('treeEdit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('добавить дочернюю категорию'),
                            'attr' => [
                                '@href' => $this->router->getAdminPattern('treeAdd', [':pid' => '~field~']),
                                'class' => 'crud-add',
                            ]
                        ],
                        [
                            'title' => t('клонировать'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('treeClone', [':id' => '~field~', ':pid' => '@parent']),
                            ]
                        ],
                        [
                            'title' => t('показать на сайте'),
                            'attr' => [
                                'target' => '_blank',
                                '@href' => function ($row) {
                                    if ($row['id'] > 0) {
                                        /** @var Article $row */
                                        return $row->getUrl();
                                    }
                                    return null;
                                }
                            ]
                        ],
                        [
                            'title' => t('удалить'),
                            'attr' => [
                                '@href' => $this->router->getAdminPattern('treeDel', [':chk[]' => '~field~']),
                                'class' => 'crud-remove-one',
                            ],
                        ],
                    ])]
            ),
            'headButtons' => [
                [
                    'attr' => [
                        'title' => t('Создать категорию'),
                        'href' => $this->router->getAdminUrl('treeAdd'),
                        'class' => 'add crud-add'
                    ]
                ]
            ],
        ]);
        return $tree;
    }

    public function actionTreeAdd($primaryKey = null)
    {
        if ($primaryKey === null) {
            $pid = $this->url->request('pid', TYPE_STRING, '');
            $this->getTreeApi()->getElement()->offsetSet('parent', $pid);
        }

        return parent::actionAdd($primaryKey);
    }

    public function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $parent = $this->url->request('dir', TYPE_INTEGER);
        $obj = $this->api->getElement();

        if ($primaryKey === null) {
            if ($parent) {
                $obj['parent'] = $parent;
            }
            if (!isset($primaryKey)) {
                $obj['dateof'] = date('Y-m-d H:i:s');
                $obj['user_id'] = $this->user->id;
            }
            $obj->setTemporaryId();
        }

        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать статью {title}') : t('Добавить статью'));
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    /**
     * Редактирование статьи
     *
     */
    public function actionEdit()
    {
        $byalias = $this->url->get('byalias', TYPE_STRING, false);
        if (!empty($byalias)) {
            $article = $this->api->getByAlias($byalias);
            Application::getInstance()->redirect($this->router->getAdminUrl('edit', ['id' => $article['id']]));
        }
        return parent::actionEdit();
    }

    /**
     * Метод для клонирования
     *
     */
    public function actionClone()
    {
        $this->setHelper($this->helperAdd());
        $id = $this->url->get('id', TYPE_INTEGER);

        $elem = $this->api->getElement();

        if ($elem->load($id)) {
            $clone_id = null;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->api->setElement($clone);
                $clone_id = $clone['id'];
            }
            unset($elem['id']);
            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }
}
