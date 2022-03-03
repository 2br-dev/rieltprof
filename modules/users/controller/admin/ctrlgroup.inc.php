<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;

use Menu\Model\Api as MenuApi;
use RS\Controller\Admin\Crud;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Tree\Element as TreeElement;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use Users\Model\GroupApi;
use Users\Model\Orm\UserGroup;

class CtrlGroup extends Crud
{
    /** @var GroupApi */
    protected $api;
    protected $module_access = [];
    protected $menu_access = [];
    protected $site_access;
    protected $form_tpl = 'group_form.tpl';

    function __construct()
    {
        parent::__construct(new GroupApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Группы позволяют объединить пользователей по какому-либо признаку. Группе пользователей можно ограничить или делегировать определенный набор прав. Права пользователей суммируюся, если пользователь состоит одновременно в нескольких группах. В ReadyScript существует 2 системные зарезервированные группы: Гости, Клиенты. Гости - это абсолютно все пользователи, в том числе неавторизованные. Группа Клиенты присваивается всем пользователям на время, пока пользователь авторизован на сайте.'));
        $helper->setTopTitle(t('Группы пользователей'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('alias'),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'alias', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                new TableType\Text('name', t('Название'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@alias']), 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('description', t('Описание')),
                new TableType\Text('alias', t('Псевдоним'), ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_admin', t('Админ')),
                new TableType\Actions('alias', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, ['disableAjax' => true]),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move'),
            ],
        ]));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Add($this->router->getAdminUrl('add'), t('добавить'), [
                    [
                        'attr' => [
                            'class' => 'btn-success crud-add'
                        ]
                    ],
                ]),
                new ToolbarButton\Dropdown([
                    [
                        'title' => t('Импорт/Экспорт'),
                        'attr' => [
                            'class' => 'button',
                            'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                        ]
                    ],
                    [
                        'title' => t('Экспорт групп пользователей в CSV'),
                        'attr' => [
                            'href' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'users-usersgroup', 'referer' => $this->url->selfUri()], 'main-csv'),
                            'class' => 'crud-add'
                        ]
                    ],
                    [
                        'title' => t('Импорт групп пользователей из CSV'),
                        'attr' => [
                            'href' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'users-usersgroup', 'referer' => $this->url->selfUri()], 'main-csv'),
                            'class' => 'crud-add'
                        ]
                    ],
                ]),
            ]
        ]));

        return $helper;
    }

    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $helper = $this->getHelper();

        if ($primaryKeyValue) {
            $helper->setTopTitle(t('Редактирование группы {name}'));
        } else {
            $helper->setTopTitle(t('Добавить группу'));
        }

        //Если пост идет для текущего модуля
        if ($this->url->isPost()) {
            $this->user_post_data['menu_access'] = $this->request('menu_access', TYPE_ARRAY);
            $this->user_post_data['menu_admin_access'] = $this->request('menu_admin_access', TYPE_ARRAY);
            $this->user_post_data['module_access'] = $this->request('module_access', TYPE_ARRAY);
            $this->user_post_data['site_access'] = $this->url->request('site_access', TYPE_INTEGER, 0);

            $this->result->setSuccess($this->api->save($primaryKeyValue, $this->user_post_data));

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($this->api->getElement()->getDisplayErrors());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                }
                return $this->result->getOutput();
            }

            if ($this->result->isSuccess()) {
                $this->successSave();
            } else {
                $this->module_access = $this->user_post_data['module_access'];
                $this->menu_access = array_flip($this->user_post_data['menu_access']);
                $this->site_access = $this->user_post_data['site_access'];
            }
        }

        //Формируем доступное меню
        $user_menu_api = new MenuApi();
        $user_menu_api->uniq = 'usermenu';
        $user_menu_api->setCheckAccess(false);
        $user_menu_api->setFilter('menutype', 'user');

        $admin_menu_api = new MenuApi();
        $admin_menu_api->uniq = 'adminmenu';
        $admin_menu_api->setCheckAccess(false);

        $admin_tree = new TreeElement([
            'checked' => $this->menu_access,
            'uniq' => $admin_menu_api->uniq,
            'sortIdField' => 'alias',
            'activeField' => 'alias',
            'mainColumn' => new TableType\Text('title', t('Название'), []),
            'checkboxName' => 'menu_admin_access[]'
        ]);
        $user_tree = clone $admin_tree;
        $user_tree->setCheckboxName('menu_access[]');
        $user_tree->setSortIdField('id');
        $user_tree->setActiveField('id');
        $user_tree->setOption('uniq', $user_menu_api->uniq);

        $user_tree->setData($user_menu_api->getTreeList(0));
        $admin_tree->setData($admin_menu_api->getAdminMenu(false));

        // Готовим массив с данными для таблицы прав к модулям
        $list_fot_table = $this->api->prepareModuleAccessData($this->module_access);

        $this->view->assign([
            'admin_tree' => $admin_tree,
            'user_tree' => $user_tree,
            'elem' => $this->api->getElement(),
            'site_access' => $this->site_access,
            'menu_access' => $this->menu_access,
            'module_access' => $this->module_access,
            'module_list' => $list_fot_table,
        ]);

        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }

    /**
     * Редактирование элемента
     */
    function actionEdit()
    {
        $id = $this->url->get('id', TYPE_STRING, 0);
        if ($id) $this->api->getElement()->load($id);

        /** @var UserGroup $obj */
        $obj = $this->api->getElement();
        $obj['__alias']->setReadOnly();
        $obj['__alias']->setHint(t('Данное поле является идентификатором и не доступно для редактирования'));

        //Загружаем сведения о правах доступа
        $this->module_access = $obj->getModuleAccess();
        $this->menu_access = array_flip($obj->getMenuAccess());
        $this->site_access = $obj->getSiteAccess(SiteManager::getSiteId());

        return $this->actionAdd($id);
    }

    /**
     * AJAX перемещение элементов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_STRING);
        $to = $this->url->request('to', TYPE_STRING);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }
}
