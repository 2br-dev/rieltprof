<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;

use Catalog\Model\CurrencyApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Helper\CustomView;
use RS\Html\Table\Type as TableType;
use RS\Html\Table;
use RS\Html\Toolbar;
use Shop\Model\Orm\Transaction;
use Shop\Model\ShipmentApi;

/**
 * Контроллер Управление налогами
 */
class OrderShipmentCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new ShipmentApi());
        $this->setCrudActions('index', 'edit', 'del', 'tableOptions');
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Отгрузки заказов'));
        $helper->setTopHelp(t('В данном разделе можно просматривать созданные документы отгрузки заказов. Перейдя к транзакции, можно отправить чек отгрузки в ОФД. Также в настройках модуля можно настроить автоматическую отправку чека при создании документа отгрузки.'));
        $helper->setTopToolbar(new Toolbar\Element([]));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('id', t('№'), [
                    'Sortable' => SORTABLE_BOTH,
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                    'TdAttr' => [
                        'class' => 'cell-sgray',
                        'width' => '50',
                    ],
                ]),
                new TableType\Text('info_order_num', t('Номер заказа'), [
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                ]),
                new TableType\Datetime('date', t('Дата создания'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Userfunc('info_total_sum', t('Сумма отгрузки'), function ($value) {
                    return CustomView::cost($value, CurrencyApi::getBaseCurrency()['stitle']);
                }),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Перейти к транзакциям'),
                            'attr' => [
                                'target' => '_blank',
                                '@href' => $this->router->getAdminPattern('index', [
                                    'f[entity][type]' => Transaction::ENTITY_SHIPMENT,
                                    ':f[entity][id]' => '@id',
                                ], 'shop-transactionctrl'),
                            ]
                        ],
                    ])
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        return $helper;
    }

    /**
     * Подготавливает Helper объекта для редактирования
     *
     * @return CrudCollection
     */
    protected function helperEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);
        $shipment = $this->api->getElement();
        $shipment->load($id);

        $helper = $this->helperAdd();
        $helper->setBottomToolbar($this->buttons(['cancel']));
        $helper->setTopTitle(t('Отгрузка заказа №%0 (от %1)', [
            $shipment['info_order_num'],
            date('d.m.Y H:i' ,strtotime($shipment['date']))
        ]));

        return $helper;
    }
}
