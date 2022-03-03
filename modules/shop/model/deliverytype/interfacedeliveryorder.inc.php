<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType;

use RS\Http\Request as HttpRequest;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;

/**
 * Интерфейс работы с заказами на доставку
 * Используется вместе с трейтом Shop\Model\DeliveryType\TraitInterfaceDeliveryOrder
 */
interface InterfaceDeliveryOrder
{
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
    public function getDeliveryOrderActions(DeliveryOrder $delivery_order): array;

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
     */
    public function executeInterfaceDeliveryOrderAction(HttpRequest $http_request, Order $order, string $action): array;

    /**
     * Создаёт заказ на доставку
     *
     * @param Order $order - объект заказа
     * @return DeliveryOrder
     * @throws ShopException
     */
    public function createDeliveryOrder(Order $order): DeliveryOrder;

    /**
     * Удаляет заказ на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return void
     * @throws ShopException
     */
    public function deleteDeliveryOrder(DeliveryOrder $delivery_order): void;

    /**
     * Обновляет данные заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return void
     * @throws ShopException
     */
    public function refreshDeliveryOrder(DeliveryOrder $delivery_order): void;

    /**
     * Возвращает список данных заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return array
     */
    public function getDeliveryOrderDataLines(DeliveryOrder $delivery_order): array;

    /**
     * Возвращает трек-номер указанного заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return string|null
     */
    public function getDeliveryOrderTrackNumber(DeliveryOrder $delivery_order): ?string;

    /**
     * Возвращает список заказов на доставку
     *
     * @param Order $order - заказ
     * @return DeliveryOrder[]
     */
    public function getDeliveryOrderList(Order $order): array;

    /**
     * Возвращает HTML для управления заказами на доставку в админке
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getDeliveryOrderAdminDeliveryParamsHtml(Order $order): string;
}
