<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Table,
    \RS\Html\Filter;

class LogCtrl extends \RS\Controller\Admin\Crud
{
    protected
        $api;
    
    function __construct()
    {
        parent::__construct(new \ExternalApi\Model\LogApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Журнал запросов к внешним API'));
        $helper->setTopToolbar($this->buttons([]));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('dateof', t('Дата'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC, 'LinkAttr' => [
                    'class' => 'crud-edit'
                ],
                'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),]),
                new TableType\Text('ip', t('IP'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Userfunc('user_id', t('Пользователь'), function($value, $cell) {
                    if ($value) {
                        $user = new \Users\Model\Orm\User($value);
                        return $user->getFio()."($value)";
                    }
                }),                
                new TableType\Text('method', t('Метод API'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('error_code', t('Код ошибки'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('request_uri', t('URL запроса'), ['hidden' => true]),
                new TableType\Text('client_id', t('ID приложения'), ['hidden' => true]),
                new TableType\Text('token', t('Авторизационный токен'), ['hidden' => true]),
                new TableType\Actions('id', [
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
                                                            new Filter\Type\Text('method', t('Метод API'), ['searchType' => '%like%']),
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
    
    function helperAdd()
    {
        $helper = parent::helperAdd();
        $helper->setBottomToolbar($this->buttons(['cancel']));
        return $helper;
    }

    /**
     * Очистка лога журнали API запросов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionClearLog()
    {
        $api = new \ExternalApi\Model\LogApi();
        $api->clearLog();
        return $this->result->setSuccess(true)->addMessage(t('Лог очищен'));
    }
    
}


