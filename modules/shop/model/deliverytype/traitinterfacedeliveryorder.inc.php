<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType;

use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar\Element as ToolbarElement;
use RS\Http\Request as HttpRequest;
use RS\Orm\Request as OrmRequest;
use RS\Router\Manager as RouterManager;
use RS\View\Engine as ViewEngine;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;

/**
 * Трейт работы с заказами на доставку
 * Используется вместе с интерфейсом Shop\Model\DeliveryType\InterfaceDeliveryOrder
 */
trait TraitInterfaceDeliveryOrder
{
    /**
     * Возвращает трек-номер указанного заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return string|null
     */
    public function getDeliveryOrderTrackNumber(DeliveryOrder $delivery_order): ?string
    {
        return null;
    }

    /**
     * Возвращает список заказов на доставку
     *
     * @param Order $order - заказ
     * @return DeliveryOrder[]
     */
    public function getDeliveryOrderList(Order $order): array
    {
        return (new OrmRequest())
            ->from(new DeliveryOrder())
            ->where([
                'order_id' => $order['id'],
                'delivery_type' => $this->getShortName(),
            ])
            ->orderby('creation_date asc')
            ->objects();
    }

    /**
     * Возвращает HTML для управления заказами на доставку в админке
     *
     * @param Order $order - заказ
     * @return string
     * @throws \SmartyException
     */
    public function getDeliveryOrderAdminDeliveryParamsHtml(Order $order): string
    {
        $view = new ViewEngine();
        $view->assign([
            'type_object' => $this,
            'order' => $order,
        ]);
        return $view->fetch('%shop%/form/order/delivery/delivery_params_delivery_order.tpl');
    }

    /**
     * Возвращает список дополнительных действий, доступных для указанного заказа на доставку
     * Каждое "действие" в списке имеет следующую структуру:
     *  [
     *      'title' => (string) текст на кнопке
     *      'class' => (string) стилизующие css классы (например, 'btn-primary btn-alt')
     *      'action' => (string) идентификатор действия который будет передан в метод executeInterfaceDeliveryOrderAction()
     *      'attributes' => (array) дополнительные html аттрибуты (например, ['target' => '_blank'])
     *  ]
     *
     * @param DeliveryOrder $delivery_order - объект заказка на доставку
     * @return array
     */
    public function getDeliveryOrderActions(DeliveryOrder $delivery_order): array
    {
        return [];
    }

    /**
     * Исполняет действие интерфейса заказов на доставку
     * При успехе - возвращает инструкции для вывода результата, при неудаче - бросает исключение
     *
     * Инструкция для вывода результата - это массив ключ=>значение, в котором тип отображения результата указывается в ключе 'view_type' (message|form|html|output)
     *  [
     *      'view_type' => 'message' - показ текстового уведомления
     *      'message' => (string) текст сообщения
     *  ];
     *  [
     *      'view_type' => 'form' - отображение формы
     *      'title' => (string) заголовок формы
     *      'assign' => (array) переменные, которые будут переданы в шаблон
     *      'template' => (string) шаблон тела формы
     *      'bottom_toolbar' => (\RS\Html\Toolbar\Element) нижняя панель действий формы
     *  ];
     *  [
     *      'view_type' => 'html' - возврат чистого html
     *      'html' => (string) возвращаемый html
     *  ];
     *  [
     *      'view_type' => 'output' - возврат содержимого без обёртки в ResultStandard (используется для отображения файлов)
     *      'content' => (string) текст сообщения
     *  ];
     *
     *
     * @param HttpRequest $http_request - объект запроса
     * @param Order $order - объект заказа
     * @param string $action - идентификатор действия
     * @return array
     * @throws ShopException
     * @throws \SmartyException
     */
    public function executeInterfaceDeliveryOrderAction(HttpRequest $http_request, Order $order, string $action): array
    {
        return $this->executeCommonDeliveryOrderAction($http_request, $order, $action);
    }

    /**
     * Возвращает true, если заказ на доставку можно удалять
     *
     * @return bool
     */
    public function canDeleteDeliveryOrder()
    {
        return true;
    }

    /**
     * Возвращает true, если заказ на доставку можно обновлять
     *
     * @return bool
     */
    public function canRefreshDeliveryOrder()
    {
        return true;
    }

    /**
     * Возвращает true, если заказ на доставку можно изменить
     *
     * @return bool
     */
    public function canChangeDeliveryOrder()
    {
        return true;
    }

    /**
     * Исполняет "общее" действие с заказом на доставку
     * При успехе - возвращает инструкции для вывода, при неудаче - бросает исключение
     *
     * @param HttpRequest $http_request - объект запроса
     * @param Order $order - объект заказа
     * @param string $action - идентификатор действия
     * @return array
     * @throws ShopException
     * @throws \SmartyException
     */
    protected function executeCommonDeliveryOrderAction(HttpRequest $http_request, Order $order, string $action): array
    {
        switch ($action) {
            case 'create':
                $this->createDeliveryOrder($order);
                return [
                    'view_type' => 'message',
                    'message' => t('Заказ на доставку успешно создан'),
                ];
            case 'view':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $items = [];

                if ($this->canRefreshDeliveryOrder()) {
                    $update_url = RouterManager::obj()->getAdminUrl('interfaceDeliveryOrderAction', [
                        'action' => 'refresh',
                        'order_id' => $order['id'],
                        'delivery_order_id' => $delivery_order['id'],
                    ]);

                    $items[] = new ToolbarButton\Button($update_url, t('Обновить данные'), [
                        'attr' => [
                            'class' => 'btn btn-sm btn-warning crud-get',
                            'data-update-container' => '.delivery-order-view',
                        ],
                    ]);
                }

                if ($this->canChangeDeliveryOrder()) {
                    $change_url = RouterManager::obj()->getAdminUrl('interfaceDeliveryOrderAction', [
                        'action' => 'change',
                        'order_id' => $order['id'],
                        'delivery_order_id' => $delivery_order['id'],
                    ]);

                    $items[] = new ToolbarButton\Button($change_url, t('Изменить заказ на доставку'), [
                        'attr' => [
                            'class' => 'btn btn-sm btn-primary crud-get',
                            'data-confirm-text' => t('Вы действительно хотите внести изменения в заказ на доставку, используя актуальные данные заказа?'),
                            'data-update-container' => '.delivery-order-view',
                        ],
                    ]);
                }

                if ($this->canDeleteDeliveryOrder()) {
                    $delete_url = RouterManager::obj()->getAdminUrl('interfaceDeliveryOrderAction', [
                        'action' => 'delete',
                        'order_id' => $order['id'],
                        'delivery_order_id' => $delivery_order['id'],
                    ]);

                    $items[] = new ToolbarButton\Button($delete_url, t('Удалить заказ на доставку'), [
                        'attr' => [
                            'class' => 'btn btn-sm btn-danger crud-get',
                            'data-confirm-text' => t('Вы действительно хотите удалить заказ на доставку?'),
                        ],
                    ]);
                }

                $bottom_toolbar = new ToolbarElement([
                    'Items' => $items,
                ]);
                return [
                    'view_type' => 'form',
                    'title' => t('Заказ на доставку № %0 от %1', [$delivery_order['number'], date('d.m.Y H:i' ,strtotime($delivery_order['creation_date']))]),
                    'bottom_toolbar' => $bottom_toolbar,
                    'template' => '%shop%/form/order/delivery/delivery_order_view.tpl',
                    'assign' => [
                        'delivery_order' => $delivery_order,
                        'type_object' => $this,
                        'order' => $order,
                    ],
                ];
            case 'delete':
                if ($this->canDeleteDeliveryOrder()) {
                    $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                    $this->deleteDeliveryOrder($delivery_order);
                    $message = t('Заказ на доставку успешно удалён');
                } else {
                    $message = t('Удаление заказа не поддерживается');
                }

                return [
                    'view_type' => 'message',
                    'message' => $message,
                ];
            case 'change':
                if ($this->canChangeDeliveryOrder()) {
                    $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                    $this->changeDeliveryOrder($delivery_order, $order);
                    $message = t('Заказ на доставку успешно изменён');
                } else {
                    $message = t('Изменение заказа не поддерживается');
                }
                return [
                    'view_type' => 'message',
                    'message' => $message,
                ];
            case 'refresh':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                if ($this->canRefreshDeliveryOrder()) {
                    $this->refreshDeliveryOrder($delivery_order);
                }
                $view = new ViewEngine();
                $view->assign([
                    'delivery_order' => $delivery_order,
                    'type_object' => $this,
                    'order' => $order,
                    'is_refresh' => true,
                ]);
                return [
                    'view_type' => 'html',
                    'html' => $view->fetch('%shop%/form/order/delivery/delivery_order_view.tpl'),
                ];
            default:
                throw new ShopException(t('Указанное действие не существует'));
        }
    }

    /**
     * Возвращает заказ на доставку, указанный в запросе
     *
     * @param HttpRequest $http_request - объект запроса
     * @param Order $order - заказ
     * @return DeliveryOrder
     * @throws ShopException
     */
    protected function getDeliveryOrderFromRequest(HttpRequest $http_request, Order $order): DeliveryOrder
    {
        $delivery_order_id = $http_request->request('delivery_order_id', TYPE_INTEGER);

        if (!$delivery_order_id) {
            throw new ShopException(t('В запросе не передан идентификатор заказа на доставку'));
        }

        $delivery_order = DeliveryOrder::loadByWhere([
            'order_id' => $order['id'],
            'id' => $delivery_order_id,
        ]);

        if (empty($delivery_order['id'])) {
            throw new ShopException(t('Указанный заказ на доставку не существует'));
        }

        return $delivery_order;
    }
}
