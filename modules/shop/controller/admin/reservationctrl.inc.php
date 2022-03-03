<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Application\Application;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер Управление налогами
*/
class ReservationCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new \Shop\Model\ReservationApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopToolbar(null);
        $helper->setTopTitle(t('Предварительные заказы'));
        $helper->setTopHelp(t('Предварительные заказы позволяют собирать информацию о намерении пользователей купить тот или иной товар в заданном количестве. Если заявка находится в статусе открыт и включена соответствующая опция в настройках модуля, то ReadyScript может уведомлять пользователей о поступлении данного товара в наличие. Вы также можете вручную обрабатывать данные заявки, связываться с клиентом и закрывать их. Предварительные заказы можно конвертировать в обычный заказ, для этого откройте предварительный заказ и нажмите соответствующую кнопку.'));
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->setHeaderHtml(
            $this->view
            ->assign('is_cron_work', \RS\Cron\Manager::obj()->isCronWork())
            ->fetch('reservation_cron_check.tpl')
        );

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Viewed(null, $this->api->getMeterApi()),
                new TableType\Text('product_title', t('Товар'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('amount', t('Количество')),
                new TableType\Text('offer', t('Комплектация')),
                new TableType\Text('phone', t('Телефон')),
                new TableType\Text('email', t('E-mail')),
                new TableType\Text('dateof', t('Дата'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('is_notify', t('Уведомление о поступлении')),
                new TableType\Text('status', t('Статус')),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('сформировать заказ'),
                            'attr' => [
                                '@href' => $this->router->getAdminPattern('createOrderFromReservation', [':reservation_id' => '@id']),
                            ]
                        ]
                    ])],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),

            ]
        ]));
        
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' =>  [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('product_id', t('ID товара')),
                        new Filter\Type\Text('product_barcode', t('Артикул товара')),
                        new Filter\Type\Text('email', 'E-mail', ['SearchType' => '%like%']),
                        new Filter\Type\Text('phone', t('Телефон'), ['SearchType' => '%like%']),
                        new Filter\Type\User('user_id', t('Пользователь')),
                    ]])
                ],
                'SecContainers' => [
                    new Filter\Seccontainer([
                        'Lines' => [
                            new Filter\Line(['items' => [
                                new Filter\Type\Date('dateof', t('Дата заказа'), ['showType' => true]),
                                new Filter\Type\Text('amount', t('Количество'), ['showType' => true]),
                                new Filter\Type\Select('status', t('Статус'), ['' => t('Любой'), 'open' => t('Открыт'), 'close' => t('Закрыт')]),
                            ]])
                        ]
                    ])
                ]
            ])
        ]));
        
        return $helper;
    }
    
    /**
    * Редактирование передварительного заказа
    * 
    */
    function actionEdit()
    {
        //Добавим кнопки внизу, если модуль магазин установлен
        if (\RS\Module\Manager::staticModuleExists('shop')){
            $id       = $this->url->get('id', TYPE_INTEGER, false);
            $reservation = $this->api->getById($id); //Получим сам элемент
            
            $this->getHelper()->setBottomToolbar(new Toolbar\Element( [
                'Items' => [
                    'save' => new ToolbarButton\SaveForm(null, t('сохранить')),
                    'cancel' => new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name.'index')),
                    'create' => new ToolbarButton\Button($this->router->getAdminUrl('createOrderFromReservation', ['reservation_id' => $reservation['id']]), t('создать заказ')),
                ]]
            ));    
        }
        
        return parent::actionEdit();
    }
    
    
    /**
    * Создаёт заказ из Предварительных заказов
    * 
    */
    function actionCreateOrderFromReservation()
    {
        $reservation_id       = $this->url->get('reservation_id', TYPE_INTEGER, false);  
        /**
        * @var \Shop\Model\Orm\Reservation $reservation
        */
        $reservation_item_api = new \Shop\Model\ReservationApi();
        $reservation          = $reservation_item_api->getById($reservation_id); 
       
        //изменение статуса предворительного заказа
        $order = $reservation_item_api->createOrderFromReservation($reservation);

        Application::getInstance()->redirect($this->router->getAdminUrl('edit', ['id'=>$order['id']], 'shop-orderctrl' ));
    }
}
