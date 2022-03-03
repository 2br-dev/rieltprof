<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table,
    \Users\Model\Orm\User;
use RS\AccessControl\Rights;
use Users\Config\ModuleRights;
use Users\Model\FilterType as CustomFilter;

/**
* Контроллр пользователей
*/
class Ctrl extends \RS\Controller\Admin\Crud
{
        
    function __construct()
    {
        parent::__construct(new \Users\Model\Api());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('Здесь представлены пользователи вашего интернет-магазина (покупатели и администраторы). Разграничивайте права пользователей, с помощью присвоения им необходимых групп. В данном разделе вы можете редактировать любые сведения пользователей, устанавливать им необходимый тип цен, при необходимости блокировать, менять пароль и т.д.'));
        $helper->setTopTitle(t('Пользователи'));
        $edit_pattern = $this->router->getAdminPattern('edit', [':id' => '@id']);
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Text('login', t('Логин'), ['href' => $edit_pattern, 'Sortable' => SORTABLE_BOTH, 'linkAttr' => ['class' => 'crud-edit'], 'hidden' => true]),
                new TableType\Text('e_mail', t('E-mail'), ['href' => $edit_pattern, 'Sortable' => SORTABLE_BOTH, 'linkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('surname', t('Фамилия'), ['href' => $edit_pattern, 'Sortable' => SORTABLE_BOTH, 'linkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('name', t('Имя'), ['href' => $edit_pattern, 'Sortable' => SORTABLE_BOTH, 'linkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('midname', t('Отчество'), ['href' => $edit_pattern, 'Sortable' => SORTABLE_BOTH, 'linkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('company', t('Компания'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('company_inn', t('ИНН'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('phone', t('Телефон'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),

                new TableType\Datetime('dateofreg', t('Дата регистрации'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Datetime('last_visit', t('Последний визит'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('group', t('Группа'), '%users%/form/filter/group.tpl'),
                new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                        new TableType\Action\DropDown([
                            [
                                'title' => t('клонировать пользователя'),
                                'attr' => [
                                    'class' => 'crud-add',
                                    '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                ]
                            ],
                            [
                                'title' => t('авторизоваться'),
                                'attr' => [
                                    'class' => 'crud-get',
                                    '@href' => $this->router->getAdminPattern('AuthAsUser', [':user_id' => '~field~'], 'users-ctrl'),
                                    'target' => '_blank',
                                ]
                            ],
                            [
                                'title' => t('заказы пользователя'),
                                'attr' => [
                                    '@href' => $this->router->getAdminPattern(false, [':f[user_id]' => '~field~'], 'shop-orderctrl'),
                                ]
                            ],
                        ])
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]]));
        
        $group_api  = new \Users\Model\GroupApi();
        $group_list = ['' => t('Любой')] + ['NULL' => t('Без группы')] + array_diff_key($group_api->getSelectList(), ['guest', 'clients']);

        $is_catalog_exists = \RS\Module\Manager::staticModuleExists('catalog');
        
        if ($is_catalog_exists) {
            $filter_by_cost = [new Filter\Type\Select('typecost', t('Тип цен'), [
                    '' => t('- Любой -'),
                    '0' => t('- По умолчанию -')]
                + \Catalog\Model\CostApi::staticSelectList())];
        } else {
            $filter_by_cost = [];
        }

        
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                                            new Filter\Type\Text('id', t('№')),
                                                            new Filter\Type\Text('e_mail',t('E-mail'), ['SearchType' => '%like%']),
                                                            new Filter\Type\Text('surname',t('Фамилия'), ['SearchType' => '%like%']),
                                                            new Filter\Type\Text('phone',t('Телефон'), ['SearchType' => '%like%']),
                                                            new Filter\Type\Text('company',t('Компания'), ['SearchType' => '%like%']),
                                                            new Filter\Type\Text('company_inn',t('ИНН'), ['SearchType' => '%like%']),
                                                            new CustomFilter\Doubles('doubles',t('Поиск дублей по полю')),
                                    ]
                                    ])
                                ],
                                'SecContainers' => [
                                    new Filter\Seccontainer( [
                                        'Lines' => [
                                            new Filter\Line( ['items' => [
                                                                    new Filter\Type\Text('login', t('Логин')),
                                                                    new Filter\Type\Text('name', t('Имя'), ['SearchType' => '%like%']),
                                                                    new Filter\Type\Text('midname', t('Отчество'), ['SearchType' => '%like%']),
                                                                    new Filter\Type\Date('dateofreg_from', t('Дата регистрации, от')),
                                                                    new Filter\Type\Date('dateofreg_to', t('Дата регистрации, до')),
                                            ]
                                            ]),
                                            new Filter\Line( ['items' => array_merge([
                                                                    new Filter\Type\Select('group', t('Группа'), $group_list),

                                            ], $filter_by_cost)
                                            ])
                                        ]])
                                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
            'beforeSqlWhere' => [$this->api, 'beforeSqlWhereCallback']
        ]));
        
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Add($this->router->getAdminUrl('add'),t('добавить'), [
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
                            'title' => t('Экспорт пользователей в CSV'),
                            'attr' => [
                                'href' => \RS\Router\Manager::obj()->getAdminUrl('exportCsv', ['schema' => 'users-users', 'referer' => $this->url->selfUri()], 'main-csv'),
                                'class' => 'crud-add'
                            ]
                        ],
                        [
                            'title' => t('Импорт пользователей из CSV'),
                            'attr' => [
                                'href' => \RS\Router\Manager::obj()->getAdminUrl('importCsv', ['schema' => 'users-users', 'referer' => $this->url->selfUri()], 'main-csv'),
                                'class' => 'crud-add'
                            ]
                        ],
                ]),
            ]
        ]));
        
        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                $this->buttons('multiedit'),
                new Toolbar\Button\Button(null, t('Сбросить пароли'), [
                    'attr' => [
                        'class' => 'crud-post',
                        'data-url' => $this->router->getAdminUrl('generatePassword'),
                        'data-confirm-text' => t('Вы действительно хотите сгенерировать новые пароли для выбранных пользователей и отправить их пользователям на почту?')
                    ]
                ]),
                $this->buttons('delete'),
            ]
        ]));
        
        return $helper;
    }
    
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null) 
    {
        $conf_userfields = \RS\Config\Loader::byModule($this)->getUserFieldsManager();
        $conf_userfields->setErrorPrefix('userfield_');
        $conf_userfields->setArrayWrapper('data');
        
        $elem = $this->api->getElement();
                
        $groups = new \Users\Model\Groupapi();
        $glist = $groups->getList(0,0,'name');
        
        $conf_userfields->setValues($elem['data']);
        $elem['conf_userfields'] = $conf_userfields;
        $elem['groups'] = $glist;
        if (isset($primaryKeyValue) && $primaryKeyValue>0){
            $elem['usergroup'] = $this->api->getElement()->getUserGroups();
        }elseif (!isset($primaryKeyValue)){
            $elem['usergroup'] = [];
        }

        //Отключаем автозаполнение полей в Chrome
        $autocomplete_off = [
            'autocomplete' => 'new-password'
        ];
        $elem['__e_mail']->setAttr($autocomplete_off);
        $elem['__login']->setAttr($autocomplete_off);
        $elem['__openpass']->setAttr($autocomplete_off);
        $elem['__openpass']->setTemplate('%users%/form/user/chpass.tpl');
        $elem['__phone']->setEnableVerification(false);

        if ($primaryKeyValue) {
            $elem['full_fio'] = $elem->getFio();
            $this->getHelper()->setTopTitle(t('Редактировать профиль пользователя {full_fio}'));
        } else {
            $this->getHelper()->setTopTitle(t('Добавить пользователя'));
        }

        $return = parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);

        $conf_userfields->setValues($elem['data']);
        
        return $return;
    }
    
    
    /**
    * Метод для клонирования
    * 
    */ 
    function actionClone()
    {
        $this->setHelper( $this->helperAdd() );
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
    
    function actionGeneratePassword()
    {
        $ids = $this->modifySelectAll( $this->url->request('chk', TYPE_ARRAY, [], false) );
        $this->result->setSuccess($this->api->generatePasswords($ids));
        
        if ($this->result->isSuccess()) {
            $this->result->addMessage(t('Пароли успешно сброшены'));
        } else {
            $this->result->addEMessage($this->api->getErrorsStr());
        }
        
        return $this->result;
    }
    
    /**
    * Вызывает окно мультиредактирования
    */
    function actionMultiedit()
    {
        $elem = $this->api->getElement();
        $group_api = new \Users\Model\GroupApi();
        $group_list = $group_api->getList(0, 0, 'name');
        $elem['groups'] = $group_list;
        
        return parent::actionMultiEdit();
    }

    /**
     *  Позволяет авторизоваться под выбранным пользователем
     */
    function actionAuthAsUser()
    {
        $user_id = $this->url->get('user_id', TYPE_INTEGER, 0);
        $login_user = new User($user_id);
        
        if (!$login_user['id']) {
            return $this->result->addEMessage(t('Пользователь не найден'));
        }
        
        //Проверим права у группы пользователей
        if ($acl_err = Rights::CheckRightError($this, ModuleRights::RIGHT_LOGIN_AS_USER)) {
            return $this->result->addEMessage($acl_err);
        }
        
        //Запретим не супервизорам авторизовываться под супервизорами
        if (!$this->user->isSupervisor() && $login_user->isSupervisor()) {
            return $this->result->addEMessage(t('Только супервизор может авторизоваться под другим супервизором'));
        }
        
        $site = \RS\Site\Manager::getSite();    
        if ($site) {
            $redirect_url = $site->getRootUrl(true);
            \RS\Application\Auth::logout();
            \RS\Application\Auth::setCurrentUser(new \Users\Model\Orm\User($user_id));
            
            return $this->result
                            ->addSection('noUpdate', true)
                            ->setAjaxWindowRedirect($redirect_url);
        }
    }
}

