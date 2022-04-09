<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Crud;
use RS\Controller\Result\Standard;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Filter;
use RS\Html\Table;
use Shop\Model\ArchiveOrderApi;
use Shop\Model\DeliveryApi;
use Shop\Model\HtmlFilterType as ShopHtmlFilterType;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;

/**
 * Контроллер Управление заказами
 */
class ArchiveOrderCtrl extends Crud
{
    /** @var ArchiveOrderApi */
    protected $api;

    function __construct()
    {
        parent::__construct(new ArchiveOrderApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();

        $helper
            ->setTopHelp(t('Здесь отображаются заказы, перенесённые в архив.
                            Заказы в архиве нельзя редактировать, их можно только удалить или перенести обратно в список заказов.'))
            ->viewAsTable()
            ->setTopTitle(t('Архив заказов'))
            ->setTable(new Table\Element([
                'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Text('order_num', t('Номер'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('user_id', t('Покупатель'), '%shop%/admin/order_user_cell.tpl'),
                    new TableType\Usertpl('status', t('Статус'), '%shop%/admin/order_status_cell.tpl', ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('dateof', t('Дата оформления'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                    new TableType\Datetime('dateofupdate', t('Дата обновления'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Usertpl('totalcost', t('Сумма'), '%shop%/admin/order_totalcost_cell.tpl', ['Sortable' => SORTABLE_BOTH]),
                    new TableType\StrYesno('is_payed', t('Оплачен'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Userfunc('user_phone', t('Телефон покупателя'), function ($user_phone, $_this) {
                        /**
                         * @var TableType\AbstractType $_this
                         * @var Order $order
                         */
                        $order = $_this->getRow();
                        $user = $order->getUser(); //Пользователь совершивший покупку
                        return $user['phone'];
                    }, ['hidden' => true]),
                    new TableType\Text('payment', t('Способ оплаты'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Text('delivery', t('Способ Доставки'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Text('track_number', t('Трек-номер'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\StrYesno('is_mobile_checkout', t('Заказ через моб.приложение'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Userfunc('e_mail', t('Email'), function ($user_mail, $_this) {
                        /**
                         * @var TableType\AbstractType $_this
                         * @var Order $order
                         */
                        $order = $_this->getRow();
                        $user = $order->getUser(); //Пользователь совершивший покупку
                        return $user['e_mail'];
                    }, ['hidden' => true]),
                    new TableType\Text('manager_user_id', t('Менеджер заказа'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),

                    new TableType\Actions('id', [], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
                ]
            ]));

        $helper->setTopToolbar();

        $payments = ['' => t('Любая')] + PaymentApi::staticSelectList();
        $deliveries = ['' => t('Любая')] + DeliveryApi::staticSelectList();

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Button(null, t('разархивировать'), [
                    'attr' => [
                        'data-url' => $this->router->getAdminUrl('unarchiveOrders'),
                        'class' => 'btn-alt btn-warning crud-post',
                    ],
                ]),
                new ToolbarButton\Delete(null, null, [
                    'attr' => [
                        'data-url' => $this->router->getAdminUrl('del'),
                    ],
                ]),
            ]
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('order_num', '№'),
                        new Filter\Type\DateRange('dateof', t('Дата оформления')),
                        new Filter\Type\Text('totalcost', t('Сумма'), ['showtype' => true])
                    ]]),
                ],
                'SecContainer' => new Filter\Seccontainer([
                    'Lines' => [
                        new Filter\Line(['Items' => [
                            new Filter\Type\User('user_id', t('Пользователь')),
                            //Поиск по добавленной таблице с пользователями
                            new ShopHtmlFilterType\UserFIO('user_fio', t('ФИО пользователя'), ['searchType' => '%like%']),
                            //Поиск по добавленной таблице с товарами заказа
                            new ShopHtmlFilterType\Product('PRODUCT.title', t('Наименование товара'), ['searchType' => '%like%']),
                            new ShopHtmlFilterType\Product('PRODUCT.barcode', t('Артикул товара'), ['searchType' => '%like%']),
                            //Поиск по добавленной таблице с пользователями
                            new ShopHtmlFilterType\UserPhone('USER.phone', t('Телефон пользователя'), ['searchType' => '%like%']),
                        ]]),
                        new Filter\Line(['Items' => [
                            new Filter\Type\User('manager_user_id', t('Менеджер')),
                            new Filter\Type\Select('payment', t('Оплата'), $payments),
                            new Filter\Type\Select('delivery', t('Доставка'), $deliveries),
                            new Filter\Type\Select('is_mobile_checkout', t('Заказ через моб. приложение'), [
                                '' => t('Не важно'),
                                1 => t('Да'),
                                0 => t('Нет'),
                            ]),
                        ]]),
                    ]
                ])
            ]),
            'Caption' => t('Поиск по заказам')
        ]));

        return $helper;
    }

    /**
     * Возвращает объект обёртки для создания
     *
     */
    function helperAdd()
    {
        return $this->helperEdit();
    }

    /**
     * Удаление заказа
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDelOrder()
    {
        //Проверяем права на запись для модуля Магазин
        if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_DELETE)) {
            return $this->result->setSuccess(false)->addSection('noUpdate', true)->addEMessage($error);
        }

        $id = $this->url->request('id', TYPE_INTEGER);
        if (!empty($id)) {
            $obj = $this->api->getElement();
            $obj->load($id);
            $obj->delete();
        }
        if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
            $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
        }

        return $this->result->setSuccess(true)->addSection('noUpdate', true)->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name . 'index'));
    }

    /**
     * Перемещает заказы в архив
     *
     * @return Standard
     */
    public function actionUnarchiveOrders()
    {
        $ids = $this->modifySelectAll($this->url->request('chk', TYPE_ARRAY, []));
        $offset = $this->url->request('offset', TYPE_INTEGER, 0);

        $ids_parts = array_chunk($ids, ArchiveOrderApi::MOVE_STEP_COUNT);

        $api = new ArchiveOrderApi();
        $api->moveFromArchive($ids_parts[$offset]);


        $this->result->setSuccess(true);
        $offset++;
        if (count($ids_parts) > $offset) {
            $this->result->addSection('repeat', true)->addSection('queryParams', [
                'data' => [
                    'chk' => $ids,
                    'offset' => $offset,
                ],
            ]);
        } else {
            $this->result->addMessage(t('%0 заказов перемещено из архива', [count($ids)]));
        }
        return $this->result;
    }
}
