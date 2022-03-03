<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;

class BlockedIp extends \RS\Controller\Admin\Crud
{
    protected 
        $api;

    function __construct()
    {
        parent::__construct(new \Main\Model\BlockedIpApi());
    }
    
    function helperIndex()
    {  
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Заблокированные IP'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить IP')]));
        $helper->addCsvButton('main-blockedip');
        $helper->setTopHelp(t('В этом разделе вы можете заблокировать IP адреса с которых идет вредоносный трафик. Обработка запросов с данных IP будет прекращена на самом начальном этапе выполнения скрипта, до инициализации основных подсистем.'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                    new TableType\Checkbox('ip', ['showSelectAll' => true]),
                    new TableType\Text('ip', t('IP-адрес'), ['Sortable' => SORTABLE_BOTH,
                                                                 'href' => $this->router->getAdminPattern('edit', [':id' => '@ip']),
                                                                 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Userfunc('expire', t('Заблокирован до'), 
                        function($value, $type) {
                            return $value ? date('d.m.Y H:i', strtotime($value)) : t('бессрочно');
                        }, 
                        ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('comment', t('Комментарий'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Actions('ip', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
            ],
        ]));
        
        
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                                            new Filter\Type\Text('ip', t('IP-адрес'), ['SearchType' => '%like%']),
                                                            new Filter\Type\DateRange('expire', t('Дата разблокировки')),
                                                            new Filter\Type\Text('comment', t('Комментарий'), ['SearchType' => '%like%']),
                                    ]
                                    ])
                                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));
        
        return $helper;
    }    
    
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать {ip}') : t('Добавить IP'));
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
}


