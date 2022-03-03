<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\CallHistoryApi;
use Crm\Model\FilterType\CallNumber;
use Crm\Model\FilterType\CallNumberAdmin;
use Crm\Model\FilterType\CallNumberClient;
use Crm\Model\FilterType\NotEmptyString;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Element as ToolbarElement,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Filter,
    \RS\Html\Table;

class CallHistoryCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new CallHistoryApi());
        $this->getApi()->initRightsFilters();

        $this->setCrudActions([
            'index',
            'edit',
            'del',
            'tableOptions'
        ]);
    }

    public function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle(t('Звонки')); //Установим заголовок раздела
        $helper->setTopToolbar(new ToolbarElement([
            'Items' => [
                new ToolbarButton\Button(
                    \RS\Router\Manager::obj()->getAdminUrl('exportCsv', ['schema' => 'crm-callhistory', 'referer' => $this->url->selfUri()], 'main-csv'),
                    t('Экспорт CSV'),
                    [
                        'attr' => [
                            'class' => 'crud-add'
                        ]
                    ]
                )
            ]
        ]));
        $helper->setTopHelp(t('В этом разделе отображает история звонков. Подключите провайдера телефонии, чтобы видеть входящие, производить исходящие звонки прямо в административной панели'));
        $helper->setBottomToolbar($this->buttons(['delete']));

        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Usertpl('call_flow', t('Номер'), '%crm%/telephony/tablecolumn/number.tpl', ['Sortable' => SORTABLE_BOTH, 'LinkAttr' => [
                    'class' => 'crud-edit'
                ],
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id'])]),

                new TableType\Usertpl('caller_number', t('Пользователь'), '%crm%/telephony/tablecolumn/user.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('call_flow', t('Направление'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('call_status', t('Статус вызова'), '%crm%/telephony/tablecolumn/status.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Datetime('event_time', t('Время звонка'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('duration', t('Продолжительность'), '%crm%/telephony/tablecolumn/duration.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('called_number', t('Администратор'), '%crm%/telephony/tablecolumn/admin.tpl', ['Sortable' => SORTABLE_BOTH]),
                new TableType\Usertpl('provider', t('Провайдер'), '%crm%/telephony/tablecolumn/provider.tpl', ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Usertpl('record_id', t('Запись'), '%crm%/telephony/tablecolumn/record.tpl', ['tdAttr' => ['align' => 'center']]),
                new TableType\Text('id', t('№'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC, 'TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), t('Подробности')),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]]));


        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new CallNumber('call_number', t('Номер телефона')),
                        new CallNumberClient('user', t('Пользователь')),
                        new CallNumberAdmin('admin', t('Администратор')),
                        new Filter\Type\DateRange('event_time', t('Дата звонка')),
                        new Filter\Type\Select('call_flow', t('Направление звонка'), [
                            '' => t('Не важно'),
                            \Crm\Model\Orm\Telephony\CallHistory::CALL_FLOW_IN => t('Входящий'),
                            \Crm\Model\Orm\Telephony\CallHistory::CALL_FLOW_OUT => t('Исходящий'),
                        ]),
                        new Filter\Type\Select('call_status', t('Статус звонка'), [
                            '' => t('Не важно'),
                            ] + \Crm\Model\Orm\Telephony\CallHistory::getCallStatuses()),

                        new Filter\Type\Select('call_sub_status', t('Статус завершения звонка'), [
                                '' => t('Не важно'),
                            ] + \Crm\Model\Orm\Telephony\CallHistory::getCallSubStatuses()),

                        new NotEmptyString('record_id', t('Запись'))
                    ]
                    ])
                ],
            ])
        ]));

        //Подключаем плагин для кругового проигрывания записей
        $this->app
            ->addCss('%crm%/360player.css')
            ->addJs('%crm%/360player/soundmanager2.js')
            ->addJs('%crm%/360player/berniecode-animator.js')
            ->addJs('%crm%/360player/360player.js');

        return $helper;
    }

    function helperEdit()
    {
        $helper = new CrudCollection($this, $this->getApi());
        $helper->viewAsForm();
        $helper->setBottomToolbar($this->buttons(['cancel']));

        return $helper;
    }

    function actionEdit()
    {
        $id = $this->url->get('id', TYPE_STRING);

        //Загружаем объект с учетом фильтров, установленных для списков
        $this->api->setFilter($this->api->getIdField(), $id);
        if ($call_history = $this->api->getFirst()) {
            $this->api->setElement($call_history);
        } else {
            $this->e404();
        }

        $this->view->assign([
            'call_history' => $call_history
        ]);

        $helper = $this->getHelper();
        $helper->setTopTitle(t('Звонок №{id}'));
        $helper->setForm($this->view->fetch('telephony/show_call.tpl'));

        return $this->result->setTemplate( $helper->getTemplate() );
    }
}