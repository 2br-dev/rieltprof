<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;

use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Product;
use Catalog\Model\WareHouseApi;
use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Application\Application;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Exception as RSException;
use RS\Helper\Paginator;
use RS\Helper\Tools as HelperTools;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Tree;
use RS\Module\AbstractModel\EntityList;
use RS\Module\Item as ModuleItem;
use RS\Router\Manager as RouterManager;
use Shop\Config\ModuleRights;
use Shop\Model\AddressApi;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;
use Shop\Model\Exception as ShopException;
use Shop\Model\HtmlFilterType as ShopHtmlFilterType;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;
use Shop\Model\Orm\UserStatus;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\PrintForm\AbstractPrintForm;
use Shop\Model\ProductsReturnApi;
use Shop\Model\RegionApi;
use Shop\Model\UserStatusApi;
use Users\Config\File as UserSConfig;
use Users\Model\Orm\User;

/**
 * Контроллер Управление заказами
 */
class OrderCtrl extends Crud
{
    const QUICK_VIEW_PAGE_SIZE = 20;

    /** @var OrderApi */
    protected $api;
    protected $status;

    function __construct()
    {
        parent::__construct(new OrderApi());

        $default_order_id = $this->url->get('id', TYPE_INTEGER);
        $order_id = $this->url->get('order_id', TYPE_INTEGER, $default_order_id);

        if ($order_id) {
            // Установим необходимый текущий сайт, если редактирование заказа
            // происходит из другого мультисайта.
            $order_site_id = $this->api->getSiteIdByOrderId($order_id);
            $this->api->setSiteContext($order_site_id);
            $this->changeSiteIdIfNeed($order_site_id);
        }

        $this->setTreeApi(new UserStatusApi(), t('статус'));
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();

        $edit_href = $this->router->getAdminPattern('edit', [':id' => '@id']);
        $this->status = $this->url->request('status', TYPE_INTEGER);

        if ($this->status > 0 && $current_status = $this->getTreeApi()->getOneItem($this->status)) {
            $this->api->setFilter('status', $this->status);
        } else {
            $this->status = 0;
            $current_status = null;
        }

        $helper
            ->setTopHelp(t('Здесь отображаются оформленные пользователями и администраторами заказы. 
                            Используйте статусы для информирования клиентов о ходе выполнения заказов и внутреннего контроля исполнения заказов. 
                            Напоминаем, что заказы могут оплачиваться пользователями только в статусе <i>Ожидает оплату</i>. 
                            Используйте статус <i>Новый</i>, если заказ требует модерации или проверки менеджером. 
                            Завершенные заказы следует переводить в статус <i>Выполнен и закрыт</i>. 
                            Переводите заказ в статус <i>Отменен</i>, чтобы вернуть остатки на склады и отметить, что заказ не следует исполнять (только если включен контроль остатков). 
                            Корректное назначение статусов поможет системе верно строить графики и показывать отчеты. 
                            Вы всегда можете переименовать системные статусы или назначить им дублеров с отличными именами. 
                            Создавайте произвольные статусы, чтобы более точно информировать пользователей о текущем положении заказа в цепочке ваших бизнесс процессов.'))
            ->setTopToolbar(new Toolbar\Element([
                    'Items' => [
                        new ToolbarButton\Dropdown([
                            [
                                'title' => t('создать заказ'),
                                'attr' => [
                                    'href' => $this->router->getAdminUrl('add'),
                                    'class' => 'btn-success'
                                ]
                            ],
                            [
                                'title' => t('добавить статус'),
                                'attr' => [
                                    'href' => $this->router->getAdminUrl('treeAdd'),
                                    'class' => 'crud-add'
                                ]
                            ]
                        ]),
                    ]]
            ))
            ->viewAsTableTree()
            ->setTopTitle(t('Заказы'))
            ->setTable(new Table\Element([
                'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Viewed(null, $this->api->getMeterApi()),
                    new TableType\Text('order_num', t('Номер'), ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href]),
                    new TableType\Usertpl('user_id', t('Покупатель'), '%shop%/order_user_cell.tpl', ['href' => $edit_href]),
                    new TableType\Usertpl('status', t('Статус'), '%shop%/order_status_cell.tpl', ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href]),
                    new TableType\Datetime('dateof', t('Дата оформления'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                    new TableType\Datetime('dateofupdate', t('Дата обновления'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Usertpl('totalcost', t('Сумма'), '%shop%/order_totalcost_cell.tpl', ['Sortable' => SORTABLE_BOTH]),
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
                    new TableType\Text('payment', t('Способ оплаты'), ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href, 'hidden' => true]),
                    new TableType\Text('delivery', t('Способ Доставки'), ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href, 'hidden' => true]),
                    new TableType\Text('track_number', t('Трек-номер'), ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href, 'hidden' => true]),
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
                    new TableType\Text('manager_user_id', t('Менеджер заказа'), ['Sortable' => SORTABLE_BOTH, 'href' => $edit_href, 'hidden' => true]),

                    new TableType\Actions('id', [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'noajax' => true,
                            'attr' => [
                                '@data-id' => '@id',
                            ]]),
                        new TableType\Action\DropDown([
                            [
                                'title' => t('Повторить заказ'),
                                'attr' => [
                                    'class' => ' ',
                                    '@href' => $this->router->getAdminPattern('add', [':from_order' => '@id']),
                                ]
                            ],
                            [
                                'title' => t('Оформить возврат'),
                                'attr' => [
                                    'class' => 'crud-add',
                                    '@href' => $this->router->getAdminPattern('add', [':order_id' => '@id'], 'shop-returnsctrl'),
                                ]
                            ],
                            [
                                'title' => t('Перейти к транзакциям'),
                                'attr' => [
                                    'target' => '_blank',
                                    '@href' => $this->router->getAdminPattern('index', [
                                        'f[entity][type]' => 'order',
                                        ':f[entity][id]' => '@id',
                                    ], 'shop-transactionctrl'),
                                ]
                            ],
                        ])
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ]
            ]));

        $helper['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Экспорт/Отчёт'),
                'attr' => [
                    'class' => 'button',
                    'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                ]
            ],
            [
                'title' => t('Экспорт заказов в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'shop-order', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Экспорт заказанных товаров в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'shop-orderitems', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Показать отчёт'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('ordersReport'),
                    'class' => 'crud-add'
                ]
            ],
        ]));

        $helper
            ->setTree($this->getIndexTreeElement(), $this->getTreeApi())
            ->setTreeBottomToolbar(new Toolbar\Element([
                'Items' => [
                    new ToolbarButton\Delete(null, null, [
                        'attr' => ['data-url' => $this->router->getAdminUrl('treeDel')]
                    ]),
                ]
            ]));

        $payments = ['' => t('Любая')] + PaymentApi::staticSelectList();
        $deliveries = ['' => t('Любая')] + DeliveryApi::staticSelectList();

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\DropUp([
                    [
                        'title' => t('Печать'),
                        'attr' => [
                            'class' => 'button',
                            'onclick' => "JavaScript:\$(this).parent().rsDropdownButton('toggle')"
                        ]
                    ],
                    [
                        'title' => t('Заказ'),
                        'attr' => [
                            'data-url' => RouterManager::obj()->getAdminUrl('massPrint', ['type' => 'orderform']),
                            'class' => 'crud-post'
                        ]
                    ],
                    [
                        'title' => t('Товарный чек'),
                        'attr' => [
                            'data-url' => RouterManager::obj()->getAdminUrl('massPrint', ['type' => 'commoditycheck']),
                            'class' => 'crud-post'
                        ]
                    ],
                    [
                        'title' => t('Лист доставки'),
                        'attr' => [
                            'data-url' => RouterManager::obj()->getAdminUrl('massPrint', ['type' => 'deliverynote']),
                            'class' => 'crud-post'
                        ]
                    ],
                ]),
                new ToolbarButton\DropUp([
                    [
                        'title' => t('редактировать'),
                        'attr' => [
                            'data-url' => $this->router->getAdminUrl('multiEdit_order'),
                            'class' => 'btn-alt btn-primary crud-multiedit'
                        ],
                    ]
                ], ['attr' => ['class' => 'edit']]),
                new ToolbarButton\Delete(null, null, ['attr' =>
                    ['data-url' => $this->router->getAdminUrl('del')]
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
     * Возвращает объект с настройками отображения дерева
     * Перегружается у наследника
     *
     * @return Tree\Element
     */
    protected function getIndexTreeElement()
    {
        $tree = new Tree\Element([
            'classField' => '_class',
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->status,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
            ],
            'callbacks' => [
                'disabledCheckbox' => function ($tree_item) {
                    return !($tree_item instanceof UserStatus) || $tree_item->isSystem();
                },
                'noRedMarker' => function ($tree_item) {
                    return !($tree_item instanceof UserStatus) || $tree_item->isSystem();
                },
            ],
            'sortable' => true,
            'sortUrl' => $this->router->getAdminUrl('treeMove'),
            'mainColumn' => new TableType\Usertpl('title', t('Название'), '%shop%/order_tree_cell.tpl', [
                'linkAttr' => ['class' => 'call-update'],
                'href' => $this->router->getAdminPattern(false, [':status' => '@id'])
            ]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('treeEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id',
                    ]
                ])
            ]),
            'headButtons' => [
                [
                    'text' => t('Статус'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ],
                ],
                [
                    'attr' => [
                        'title' => t('Добавить статус'),
                        'href' => $this->router->getAdminUrl('treeAdd'),
                        'class' => 'add crud-add',
                    ]
                ]
            ],
        ]);

        return $tree;
    }

    function actionMultiEdit_order()
    {
        $user_status_api = new UserStatusApi();
        $this->view->assign([
            'status_list' => $user_status_api->getTreeList(),
            'default_statuses' => $user_status_api->getStatusIdByType(),
        ]);
        $this->setHelper($this->helperMultiEdit());
        return parent::actionMultiEdit();
    }

    /**
     * Отбработка хелпера, подготовка обёртки
     *
     */
    function helperEdit()
    {
        $id = $this->url->request('id', TYPE_STRING, 0);
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $this->buttons([$id > 0 ? 'saveapply' : 'apply', 'cancel']),
            'viewAs' => 'form'
        ]);
        if ($id > 0) { //Если заказ уже существует
            $helper['bottomToolbar']->addItem(
                new ToolbarButton\delete($this->router->getAdminUrl('delOrder', ['id' => $id, 'dialogMode' => $this->url->request('dialogMode', TYPE_INTEGER)]), null, [
                    'noajax' => true,
                    'attr' => [
                        'class' => 'btn-danger delete crud-get crud-close-dialog',
                        'data-confirm-text' => t('Вы действительно хотите удалить заказ?')
                    ]
                ]),
                'delete'
            );
            //Добавим ещё повотр заказа
            $helper['bottomToolbar']->addItem(
                new ToolbarButton\Cancel($this->router->getAdminUrl('add', ['from_order' => $id]), t('Повторить заказ'), [
                    'noajax' => true,
                    'attr' => [
                        'class' => 'btn btn-alt btn-primary m-l-30',
                    ]
                ])
            );
            // Добавим контрол отгрузки
            $helper['bottomToolbar']->addItem(
                new ToolbarButton\Button($this->router->getAdminUrl('shipment', ['order_id' => $id], 'shop-markingtools'), t('Отгрузить'), [
                    'attr' => [
                        'class' => 'btn btn-alt btn-primary m-l-30 crud-edit shipmentToolbarButton',
                    ]
                ])
            );
            // Добавим ссылку на транзакции
            $params = [
                'f[entity][type]' => 'order',
                'f[entity][id]' => $id,
            ];
            $helper['bottomToolbar']->addItem(
                new ToolbarButton\Button($this->router->getAdminUrl('index', $params, 'shop-transactionctrl'), t('Перейти к транзакциям'), [
                    'attr' => [
                        'class' => 'm-l-30',
                        'target' => '_blank',
                    ]
                ])
            );
        }
        return $helper;
    }

    /**
     * Форма добавления элемента дерева
     *
     * @return CrudCollection
     */
    public function helperTreeAdd()
    {
        $this->api = $this->getTreeApi();
        $id = $this->url->request('id', TYPE_INTEGER, 0);
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $this->buttons([$id > 0 ? 'saveapply' : 'save', 'cancel']),
            'viewAs' => 'form'
        ]);

        return $helper;
    }

    /**
     * Обрабатывает заказ - страница редактирования
     *
     */
    function actionEdit()
    {
        $helper = $this->getHelper();

        $id = $this->url->request('id', TYPE_STRING, 0);
        $order_id = $this->url->request('order_id', TYPE_INTEGER, false);
        $refresh_mode = $this->url->request('refresh', TYPE_BOOLEAN);

        /** @var Order $order */
        $order = $this->api->getElement();
        if ($refresh_mode) {
            $order->setRefreshMode(true);
        }

        if ($id) {
            $order->load($id);
            $this->api->getMeterApi()->markAsViewed($id);

        } elseif ($order_id) { //Если идёт только создание
            $order['id'] = $order_id;
        }
        $show_delivery_buttons = 1; //Флаг показа дополнительных кнопок при смене доставки

        if ($this->url->isPost()) {
            //Подготавливаем заказ с учетом правок
            $user_id = $this->url->request('user_id', TYPE_INTEGER, 0); //id пользователя
            $post_address = $this->url->request('address', TYPE_ARRAY); //Сведения адреса
            $items = $this->url->request('items', TYPE_ARRAY);  //Товары
            $warehouse = $this->url->request('warehouse', TYPE_INTEGER); //Склад
            $delivery_extra = $this->url->request('delivery_extra', TYPE_ARRAY, false); //Дополнительные данные для доставки

            //Если склад изменили
            if ($order['warehouse'] != $warehouse) {
                $order['back_warehouse'] = $order['warehouse']; //Запишем склад на который надо вернуть остатки
            }

            //Если включено уведомлять пользователя, то сохраним сведения о прошлом адресе, который ещё не перезаписан
            if ($this->url->request('notify_user', TYPE_INTEGER, false)) {
                $order->before_address = $order->getAddress();
            }

            $old_extra = $order['extra']; // записыаем extra для последующей проверки
            //Получаем данные из POST
            $order->removeConditionCheckers();
            if (!$order->checkData()) {
                return $this->result
                    ->setErrors($order->getDisplayErrors());
            }
            // checkData стирает свойства ArrayList если они не пришли в post, если extra стёрлась - восстанавливаем её
            if (!empty($old_extra) && empty($order['extra'])) {
                $order['extra'] = $old_extra;
            }

            if ($delivery_extra) {
                $order->addExtraKeyPair('delivery_extra', $delivery_extra);
            }

            $address = new Address();
            $address->getFromArray($post_address + ['user_id' => $order['user_id']]);
            $address->updateAddressTitles(); // Скорректируем названия страны и региона перед выводом (как при сохранении)
            $address->updateCityId(); // Скорректируем id города перед выводом (как при сохранении)

            //Если включено уведомлять пользователя, то сохраним сведения о прошлом адресе, который ещё не перезаписан
            if ($this->url->request('notify_user', TYPE_INTEGER, false)) {
                $order->before_address = $order->getAddress();
            }

            if ($order['use_addr']) { //Если есть заданный адрес
                $order->setAddress($address);
            }

            //Если цена задана уже у заказа
            if ($this->url->post('user_delivery_cost', TYPE_STRING) === '') {
                $order['user_delivery_cost'] = null;
            }
            /* todo В связи с изменениями в работе доставок "delivery_new_query" теперь вызывается только при создании заказа
            //Если нужно пересчитать доставку
            if ($refresh_mode && ($this->url->post('user_delivery_cost', TYPE_STRING) === '')) {
                $order['delivery_new_query'] = 1;
            }*/

            //Если заказ создан установим флаг показа дополнительных кнопок доставки, если они существуют
            if ($order['id'] > 0) {
                $show_delivery_buttons = $this->url->post('show_delivery_buttons', TYPE_INTEGER, 1);
                /* todo В связи с изменениями в работе доставок "delivery_new_query" теперь вызывается только при создании заказа
                //Если мы поменяли в доставке что-либо, то тоже запросим внешнюю доставку, после сохранения
                if (!$show_delivery_buttons) {
                    $order['delivery_new_query'] = 1;
                }*/
            }


            $order->getCart()->updateOrderItems($items);

            if (!$refresh_mode) {
                //Проверяем права на запись для модуля Магазин
                if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                    $this->api->addError($error);
                    return $this->result->setSuccess(false)->setErrors($this->api->getDisplayErrors());
                }

                //Обновляем или вставим запись если сменился пользователь
                if ($order['use_addr']) {
                    //Проверим пользователя у Адреса, и если пользователь поменялся, то задублируем адрес, иначе обновим
                    $address_api = new AddressApi();
                    $address_api->setFilter('id', $order['use_addr']);
                    $old_address = $address_api->getFirst();

                    if ($old_address && ($old_address['user_id'] != $user_id)) {
                        unset($address['id']);
                        $address->insert();
                        $order->setUseAddr($address['id']);
                    } else {
                        $address['id'] = $order['use_addr'];
                        $address->update();
                    }
                }
                $order['is_exported'] = 0; //Устанавливаем флаг, что заказ нужно заново выгрузить в commerceML

                //Если нужно создать заказ
                if (isset($order['id']) && $order['id'] < 0) {
                    $order->setCurrency(CurrencyApi::getCurrentCurrency());
                    if ($save_result = $order->insert()) { //Перенаправим на ректирование, если создался заказ
                        $order->getCart()->saveOrderData();
                        return $this->result->setSuccess(true)
                            ->setSuccessText(t('Заказ успешно создан'))
                            ->setHtml(false)
                            ->addSection('windowRedirect', $this->router->getAdminUrl('edit', ['id' => $order['id']], 'shop-orderctrl'));
                    }
                } elseif ($save_result = $order->update($id)) {
                    $order->getCart()->saveOrderData();
                }

                $this->result->setSuccess($save_result);

                if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                    if (!$this->result->isSuccess()) {
                        $this->result->setErrors($order->getDisplayErrors());
                    } else {
                        $this->result->setSuccessText(t('Изменения успешно сохранены'));
                    }
                    return $this->result->getOutput();
                }

                if ($this->result->isSuccess()) {
                    $this->successSave();
                } else {
                    $helper['formErrors'] = $order->getDisplayErrors();
                }
            }
        }
        $user_num_of_order = $this->api->getUserOrdersCount($order['user_id']);

        //Склады
        $warehouses = WareHouseApi::getWarehousesList();
        $couriers = DeliveryApi::getCourierSelectList([]);

        $user_status_api = new UserStatusApi();
        $return_api = new ProductsReturnApi();
        $returned_items = $return_api->getReturnItemsByOrder($id);

        $this->view->assign([
            'order_footer_fields' => $order->getForm(null, 'footer', false, null, '%shop%/order_footer_maker.tpl'),
            'order_depend_fields' => $order->getForm(null, 'depend', false, null, '%shop%/order_depend_maker.tpl'),
            'order_user_fields' => $order->getForm(null, 'user', false, null, '%shop%/order_depend_maker.tpl'),
            'order_info_fields' => $order->getForm(null, 'info', false, null, '%shop%/order_info_maker.tpl'),
            'order_delivery_fields' => $order->getForm(null, 'delivery', false, null, '%shop%/order_info_maker.tpl'),
            'order_payment_fields' => $order->getForm(null, 'payment', false, null, '%shop%/order_info_maker.tpl'),

            'catalog_folders' => ModuleItem::getResourceFolders('catalog'),
            'elem' => $order,
            'user_id' => $order['user_id'],
            'warehouse_list' => $warehouses,
            'courier_list' => $couriers,
            'status_list' => $user_status_api->getTreeList(),
            'default_statuses' => $user_status_api->getStatusIdByType(),
            'refresh_mode' => $refresh_mode,
            'show_delivery_buttons' => $show_delivery_buttons,
            'user_num_of_order' => $user_num_of_order,
            'returned_items' => $returned_items,
        ]);
        $helper['form'] = $this->view->fetch('%shop%/orderview.tpl');
        $helper->setTopTitle(null);

        if ($refresh_mode) { //Если режим обновления
            return $this->result->setHtml($helper['form']);
        } else { //Если режим редактирования
            $this->view->assign([
                'elements' => $helper->active(),
            ]);
            return $this->result->setTemplate($helper['template']);
        }
    }

    /**
     * Возращает объект обёртки для создания
     *
     */
    function helperAdd()
    {
        return $this->helperEdit();
    }

    /**
     * Форма добавления элемента
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать ===true при успешном сохранении,
     *                                   иначе будет вызов стандартного _successSave метода
     * @param mixed $helper - переданный объект helper
     *
     * @return \RS\Controller\Result\Standard|bool
     * @throws \SmartyException
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if ($this->url->isPost()) { //Если был передан POST запрос
            return $this->actionEdit();
        }
        $from_order = $this->url->request('from_order', TYPE_INTEGER, false);

        $helper = $this->getHelper();
        //Создадим заказ с отрицательным идентификатором
        $order = $this->getApi()->getElement();
        //Посмотрим, не повтор ли это предыдущего заказа
        if ($from_order) {
            $order_api = new OrderApi();
            $order = $order_api->repeatOrder($from_order);
        } else {
            $order->setTemporaryId();
        }

        //Склады
        $warehouses = WareHouseApi::getWarehousesList();
        $couriers = DeliveryApi::getCourierSelectList([]);

        $user_status_api = new UserStatusApi();
        $this->view->assign([
            'elem' => $order,
            'order_footer_fields' => $order->getForm(null, 'footer', false, null, '%shop%/order_footer_maker.tpl'),
            'order_user_fields' => $order->getForm(null, 'user', false, null, '%shop%/order_depend_maker.tpl'),
            'catalog_folders' => ModuleItem::getResourceFolders('catalog'),
            'warehouse_list' => $warehouses,
            'courier_list' => $couriers,
            'status_list' => $user_status_api->getTreeList(),
            'user_num_of_order' => 0,
            'refresh_mode' => false
        ]);
        $helper['form'] = $this->view->fetch('%shop%/orderview.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Получает диалоговое окно с поиском пользователя для добавления или создания нового пользователя
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function actionUserDialog()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Добавление пользователя'));
        $helper->viewAsForm();

        $order = $this->api->getNewElement();
        $user = new User();
        $user['__phone']->setEnableVerification(false);

        //Если нужно обновить блок 
        if ($this->url->isPost()) {
            $is_reg_user = $this->request('is_reg_user', TYPE_INTEGER, 0); //Смотрим, нужно ли регистривать или указать существующего пользователя

            if ($is_reg_user) { //Если нужно регистрировать
                //Проверим
                $user['is_company'] = $this->request('is_company', TYPE_INTEGER, false);
                $user['company'] = $this->request('company', TYPE_STRING, false);
                $user['company_inn'] = $this->request('company_inn', TYPE_STRING, false);
                $user['name'] = $this->request('name', TYPE_STRING, false);
                $user['surname'] = $this->request('surname', TYPE_STRING, false);
                $user['midname'] = $this->request('midname', TYPE_STRING, false);
                $user['phone'] = $this->request('phone', TYPE_STRING, false);
                $user['login'] = $this->request('login', TYPE_STRING, false);
                $user['e_mail'] = $this->request('e_mail', TYPE_STRING, false);
                $user['openpass'] = $this->request('pass', TYPE_STRING, false);
                $user['data'] = $this->request('data', TYPE_ARRAY, false);
                $user['changepass'] = 1;
                if ($user->save()) {
                    $user_num_of_order = $this->api->getUserOrdersCount($user['id']);
                    $this->view->assign([
                        'user' => $user,
                        'user_num_of_order' => $user_num_of_order
                    ]);
                    return $this->result->setSuccess(true)
                        ->addSection('noUpdateTarget', true)
                        ->addSection('user_id', $user['id'])
                        ->addSection('insertBlockHTML', $this->view->fetch('%shop%/form/order/user.tpl'));
                } else {
                    foreach ($user->getErrors() as $error) {
                        $this->api->addError($error);
                    }
                }
            } else { //Если не нужно регистрировать, а указать конкретного пользователя
                $user_id = $this->request('user_id', TYPE_INTEGER, false);
                if ($user_id) {
                    $user = new User($user_id);
                    $user_num_of_order = $this->api->getUserOrdersCount($user_id);
                    $this->view->assign([
                        'user' => $user,
                        'user_num_of_order' => $user_num_of_order
                    ]);
                    return $this->result->setSuccess(true)
                        ->addSection('noUpdateTarget', true)
                        ->addSection('user_id', $user_id)
                        ->addSection('insertBlockHTML', $this->view->fetch('%shop%/form/order/user.tpl'));
                } else {
                    $this->api->addError(t('Не выбран пользователь для добавления'));
                }
            }
            return $this->result->setSuccess(false)
                ->setErrors($this->api->getDisplayErrors());
        }
        /** @var UsersConfig $users_config */
        $users_config = ConfigLoader::byModule('users');
        $conf_userfields = $users_config->getUserFieldsManager()->setErrorPrefix('userfield_')->setArrayWrapper('data');

        $this->view->assign([
            'user' => $user,
            'elem' => $order,
            'conf_userfields' => $conf_userfields
        ]);

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                'save' => new ToolbarButton\SaveForm(null, t('применить')),
                'cancel' => new ToolbarButton\Cancel(null, t('отмена')),
            ],
        ]));

        $helper['form'] = $this->view->fetch('%shop%/form/order/user_dialog.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Получает диалоговое окно доставки заказа
     *
     * @return Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionAddressDialog()
    {
        $helper = new CrudCollection($this);
        $helper->viewAsForm();

        $order_id = $this->url->request('order_id', TYPE_INTEGER, 0);
        /** @var Order $order */
        $order = $this->api->getNewElement();
        if ($order_id < 0) { //Если заказ только должен создаться
            $order['id'] = $order_id;
            $user_id = $this->request('user_id', TYPE_INTEGER, 0);
            $helper->setTopTitle(t('Добавление адреса'));
        } else { //Если уже заказ создан
            $order->load($order_id);
            $user_id = $order['user_id'];
            $helper->setTopTitle(t('Редактирование адреса'));
        }

        //Получим список адресов
        $address_api = new AddressApi();
        if ($user_id) { //Если пользователь указан
            $address_api->setFilter('user_id', $user_id);
            $address_list = $address_api->getList();
        } else { //Если есть только сведения о заказе
            $address_api->setFilter('order_id', $order_id);
            $address_list = $address_api->getList();
        }

        //Если задан конкретный адрес
        $order_use_addr = $this->url->request('use_addr', TYPE_INTEGER, $order['use_addr']);
        if ($order_use_addr) {
            $this->view->assign([
                'current_address' => $address_api->getOneItem($order_use_addr),
                'address_part' => $this->actionGetAddressRecord($order_use_addr)->getHtml(),
            ]);
        } else { //Если адресов нет, или они не заданы
            $use_addr = 0; //Выбранный адрес
            if ($address_list) {
                $first_address = reset($address_list);
                if ($first_address['id']) {
                    $use_addr = $first_address['id'];
                }
            }

            $this->view->assign([
                'address_part' => $this->actionGetAddressRecord($use_addr)->getHtml()
            ]);
        }

        if ($this->url->isPost()) { //Если пришёл запрос
            //Получим данные
            if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                $this->api->addError($error);
                return $this->result->setSuccess(false)->setErrors($this->api->getDisplayErrors());
            }
            $use_addr = $this->url->request('use_addr', TYPE_INTEGER); //Использовать адрес пользователя
            $post_address = $this->url->request('address', TYPE_ARRAY);  //Сведения об адресе
            $edit_type = $this->url->request('edit_type', TYPE_STRING);

            //Назначим значения
            $order->setUseAddr($use_addr);

            if ($edit_type == 'new') { //Если нужно создать новый адрес для доставки
                $address = new Address();
            } else { //Если используется существующий адрес
                $address = new Address($use_addr);
                $address['region_id'] = 0; // Для ситуации когда region_id указан у адреса, но его нет в post
            }

            $address->getFromArray($post_address + ['user_id' => $user_id]);

            if ($edit_type == 'new') { //Вставим
                if (!$user_id) { //Если пользователь не указан, запишем адрес к заказу
                    $address['order_id'] = $order['id'];
                }
                $address->insert();
                $use_addr = $address['id'];
            } else { //Обновим
                $address->update();
            }

            $order->setUseAddr($address['id']);

            $this->view->assign([
                'elem' => $order,
                'user_id' => $user_id,
                'address' => $address,
                'order_address_fields' => $order->getForm(null, 'address', false, null, '%shop%/order_info_maker.tpl'),
            ]);

            return $this->result->setSuccess(true)
                ->setHtml(false)
                ->addSection('noUpdateTarget', true)
                ->addSection('address', $post_address)
                ->addSection('use_addr', $use_addr)
                ->addSection('insertBlockHTML', $this->view->fetch('%shop%/form/order/address.tpl'));
        }

        $this->view->assign([
            'order' => $order,
            'address_list' => $address_list,
        ]);

        $bottom_toolbar_items = [];
        if ($order['use_addr']) {
            $bottom_toolbar_items['update_address'] = new ToolbarButton\SaveForm(null, t('изменить выбранный адрес'), [
                'attr' => [
                    'data-url' => $this->router->getAdminUrl(null, $this->url->getSource(GET) + ['edit_type' => 'edit']),
                ],
            ]);
        }
        if ($order['user_id'] || !$order['use_addr']) {
            $bottom_toolbar_items['new_address'] = new ToolbarButton\SaveForm(null, t('сохранить новый адрес'), [
                'attr' => [
                    'data-url' => $this->router->getAdminUrl(null, $this->url->getSource(GET) + ['edit_type' => 'new']),
                ],
            ]);
        }
        $bottom_toolbar_items['cancel'] = new ToolbarButton\Cancel(null, t('отмена'));

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => $bottom_toolbar_items,
        ]));

        $helper['form'] = $this->view->fetch('%shop%/form/order/address_dialog.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Получает диалоговое окно доставки заказа
     *
     * @return Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionDeliveryDialog()
    {
        $delivery_id = $this->url->request('delivery', TYPE_INTEGER); //Тип доставки
        $helper = new CrudCollection($this);
        $helper->viewAsForm();

        $order_id = $this->url->request('order_id', TYPE_INTEGER, 0);
        /** @var Order $order */
        $order = $this->api->getNewElement();
        if ($order_id < 0) { //Если заказ только должен создатся
            $order['id'] = $order_id;
            $user_id = $this->request('user_id', TYPE_INTEGER, 0);
            $helper->setTopTitle(t('Добавление доставки'));
        } else { //Если уже заказ создан
            $order->load($order_id);
            $user_id = $order['user_id'];
            $helper->setTopTitle(t('Редактирование доставки'));
        }

        $delivery_api = new DeliveryApi();
        $dlist = $delivery_api->getListForOrder();

        if ($this->url->isPost()) { //Если пришёл запрос
            //Получим данные
            if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                $this->api->addError($error);
                return $this->result->setSuccess(false)->setErrors($this->api->getDisplayErrors());
            }
            $user_delivery_cost = $this->url->request('user_delivery_cost', TYPE_STRING); //Своя цена доставки
            $delivery_id = $this->url->post('delivery', TYPE_INTEGER); //Тип доставки

            //Назначим значения
            $order['delivery'] = $delivery_id;

            $order_orm = new Order($order_id);
            $order['warehouse'] = $order_orm['warehouse'];

            $delivery = new Delivery($delivery_id); //Назначенная доставка
            $warehouses = WareHouseApi::getWarehousesList();//Склады
            $this->view->assign([
                'elem' => $order,
                'delivery' => $delivery,
                'user_id' => $user_id,
                'warehouse_list' => $warehouses,
                'user_delivery_cost' => $user_delivery_cost,
                'order_delivery_fields' => $order->getForm(null, 'delivery', false, null, '%shop%/order_info_maker.tpl'),
            ]);

            return $this->result->setSuccess(true)
                ->setHtml(false)
                ->addSection('noUpdateTarget', true)
                ->addSection('delivery', $delivery_id)
                ->addSection('user_delivery_cost', $user_delivery_cost)
                ->addSection('insertBlockHTML', $this->view->fetch('%shop%/form/order/delivery.tpl'));
        }

        $this->view->assign([
            'dlist' => $dlist,
            'order' => $order,
            'delivery_id' => $delivery_id,
        ]);

        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                'save' => new ToolbarButton\SaveForm(null, t('применить')),
                'cancel' => new ToolbarButton\Cancel(null, t('отмена')),
            ],
        ]));

        $helper['form'] = $this->view->fetch('%shop%/form/order/delivery_dialog.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Открывает диалоговое окно оплаты
     *
     * @return \RS\Controller\Result\Standard
     * @throws \SmartyException
     */
    function actionPaymentDialog()
    {
        $helper = new CrudCollection($this);
        $helper->viewAsForm();

        $order_id = $this->url->request('order_id', TYPE_INTEGER);
        /** @var Order $order */
        $order = $this->api->getNewElement();
        if ($order_id < 0) { //Если заказ только должен создатся
            $order['id'] = $order_id;
            $helper->setTopTitle(t('Добавление оплаты'));
        } else { //Если уже заказ создан
            $order->load($order_id);
            $helper->setTopTitle(t('Редактирование оплаты'));
        }

        if ($this->url->isPost()) { //Если пришёл запрос
            $pay_id = $this->url->request('payment', TYPE_INTEGER);
            $order['payment'] = $pay_id; //Установим оплату

            $this->view->assign([
                'elem' => $order,
                'payment_id' => $pay_id,
                'pay' => $order->getPayment(),
                'order_payment_fields' => $order->getForm(null, 'payment', false, null, '%shop%/order_info_maker.tpl'),
            ]);

            return $this->result->setSuccess(true)
                ->setHtml(false)
                ->addSection('noUpdateTarget', true)
                ->addSection('payment', $pay_id)
                ->addSection('insertBlockHTML', $this->view->fetch('%shop%/form/order/payment.tpl'));
        }

        $pay_api = new PaymentApi();
        $plist = $pay_api->getList();

        $this->view->assign([
            'order' => $order,
            'plist' => $plist
        ]);

        $helper
            ->setBottomToolbar(new Toolbar\Element([
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(null, t('применить')),
                    'cancel' => new ToolbarButton\Cancel(null, t('отмена')),
                ]
            ]));

        $helper['form'] = $this->view->fetch('%shop%/form/order/payment_dialog.tpl');
        return $this->result->setTemplate($helper['template']);
    }


    /**
     * Возвращает диалог с рекомендуемыми и сопутствующим
     *
     * @return \RS\Controller\Result\Standard
     * @throws \SmartyException
     */
    function actionGetRecommendedAndConcomitantBlock()
    {
        $order_id = $this->request('order_id', TYPE_INTEGER, 0); //id заказа
        $ids = $this->request('ids', TYPE_ARRAY, []); //id товаров для подгрузки

        if (!empty($ids)) {
            $data = [];
            $order = new Order($order_id);
            $user = $order->getUser();
            foreach ($ids as $id) {
                $product = new Product($id);
                $recommended = $product->getRecommended(true);
                $concomitant = $product->getConcomitant();

                $cost_id = ($user['id']) ? CostApi::getUserCost() : CostApi::getDefaultCostId(); //Определим цену для пользователя

                $this->view->assign([
                    'id' => $id,
                    'cost_id' => $cost_id,
                    'product' => $product,
                    'recommended' => $recommended,
                    'concomitants' => $concomitant
                ]);

                if (!empty($recommended) || !empty($concomitant)) {
                    $data[$id]['html'] = $this->view->fetch('%shop%/form/order/recommended_and_concomitant.tpl');
                } else {
                    $data[$id]['html'] = "";
                }
            }
            return $this->result->setSuccess(true)->addSection('data', $data);
        }
        return $this->result->setSuccess(false)->addEMessage(t('Не указаны товары'));
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

    function actionGetAddressRecord($_address_id = null)
    {
        $address_id = $this->url->request('address_id', TYPE_INTEGER, $_address_id);
        $country_list = RegionApi::countryList();
        $address = new Address($address_id);

        if ($address_id == 0) {
            $address['country_id'] = key($country_list);
        }

        if ($address['country_id']) {
            $region_api = new RegionApi();
            $region_api->setFilter('parent_id', $address['country_id']);
            $region_list = $region_api->getList();
        } else {
            $region_list = [];
        }

        $this->view->assign([
            'address' => $address,
            'country_list' => $country_list,
            'region_list' => $region_list
        ]);

        return $this->result->setTemplate('form/order/order_delivery_address.tpl');
    }

    function actionGetCountryRegions()
    {
        $parent = $this->url->request('parent', TYPE_INTEGER);

        $this->api = new RegionApi();
        $result = [];
        if ($parent) {
            $this->api->setFilter('parent_id', $parent);
            $list = $this->api->getAssocList('id', 'title');
            foreach ($list as $key => $value) {
                $result[] = ['key' => $key, 'value' => $value];
            }
        }
        return $this->result->addSection('list', $result);
    }

    function actionGetOfferPrice()
    {
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        $offer_id = $this->url->request('offer_id', TYPE_INTEGER);
        $shop_config = ConfigLoader::byModule('shop');
        $catalog_config = ConfigLoader::byModule('catalog');
        $cost_list = CostApi::getInstance()->queryObj()->objects(null, 'id');

        $product = new Product($product_id);
        $offer_costs = [];
        foreach ($cost_list as $type_cost) {
            $base_cost = $product->getCost($type_cost['id'], $offer_id, false);
            $discount_from_old_cost = 0;
            $title = $base_cost . ' - ' . $type_cost['title'];

            if ($shop_config['old_cost_delta_as_discount']) {
                $old_cost_id = $cost_list[$type_cost['old_cost']] ?? $catalog_config['old_cost'];
                $old_cost = $product->getCost($old_cost_id, $offer_id, false);
                if ($old_cost > $base_cost) {
                    $discount_from_old_cost = $old_cost - $base_cost;
                    $base_cost = $old_cost;
                }
            }

            $offer_costs[$type_cost['id']] = [
                'title' => $title,
                'base_cost' => $base_cost,
                'discount_from_old_cost' => $discount_from_old_cost,
            ];
        }

        return $this->result->addSection('costs', $offer_costs);
    }

    function actionGetUserAddresses()
    {
        $parent = $this->url->request('user_id', TYPE_INTEGER);

        $address_api = new AddressApi();
        $result = [];
        if ($parent) {
            $address_api->setFilter('user_id', $parent);
            /** @var Address $list */
            $list = $address_api->getList();
            foreach ($list as $value) {
                $result[] = ['key' => $value['id'], 'value' => $value->getLineView()];
            }
        }
        return $this->result->addSection('list', $result);
    }

    /**
     * Печать заказа
     */
    function actionPrintForm()
    {
        \RS\Img\Core::switchFormat(\RS\Img\Core::FORMAT_WEBP, false);
        $order_id = $this->url->request('order_id', TYPE_MIXED);
        $selectall = $this->url->request('selectAll', TYPE_STRING);
        $type = $this->url->request('type', TYPE_STRING);
        // если передан массив order_id загрузим выбранный список заказов
        if (is_array($order_id)) {
            if ($selectall) {
                $order = OrderApi::getSavedRequest('Shop\Controller\Admin\OrderCtrl_list')
                    ->limit(null)->objects();
            } else {
                $this->api->setFilter('id', $order_id, 'in');
                $order = $this->api->getList();
            }
        } else {
            $order = $this->api->getOneItem($order_id);
        }
        if (!empty($order)) {
            $print_form = AbstractPrintForm::getById($type, $order);
            if ($print_form) {
                $this->wrapOutput(false);
                return $print_form->getHtml();
            } else {
                return t('Печатная форма не найдена');
            }
        }
        return t('Заказ не найден');
    }

    /**
     * Действие которое вызывает окно с дополнительной информацией в заказе
     *
     * @return Standard
     */
    function actionOrderQuery()
    {
        $type = $this->request('type', TYPE_STRING, false);

        if (!$type) {
            return $this->result->setSuccess(false)->addSection('close_dialog', true)->addEMessage(t('Не указан параметр запроса - type (delivery или payment)'));
        }
        $order_id = $this->request('order_id', TYPE_STRING, 0);

        if (!$order_id) {
            return $this->result->setSuccess(false)->addSection('close_dialog', true)->addEMessage(t('id заказа указан неправильно'));
        }

        /** @var Order $order */
        $order = new Order($order_id);

        if (!$order['id']) {
            return $this->result->setSuccess(false)->addSection('close_dialog', true)->addEMessage(t('Такой заказ не найден'));
        }

        try {
            switch ($type) {
                case "delivery":
                    if ($delivery_id = $this->url->request('delivary_id', TYPE_INTEGER)) {
                        $order['delivery'] = $delivery_id;
                    }
                    return $this->result->setSuccess(true)->setHtml($order->getDelivery()->getTypeObject()->actionOrderQuery($order));
                    break;
                case "payment":
                    if ($payment_id = $this->url->request('payment_id', TYPE_INTEGER)) {
                        $order['payment'] = $payment_id;
                    }
                    return $this->result->addSection($order->getPayment()->getTypeObject()->actionOrderQuery($order));
                    break;
            }
        } catch(\RS\Exception $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage());
        }

        return null;
    }

    /**
     * Строит отчёт по заказам и выдаёт в отдельном окне
     *
     */
    function actionOrdersReport()
    {
        //Получим из сесии сведения по отбору
        $where_conditions = isset($_SESSION[EntityList::WHERE_CONDITION_VAR]['Shop\Controller\Admin\OrderCtrl_list']) ? clone $_SESSION[EntityList::WHERE_CONDITION_VAR]['Shop\Controller\Admin\OrderCtrl_list'] : false;
        if ($where_conditions) {
            //Получим данные в массив для отчёта
            $order_report_arr = $this->api->getReport($where_conditions);

            $this->view->assign([
                'order_report_arr' => $order_report_arr,
                'currency' => CurrencyApi::getBaseCurrency(),//Базовая валюта
                'payments' => PaymentApi::staticSelectList(),
                'deliveries' => DeliveryApi::staticSelectList(),
            ]);
        }

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Статистика по заказам'));
        $helper->viewAsAny();
        $helper['form'] = $this->view->fetch('orders_report.tpl');

        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Подбирает город по совпадению в переданной строке
     */
    function actionSearchCity()
    {
        $query = $this->request('term', TYPE_STRING, false);
        $region_id = $this->request('region_id', TYPE_INTEGER, false);
        $country_id = $this->request('country_id', TYPE_INTEGER, false);

        if ($query !== false && $this->url->isAjax()) { //Если задана поисковая фраза и это аякс
            $cities = $this->api->searchCityByRegionOrCountry($query, $region_id, $country_id);

            $result_json = [];
            if (!empty($cities)) {
                foreach ($cities as $city) {
                    $region = $city->getParent();
                    $country = $region->getParent();
                    $result_json[] = [
                        'value' => $city['title'],
                        'label' => preg_replace("%($query)%iu", '<b>$1</b>', $city['title']),
                        'id' => $city['id'],
                        'zipcode' => $city['zipcode'],
                        'region_id' => $region['id'],
                        'country_id' => $country['id']
                    ];
                }
            }

            $this->wrapOutput(false);
            $this->app->headers->addHeader('content-type', 'application/json');
            return json_encode($result_json);
        }
        return null;
    }

    /**
     * Отображает в side баре последние отфильтрованные заказы
     */
    function actionAjaxQuickShowOrders()
    {
        $page = $this->url->request('p', TYPE_INTEGER, 1);
        $exclude_id = $this->url->request('exclude_id', TYPE_INTEGER);

        $request_object = $this->api->getSavedRequest($this->controller_name . '_list');

        if ($request_object) {
            $this->api->setQueryObj($request_object);
            $this->api->setFilter("id", $exclude_id, '!=');
        }

        $paginator = new Paginator($page, $this->api->getListCount(), self::QUICK_VIEW_PAGE_SIZE);

        $this->view->assign([
            'orders' => $this->api->getList($page, self::QUICK_VIEW_PAGE_SIZE),
            'paginator' => $paginator
        ]);

        return $this->result->addSection(['title' => t('Быстрый просмотр заказов'),])->setTemplate('quick_show_orders.tpl');
    }

    /**
     * Прослойка для массовой печати документов к заказам
     */
    function actionMassPrint()
    {
        $type = $this->url->request('type', TYPE_STRING, null);
        $chk = $this->url->request('chk', TYPE_MIXED, null);
        $selectall = $this->url->request('selectAll', TYPE_STRING, null);
        $url = $this->router->getAdminUrl('printForm', ['type' => $type, 'order_id' => $chk, 'selectAll' => $selectall]);
        return $this->result->setAjaxWindowRedirect($url);
    }

    /**
     * Действие добавления элемента дерева
     *
     * @param int $primary_key_value - id объекта
     * @return \RS\Controller\Result\Standard|bool
     */
    function actionTreeAdd($primary_key_value = null)
    {
        $helper = $this->getHelper();

        /** @var UserStatus $elem */
        $elem = $this->api->getElement();
        if ($primary_key_value && $elem->isSystem()) {
            $elem['__type']->setAttr(['disabled' => true]);
        }

        if (!$elem->isSystem()) {
            $helper->setFormSwitch('other');
        }

        return parent::actionTreeAdd($primary_key_value);
    }

    /**
     * Создание пользователя из пользователя без регистрации
     *
     */
    function actionCreateUserFromNoRegister()
    {
        if ($this->url->isPost()) {
            $user = new User();
            $user->getFromArray($this->url->getSource(POST), "user_");
            //Уточним некоторые параметры
            $fio = explode(" ", $this->url->request('user_fio', TYPE_STRING, ""));
            if (isset($fio[0])) {
                $user['surname'] = $fio[0];
            }
            if (isset($fio[1])) {
                $user['name'] = $fio[1];
            }
            if (isset($fio[2])) {
                $user['midname'] = $fio[2];
            }

            $user['login'] = $this->url->request('user_email', TYPE_STRING, "");
            $user['e_mail'] = trim($this->url->request('user_email', TYPE_STRING, ""));
            $user['openpass'] = HelperTools::generatePassword(6);

            $user['__phone']->setEnableVerification(false);
            $user['__data']->removeAllCheckers();
            $user->save();

            //вставим запись если сменился пользователь

            $order_id = $this->url->get('order', TYPE_INTEGER, null);

            $order = new Order($order_id);
            if ($order_id && $order['use_addr']) {
                $address = $order->getAddress();
                if ($address['id']) {
                    $address['user_id'] = $user['id'];
                    $address->update();
                }
            }

            if ($user->hasError()) {
                return $this->result->setSuccess(false)->addEMessage($user->getErrorsStr());
            }
            $this->view->assign([
                'user' => $user
            ]);
            return $this->result->setSuccess(true)->addSection('user_id', $user['id'])->setTemplate('%shop%/form/order/user.tpl');
        }
        return $this->result->setSuccess(false);
    }

    /**
     * Вызов действия транзакции
     *
     * @return Standard
     */
    public function actionTransactionAction()
    {
        $transaction = new Transaction($this->url->request('transaction_id', TYPE_INTEGER));
        $action = $this->url->request('action', TYPE_STRING);

        try {
            if ($error = Rights::CheckRightError($this, ModuleRights::RIGHT_TRANSACTION_ACTIONS)) {
                throw new RSException($error);
            }

            $message = $transaction->getPayment()->getTypeObject()->executeTransactionAction($transaction, $action);
            return $this->result->setSuccess(true)->addMessage($message);
        } catch (RSException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage())->addSection('noUpdate', true);
        }
    }

    /**
     * Вызов действия заказа на доставку
     *
     * @return Standard
     * @throws \SmartyException
     */
    public function actionInterfaceDeliveryOrderAction()
    {
        try {
            $action = $this->url->request('action', TYPE_STRING);
            $order = new Order($this->url->request('order_id', TYPE_INTEGER));
            if (empty($order['id'])) {
                throw new ShopException(t('Указанный заказ не существует'));
            }
            $delivery_type = $order->getDelivery()->getTypeObject();
            if ($delivery_type instanceof InterfaceDeliveryOrder) {
                $result = $delivery_type->executeInterfaceDeliveryOrderAction($this->url, $order, $action);
                switch ($result['view_type']) {
                    case 'message':
                        $this->result->addMessage($result['message']);
                        if (!empty($result['no_update'])) {
                            $this->result->addSection('noUpdate', true);
                        }
                        break;
                    case 'form':
                        $helper = new CrudCollection($this);
                        $helper->viewAsForm();
                        $helper->setTopTitle($result['title']);
                        if (isset($result['bottom_toolbar'])) {
                            $helper->setBottomToolbar($result['bottom_toolbar']);
                        }
                        $this->view->assign($result['assign']);
                        $helper['form'] = $this->view->fetch($result['template']);
                        $this->result->setTemplate($helper['template']);
                        break;
                    case 'html':
                        $this->result->setHtml($result['html']);
                        break;
                    case 'output':
                        Application::getInstance()->headers->addHeader('Content-Type', $result['content_type'])->sendHeaders();
                        echo $result['content'];
                        break;
                }
                return $this->result->setSuccess(true);
            } else {
                throw new ShopException(t('Расчётный класс доставки не поддерживает интерфейс InterfaceDeliveryOrder'));
            }
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage())->addSection('noUpdate', true);
        }
    }

    /**
     * Вызов действия рекуррентных платежей
     *
     * @return Standard
     * @throws \SmartyException
     */
    public function actionInterfaceRecurringPaymentsAction()
    {
        try {
            $order = new Order($this->url->request('order_id', TYPE_INTEGER));
            $payment_type = $order->getPayment()->getTypeObject();
            $action = $this->url->request('action', TYPE_STRING);

            if (!$order['id']) {
                throw new ShopException(t('Не указан номер заказа'));
            }
            if (!($payment_type instanceof InterfaceRecurringPayments)) {
                throw new ShopException(t('Класс оплаты не поддерживает интерфейс "рекуррентных платежей"'));
            }

            $result = $payment_type->executeInterfaceRecurringPaymentsAction($order, $action);
            switch ($result['view_type']) {
                case 'message':
                    $this->result->addMessage($result['message'])->addSection('close_dialog', true);
                    break;
                case 'form':
                    $helper = new CrudCollection($this);
                    $helper->viewAsForm();
                    $helper->setTopTitle($result['title']);
                    if (isset($result['bottom_toolbar'])) {
                        $helper->setBottomToolbar($result['bottom_toolbar']);
                    }
                    $this->view->assign($result['assign']);
                    $helper['form'] = $this->view->fetch($result['template']);
                    $this->result->setTemplate($helper['template']);
                    break;
                default:
                    throw new ShopException(t('Недопустимый тип результата'));
            }
            return $this->result->setSuccess(true);
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addEMessage($e->getMessage())->addSection('noUpdate', true);
        }
    }
}
