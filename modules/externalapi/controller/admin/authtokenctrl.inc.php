<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Table,
    \RS\Html\Filter;

class AuthTokenCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $api;
    
    function __construct()
    {
        $this->setCrudActions([
            'index',
            'add',
            'edit',
            'del'
        ]);
        parent::__construct(new \ExternalApi\Model\TokenApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Журнал авторизационных токенов'));
        $helper->setTopToolbar($this->buttons(['add']));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('token', ['showSelectAll' => true]),
                new TableType\Datetime('dateofcreate', t('Дата'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC, 'LinkAttr' => [
                    'class' => 'crud-edit'
                ],
                'href' => $this->router->getAdminPattern('edit', [':id' => '@token']),]),
                new TableType\Text('token', t('Авторизационный токен')),                
                new TableType\Text('ip', t('IP'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Userfunc('user_id', t('Пользователь'), function($value, $cell) {
                    if ($value) {
                        $user = new \Users\Model\Orm\User($value);
                        return $user->getFio()."($value)";
                    }
                }),                
                new TableType\Text('app_type', t('Тип приложения'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Datetime('expire', t('Дата истечения'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('token', [
                                new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]]));
        
        $helper->setFilter(new Filter\Control( [
            'container' => new Filter\Container( [
                                'lines' =>  [
                                    new Filter\Line( ['items' => [
                                                            new Filter\Type\DateRange('dateof', t('Дата')),
                                                            new Filter\Type\Text('ip', t('IP')),
                                                            new Filter\Type\User('user_id', t('Пользователь')),
                                                            new Filter\Type\Text('token', t('Токен'), ['searchType' => '%like%']),
                                    ]
                                    ]),
                                ],
                                'SecContainers' => [
                                    new Filter\Seccontainer( [
                                    'lines' => [
                                        new Filter\Line( ['items' => [
                                                            new Filter\Type\Text('request_uri', t('URL запроса'), ['searchType' => '%like%']),
                                                            new Filter\Type\Text('error_code', t('Код ошибки')),
                                        ]])
                                    ]
                                    ])],
            ]),
            
            'field_prefix' => $this->api->getElementClass()
        ]));
        
        $helper->setBottomToolbar($this->buttons(['delete']));
        return $helper;
    }

    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if (!$primaryKeyValue) {
            $element = $this->getApi()->getElement();
            $element['token'] = $element->generateToken();
            $element['user_id'] = $this->user['id'];
            $element['ip'] = $this->url->server('REMOTE_ADDR');
            $element['dateofcreate'] = date('Y-m-d H:i:s');
            $element['expire'] = time() + 60*60*24*265;
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
    
}