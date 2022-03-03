<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Controller\Admin;

use Menu\Model\Api as MenuApi;
use Menu\Model\Orm\Menu;
use RS\Application\Application;
use RS\Controller\Admin\Crud;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Tree;
use RS\Module\Item as ModuleItem;

/**
 * Контроллер меню в админки
 */
class Ctrl extends Crud
{
    /** @var MenuApi */
    protected $api;
    protected $user_menu_type = 'user';

    function __construct($param = [])
    {
        parent::__construct(new MenuApi());
        $this->setTreeApi($this->api);
        $this->api->setFilter('menutype', $this->user_menu_type);

        $this->app->addCss($this->mod_css . 'menucontrol.css', null, BP_ROOT);

        $this->multiedit_check_func = [$this->api, 'checkParent'];
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В этом разделе можно создавать различные текстовые и информационные страницы, которые, при необходимости, могут отображаться в меню на сайте.
                                Каждая страница может иметь необходимый URL-адрес. Данным разделом следует также воспользоваться,
                                если вы желаете сконструировать на вашем сайте отдельную страницу с собственным набором модулей.
                                Для этого, создайте здесь страницу, укажите у неё тип "Страница" и настройте затем её в разделе <i>Веб-сайт &rarr; Конструктор сайта</i>.'));
        $helper->setTopTitle(t('Меню'));
        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Dropdown([
                    [
                        'title' => t('добавить пункт меню'),
                        'attr' => [
                            'class' => 'btn-success crud-add',
                            'href' => $this->router->getAdminUrl('add')
                        ]
                    ],
                    [
                        'title' => t('добавить разделитель'),
                        'attr' => [
                            'class' => 'crud-add',
                            'href' => $this->router->getAdminUrl('add', ['sep' => 1])
                        ]
                    ]
                ]),
            ]
        ]));
        $helper->addCsvButton('menu-menu');

        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->viewAsTree();
        $helper->setTree($this->getIndexTreeElement());

        return $helper;
    }

    protected function getIndexTreeElement()
    {
        $tree = new Tree\Element([
            'disabledField' => 'public',
            'disabledValue' => '0',
            'activeField' => 'id',
            'sortIdField' => 'id',
            'hideFullValue' => true,
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('treeMove'),
            'mainColumn' => new TableType\Usertpl('title', t('Название'), '%menu%/tree_column.tpl'),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id'
                    ]
                ]),
                new TableType\Action\DropDown([
                    [
                        'title' => t('Редактировать мета-теги'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('edit', [':id' => 'menu.item_{id}', 'create' => 1], 'pageseo-ctrl'),
                            'class' => 'crud-add'
                        ]
                    ],
                    [
                        'title' => t('Клонировать'),
                        'attr' => [
                            'class' => 'crud-add',
                            '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                        ]
                    ],
                    [
                        'title' => t('Добавить дочерний элемент'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('add', [':pid' => '@id']),
                            'class' => 'crud-add'
                        ]
                    ],
                    [
                        'title' => t('Добавить разделитель'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('add', [':pid' => '@id', 'sep' => 1]),
                            'class' => 'crud-add'
                        ]
                    ],
                    [
                        'title' => t('Показать на сайте'),
                        'attr' => [
                            '@href' => $this->router->getAdminPattern('itemRedirect', [':id' => '@id']),
                            'target' => '_blank'
                        ]
                    ]
                ]),
            ]),
        ]);

        return $tree;
    }

    /**
     * Делает редирект на соотвествующий url на сайте
     * Через Request передаётся id пункта меню
     *
     */
    function actionItemRedirect()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        /** @var Menu $menu */
        $menu = $this->api->getOneItem($id);

        if ($menu) {
            Application::getInstance()->redirect($menu->getHref());
        }
    }

    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($this->url->isPost()) {
            $this->user_post_data = ['menutype' => $this->user_menu_type];
        }

        $parent = $this->url->get('pid', TYPE_INTEGER, null);
        $obj = $this->api->getElement();

        if ($parent) {
            $obj['parent'] = $parent;
        }

        $title = $obj['id'] ? t('Редактировать меню ') . '{title}' : t('Добавить меню');

        $obj['tpl_module_folders'] = ModuleItem::getResourceFolders('templates');
        $this->getHelper()->setTopTitle($title);

        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    function successSave()
    {
        $obj = $this->api->getElement();
        Application::getInstance()->redirect($this->url->replaceKey([$this->action_var => '', 'pid' => $obj['parent']]));
    }

    function actionGetMenuTypeForm()
    {
        $type = $this->url->request('type', TYPE_STRING);
        $types = $this->api->getMenuTypes();

        if (isset($types[$type])) {
            $this->view->assign([
                'changeType' => true,
                'type_object' => $types[$type]
            ]);
            $this->result->setTemplate('form/menu/type_form.tpl');
        }
        return $this->result;
    }
}
