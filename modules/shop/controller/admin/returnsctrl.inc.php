<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\Html\Toolbar;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use \RS\Html\Toolbar\Button as ToolbarButton;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;


/**
 * Class Возврат товара
 * @package Shop\Controller\Admin
 */
class ReturnsCtrl extends \RS\Controller\Admin\Crud
{

    /**
     * @var \Shop\Model\ProductsReturnApi $api
     */
    public $api;

    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new \Shop\Model\ProductsReturnApi());
    }

    /**
     * Хелпер для страницы возвратов
     *
     * @return CrudCollection
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Возвраты товаров'));
        $helper->setTopHelp(t('В данном разделе можно создавать, редактировать, удалять заявления на возврат товаров. Для каждого заявления можно распечатать печатную форму. Заявление на возврат может содержать не все товарные позиции, вошедшие в заказ.'));

        $helper->setTopToolbar(new \RS\Html\Toolbar\Element([
            'Items' => [
                new \RS\Html\Toolbar\Button\Add($this->router->getAdminPattern('preAdd'), t('Создать возврат'), [
                    'attr' => [
                        'class' => 'btn-success crud-sm-dialog'
                    ]
                ])
            ]
        ]));
        $helper -> setTable(new Table\Element([
            'Columns' => [
                new TableType\Viewed(null, $this->api->getMeterApi()),
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('return_num', t('Номер возврата'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Userfunc('order_id', t('Номер заказа'), function($order_id){
                    $order = new \Shop\Model\Orm\Order($order_id);
                    return $order['order_num'];
                }, ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@order_id'], 'shop-orderctrl')]),
                new TableType\Datetime('dateof', t('Дата оформления возврата'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('cost_total', t('Сумма возврата'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('status', t('Статус'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Userfunc('user_id', t('Пользователь'), function($value, $cell) {
                    if ($value) {
                        $user = new \Users\Model\Orm\User($value);
                        return $user->getFio()."($value)";
                    }
                }, ['hidden' => true]),
                new TableType\Text('name', t('Имя'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('surname', t('Фамилия'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Actions('id', [
                    //Опишем инструменты, которые нужно отобразить в строке таблицы пользователю
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Выбить чек'),
                            'attr' => [
                                'class' => 'crud-get',
                                '@href' => $this->router->getAdminPattern('sendReceipt', [':id' => '@id'], 'shop-transactionctrl'),
                            ],
                        ],
                        [
                            'title' => t('Перейти к транзакциям'),
                            'attr' => [
                                'target' => '_blank',
                                '@href' => $this->router->getAdminPattern('index', [
                                    'f[entity][type]' => Transaction::ENTITY_PRODUCTS_RETURN,
                                    ':f[entity][id]' => '@id',
                                ], 'shop-transactionctrl'),
                            ]
                        ],
                        [
                            'title' => t('Клонировать возврат'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('Распечатать заявление на возврат'),
                            'attr' => [
                                'target' => '_blank',
                                '@href' => $this->router->getUrlPattern('shop-front-myproductsreturn', [
                                    'Act' => 'print',
                                    ':return_id' => '@id'
                                ])
                            ]
                        ]
                    ])
                ],
                    //Включим отображение кнопки настройки колонок в таблице
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]]
        ));

        $returnproduct = new \Shop\Model\Orm\ProductsReturn();
        $statuses = ["" => t("-Не выбрано-")] + $returnproduct->__status->getList();

        //Опишем фильтр, который следует добавить
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container( [ //Контейнер визуального фильтра
                'Lines' =>  [
                    new Filter\Line( ['Items' => [ //Одна линия фильтров
                        new Filter\Type\Text('id', '№'), //Фильтр по ID
                        new Filter\Type\Text('order_num', t('Номер возврата'), ['searchType' => '%like%']),
                        new Filter\Type\DateRange('dateof', t('Дата оформления возврата')),
                        new Filter\Type\Text('name', t('Имя'), ['searchType' => '%like%']),
                        new Filter\Type\Text('surname', t('Фамилия'), ['searchType' => '%like%']),
                        new Filter\Type\Select('status', t('Статус возврата'), $statuses),
                    ]
                    ]),
                ]
            ]),
            'Caption' => t('Поиск')
        ]));

        return $helper;
    }

    /**
     * Возвращает идентификаторы заказов для конкретного пользователя
     *
     * @return string
     * @throws \RS\Db\Exception
     */
    function actionAjaxUsersOrder()
    {
        $user_id = $this->url->request('user_id',TYPE_INTEGER, 0);

        $order_info = \RS\Orm\Request::make()
                        ->from(new\Shop\Model\Orm\Order())
                        ->where([
                            'user_id' => $user_id
                        ])
                        ->orderby('dateof desc')
                        ->exec()
                        ->fetchSelected(null, ['id', 'order_num', 'dateof']);

        $orders = [];
        if (!empty($order_info)){
            foreach ($order_info as $order){
                $orders[] = [
                    'order_id' => $order['id'],
                    'order_num' => $order['order_num'],
                    'date' => date('d.m.Y', strtotime($order['dateof'])),
                    'dateof' => $order['dateof'],
                ];
            }
        }

        return $this->result->setSuccess(true)->addSection('orders', $orders);
    }

    /**
     * Окно добавления возврата по пользователи или id заказа
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionPreAdd()
    {
        $helper = new CrudCollection($this);
        $helper->setBottomToolbar( new Toolbar\Element ([
                'items' => [new ToolbarButton\SaveForm(null, t('Далее'))]
        ]));

        //Определим объект
        $form_object = new FormObject(new PropertyIterator([
            'user_id' => new Type\User([
                'description' => t('Пользователь'),
                'Attr' => [[
                    'placeholder' => t('ФИО или E-mail')
                ]],
            ]),
            'order_num' => new Type\Varchar([
                'description' => t('Номер заказа'),
                'Checker' => ['chkEmpty', t('Номер заказа - обязательное поле')],
                'template' => '%shop%/form/productsreturn/pre_add/order_num.tpl'
            ])
        ]));

        if ($this->url->isPost()) { //Если запрос пришел
            $form_object->checkData();
            if ($form_object->hasError()){
                return $this->result->setSuccess(false)->setErrors($form_object->getDisplayErrors());
            }

            //Проверяем существование заказа
            $order_orm = \Shop\Model\Orm\Order::loadByWhere([
                'order_num' => $form_object['order_num'],
                'site_id' => \RS\Site\Manager::getSiteId()
            ]);

            if ($order_orm['id']) {
                $url = $this->router->getAdminUrl('add', ['order_id' => $order_orm['id']]);

                return $this->result->setSuccess(true)
                    ->setNoAjaxRedirect($url)
                    ->addSection('callCrudAdd', $url);
            }
            return $this->result->setSuccess(false)->addEMessage(t('Заказ с таким номером не существует'));
        }

        $helper->viewAsForm();
        $helper->setTopTitle(t('Создать заявление на возврат товаров'));
        $helper->setFormObject($form_object);
        return $this->result->setTemplate( $helper['template'] );
    }


    /**
     * Добавление возрата товара по заказу и пользователю
     *
     * @param null|integer $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении,
     *                                   иначе будет вызов стандартного _successSave метода
     * @param null|\RS\Controller\Admin\Helper\CrudCollection $helper - текуй хелпер
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        /** @var \Shop\Model\Orm\ProductsReturn $return_orm */
        $return_orm = $this->api->getElement();
        if ($order_id = $this->url->request('order_id', TYPE_INTEGER, 0)){
            $order = new Order($order_id);
            if (!$order['id']) {
                $this->e404(t('Заказ не найден'));
            }

            $return_orm['order_id'] = $order_id;
            $return_orm->preFillFields();

            $this->getHelper()->setTopTitle(t('Создать возврат по заказу N%0', [$order['order_num']]));
        }

        if ($primaryKeyValue) {
            $this->getHelper()->setTopTitle(t('Редактировать возврат {return_num}'));
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Метод для клонирования возврата
     *
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
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
            unset($elem['return_num']);
            return $this->actionAdd($clone_id);
        } else {
            $this->e404();
        }
    }
}