<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Admin;

use Catalog\Model\WareHouseApi;
use Catalog\Model\WareHouseGroupApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Category;

/**
 * Класс контроллера складов админки
 */
class WareHouseCtrl extends Crud
{
    /** @var WareHouseApi */
    protected $api;
    protected $group;

    public function __construct()
    {
        parent::__construct(new WareHouseApi());
        $this->setCategoryApi(new WareHouseGroupApi(), t('группу складов'));
    }

    /**
     * Отображение списка
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if ($this->group > 0 && !$this->getCategoryApi()->getById($this->group)) {
            $this->group = 0;
        }
        if ($this->group > 0) {
            $this->api->setFilter('group_id', $this->group);
        }
        return parent::actionIndex();
    }

    /**
     * Вызывается перед действием Index и возвращает коллекцию элементов,
     * которые будут находиться на экране.
     *
     * @return CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $this->group = $this->url->request('group', TYPE_STRING);
        $helper->setTopHelp(t('На этой вкладке вы можете задать список складов, а также настроить их географическое положение, время работы и другие параметры. В некоторых случаях, складами могут выступать ваши магазины(торговые точки). Создав в данном разделе склады, вы сможе указывать остатки каждого товара (во вкладке Комплектации) на любом из ваших складов. Остатки на складах могут отображаться всем пользователям в карточке товара в виде условных рисок.'));
        $helper->setTopTitle(t('Склады'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить')]));
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->addCsvButton('catalog-warehouse');
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Полное название'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'Sortable' => SORTABLE_BOTH, 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('alias', t('URL имя склада'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\StrYesno('default_house', t('Склад по умолчанию?')),
                new TableType\StrYesno('public', t('Публичный')),
                new TableType\StrYesno('checkout_public', t('Пункт самовывоза')),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать склад'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ],
                        ],
                        [
                            'title' => t('установить по умолчанию'),
                            'attr' => [
                                '@data-url' => $this->router->getAdminPattern('setDefaultWareHouse', [':id' => '@id']),
                                'class' => 'crud-get'
                            ],
                        ],
                    ]),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('title', t('Полное наименование'), ['SearchType' => '%like%']),
                        new Filter\Type\Text('alias', t('URL имя склада'), ['SearchType' => '%like%']),
                    ]]),
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        $helper->setCategory(new Category\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->group,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'noExpandCollapseButton' => true,
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('categoryMove'),
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':group' => '@id'])]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('categoryEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id',
                    ],
                ]),
            ]),
            'headButtons' => [
                [
                    'attr' => [
                        'title' => t('Создать группу складов'),
                        'href' => $this->router->getAdminUrl('categoryAdd'),
                        'class' => 'add crud-add',
                    ],
                ],
            ],
        ]), $this->getCategoryApi());

        $helper->setCategoryBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Delete(null, null, ['attr' => [
                    'data-url' => $this->router->getAdminUrl('categoryDel'),
                ]]),
            ],
        ]));

        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));

        $helper->viewAsTableCategory();

        return $helper;
    }

    /**
     * AJAX
     */
    public function actionSetDefaultWareHouse()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->api->setDefaultWareHouse($id);
        return $this->result->setSuccess(true)->getOutput();
    }

    /**
     * Метод для клонирования
     *
     * @return Standard
     * @throws \RS\Controller\ExceptionPageNotFound
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
            unset($elem['xml_id']);
            $elem['default_house'] = 0;
            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }
}
