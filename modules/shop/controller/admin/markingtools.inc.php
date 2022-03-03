<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;

use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Db\Adapter as DbAdapter;
use RS\Helper\Tools as HelperTools;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Cart;
use Shop\Model\Exception as ShopException;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\OrderItemUIT;
use Shop\Model\Orm\Shipment;
use Shop\Model\Orm\ShipmentItem;
use Shop\Model\Orm\Transaction;
use Shop\Model\TransactionApi;

/**
 * Контроллер Инструменты маркировок
 */
class MarkingTools extends Crud
{
    /** @var OrderApi */
    protected $api;

    function __construct()
    {
        parent::__construct(new OrderApi());

        $order_id = $this->url->get('order_id', TYPE_INTEGER);

        if ($order_id) {
            // Установим необходимый текущий сайт, если редактирование заказа
            // происходит из другого мультисайта.
            $order_site_id = $this->api->getSiteIdByOrderId($order_id);
            $this->api->setSiteContext($order_site_id);
            $this->changeSiteIdIfNeed($order_site_id);
        }
    }

    public function actionShipment()
    {
        $helper = $this->getHelper();

        $order_id = $this->url->request('order_id', TYPE_INTEGER);

        $order = new Order($order_id);
        if (!$order['is_payed']) {
            return $this->result->setSuccess(false)->addEMessage(t('Для отгрузки заказ должен быть полностью оплачен'))->addSection('close_dialog', 1);
        }

        $cart = $order->getCart();
        $product_items = $cart->getProductItems();

        if ($this->url->isPost()) {
            $uit = $this->url->request('uit', TYPE_ARRAY);

            $uit_ids = [];
            foreach ($uit as $item_uit_list) {
                foreach ($item_uit_list as $uit_key => $uit_item) {
                    $uit_ids[] = DbAdapter::escape($uit_key);
                }
            }

            $exist_uits = (new OrmRequest())
                ->select('concat(U.gtin, U.serial) uit_id')
                ->from(new OrderItemUIT(), 'U')
                ->join(ShipmentItem::_getTable(), 'U.id = S.uit_id', 'S')
                ->where('concat(U.gtin, U.serial) in ("#0")', [implode('","', $uit_ids)])
                ->exec()->fetchSelected(null, 'uit_id');

            if ($exist_uits) {
                return $this->result->setSuccess(false)
                    ->addSection('error_type', 'uit_highlight')
                    ->addSection('error', t('В отгрузе присутствуют коды, отгруженные в других заказах.'))
                    ->addSection('uit_list', $exist_uits);
            }

            foreach ($product_items as $uniq => $item) {
                /** @var OrderItem $order_item */
                $order_item = $item[Cart::CART_ITEM_KEY];
                try {
                    $order_item->rewriteUITs($uit[$uniq] ?? []);
                } catch (MarkingException $e) {
                    throw $e;
                }
            }

            return $this->result->setSuccess(true);
        }

        $this->view->assign([
            'order' => $order,
            'cart' => $cart,
            'product_items' => $product_items,
            'shipped_amount' => self::getShippedItemsAmountByOrder($order),
            'shipped_uits' => self::getShippedItemsUITsByOrder($order),
        ]);

        $helper->setTopTitle(t('Отгрузка'));
        $helper['form'] = $this->view->fetch('%shop%/form/order/order_shipment.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    public function helperShipment()
    {
        $bottomToolbar = $this->buttons(['save', 'cancel']);
        $bottomToolbar->addItem(new ToolbarButton\Button('', t('отгрузить'), [
            'attr' => [
                'class' => 'execute-shipment btn-warning'
            ]
        ]));
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $bottomToolbar,
            'viewAs' => 'form',
        ]);
        return $helper;
    }

    /**
     * Разбирает содержимое штрихкода
     *
     * @return string
     */
    public function actionParseCode()
    {
        $result = [
            'success' => false,
            'error_type' => 'float_head',
        ];

        $this->wrapOutput(false);
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        $code = $this->url->request('code', TYPE_STRING);

        $product = new Product($product_id);
        if (empty($product['id'])) {
            $result['error'] = t('Указанный товар не найден');
        } elseif (empty($product['marked_class'])) {
            $result['error'] = t('Указанный товар не подлежит маркировке');
        } else {
            $marking_class = MarkingApi::getMarkedClasses()[$product['marked_class']];
            try {
                $uit = $marking_class->getUITFromCode($code);

                $result = [
                    'success' => true,
                    'result' => $uit->asArray(),
                ];
            } catch (MarkingException $e) {
                $result['error'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Согздаёт "отгрузку"
     *
     * @return Standard
     * @throws ShopException
     */
    public function actionMakeShipment()
    {
        $shop_config = ConfigLoader::byModule('shop');
        $order_id = $this->url->get('order_id', TYPE_STRING);
        $shipment_data = $this->url->post('shipment', TYPE_ARRAY);
        $add_delivery_to_shipment = $this->url->post('add_delivery', TYPE_ARRAY);
        $create_receipt = $shop_config['create_receipt_upon_shipment'] ?: $this->url->post('create_receipt', TYPE_ARRAY);
        $order = new Order($order_id);
        $product_items = $order->getCart()->getProductItems();

        if (empty($shipment_data)) {
            return $this->result->setSuccess(false)->addEMessage(t('Список товарных позиций для отгрузки пуст.'));
        }

        $order_uits = (new OrmRequest())
            ->from(new OrderItemUIT())
            ->where([
                'order_id' => $order_id,
            ])
            ->objects();

        $order_uits_by_front_id = [];
        foreach ($order_uits as $order_uit) {
            $front_id = $order_uit['gtin'] . $order_uit['serial'];
            $order_uits_by_front_id[$front_id] = $order_uit['id'];
        }
        $shipped_cost = self::getShippedItemsCostByOrder($order);
        $shipped_amount = self::getShippedItemsAmountByOrder($order);

        $shipment = new Shipment();
        $shipment['order_id'] = $order_id;
        $shipment['info_order_num'] = $order['order_num'];
        $shipment->setTempId();

        $total_shipment_cost = 0;
        $is_shipped = false;
        foreach ($shipment_data as $uniq => $item) {
            $cart_item = $product_items[$uniq][Cart::CART_ITEM_KEY];
            $shipped_item_cost = $shipped_cost[$uniq] ?? 0;
            $shipped_item_amount = $shipped_amount[$uniq] ?? 0;
            $item_cost_left = $cart_item['price'] - $cart_item['discount'] - $shipped_item_cost;
            $item_amount_left = $cart_item['amount'] - $shipped_item_amount;
            if ($item_amount_left > 0) {
                if (isset($item['uit'])) {
                    foreach ($item['uit'] as $front_uit) {
                        if (isset($order_uits_by_front_id[$front_uit])) {
                            $shipment_item = new ShipmentItem();
                            $shipment_item['order_id'] = $order_id;
                            $shipment_item['shipment_id'] = $shipment['temp_id'];
                            $shipment_item['order_item_uniq'] = $uniq;
                            $shipment_item['amount'] = 1;
                            $shipment_item['uit_id'] = $order_uits_by_front_id[$front_uit];

                            if ($shipped_item_amount + 1 == $cart_item['amount']) {
                                $shipment_item['cost'] = $item_cost_left;
                            } else {
                                $shipment_item['cost'] = round($item_cost_left / $item_amount_left * 100) / 100;
                            }

                            $shipment_item->insert();
                            $total_shipment_cost += $shipment_item['cost'];
                            $shipped_item_amount += $shipment_item['amount'];
                            $item_cost_left -= $shipment_item['cost'];
                        }
                    }
                } elseif (!empty($item['amount'])) {
                    $shipment_item = new ShipmentItem();
                    $shipment_item['order_id'] = $order_id;
                    $shipment_item['shipment_id'] = $shipment['temp_id'];
                    $shipment_item['order_item_uniq'] = $uniq;
                    $shipment_item['amount'] = ($item['amount'] > $item_amount_left) ? $item_amount_left : $item['amount'];

                    if ($shipped_item_amount + $item['amount'] == $cart_item['amount']) {
                        $shipment_item['cost'] = $item_cost_left;
                    } else {
                        $shipment_item['cost'] = round($item_cost_left / $item_amount_left * $item['amount'], 2);
                    }

                    $shipment_item->insert();
                    $total_shipment_cost += $shipment_item['cost'];
                }
                $is_shipped = true;
            }
        }

        if ($is_shipped) {
            if ($add_delivery_to_shipment) {
                foreach ($shipment->getOrder()->getCart()->getCartItemsByType(Cart::TYPE_DELIVERY) as $item) {
                    $shipment_item = new ShipmentItem();
                    $shipment_item['order_id'] = $order_id;
                    $shipment_item['shipment_id'] = $shipment['temp_id'];
                    $shipment_item['order_item_uniq'] = $item['uniq'];
                    $shipment_item['amount'] = $item['amount'];
                    $shipment_item['cost'] = $item['price'] - $item['discount'];

                    $shipment_item->insert();
                    $total_shipment_cost += $shipment_item['cost'];
                }
            }

            $shipment['info_total_sum'] = $total_shipment_cost;
            $shipment->insert();
            $transaction = new Transaction();
            $transaction['dateof'] = date('Y-m-d H:i:s');
            $transaction['order_id'] = $order_id;
            $transaction['user_id'] = $order->getUser()['id'];
            $transaction['personal_account'] = false;
            $transaction['cost'] = $total_shipment_cost;
            $transaction['reason'] = t('Отгрузка заказа №%0', [$order['order_num']]);
            $transaction['status'] = Transaction::STATUS_SUCCESS;
            $transaction['entity'] = Transaction::ENTITY_SHIPMENT;
            $transaction['entity_id'] = $shipment['id'];

            if ($transaction->insert()) {
                $transaction['sign'] = TransactionApi::getTransactionSign($transaction);
                $transaction->update();

                if ($create_receipt) {
                    $transaction_api = new TransactionApi();
                    $receipt_result = $transaction_api->createReceipt($transaction);
                    if ($receipt_result === true) {
                        return $this->result->setSuccess(true)->addMessage(t('Отгрузка успешно создана. Чек отправлен.'));
                    } else {
                        return $this->result->setSuccess(true)->addEMessage(t('Отгрузка создана. Ошибка при отправке чека: %0', [$receipt_result]));
                    }
                } else {
                    return $this->result->setSuccess(true)->addMessage(t('Отгрузка успешно создана.'));
                }
            } else {
                return $this->result->setSuccess(true)->addEMessage(t('Отгрузка создана. Ошибка при создании транзакции: %0', [$transaction->getErrorsStr()]));
            }
        } else {
            return $this->result->setSuccess(true)->addEMessage(t('Нет товаров для отгрузки.'));
        }
    }

    /**
     * Возвращает количество уже отгруженных товаров в заказе
     *
     * @param Order $order - заказ
     * @return float[]
     */
    protected static function getShippedItemsAmountByOrder(Order $order)
    {
        $items = (new OrmRequest())
            ->from(new ShipmentItem())
            ->where([
                'order_id' => $order['id'],
            ])
            ->exec()->fetchSelected('order_item_uniq', 'amount', true);

        $result = [];
        foreach ($items as $uniq => $amount) {
            $result[$uniq] = (is_array($amount)) ? array_sum($amount) : $amount;
        }

        return $result;
    }

    /**
     * Возвращает сумму уже отгруженных товаров в заказе
     *
     * @param Order $order - заказ
     * @return float[]
     */
    protected static function getShippedItemsCostByOrder(Order $order)
    {
        $items = (new OrmRequest())
            ->from(new ShipmentItem())
            ->where([
                'order_id' => $order['id'],
            ])
            ->exec()->fetchSelected('order_item_uniq', 'cost', true);

        $result = [];
        foreach ($items as $uniq => $cost) {
            $result[$uniq] = (is_array($cost)) ? array_sum($cost) : $cost;
        }

        return $result;
    }

    /**
     * Возвращает список идентификаторов уже отгруженных УИТ
     *
     * @param Order $order - заказ
     * @return array
     */
    protected static function getShippedItemsUITsByOrder(Order $order)
    {
        /** @var OrderItemUIT $uits */
        $uits = (new OrmRequest())
            ->from(new OrderItemUIT(), 'U')
            ->join(ShipmentItem::_getTable(), 'U.id = I.uit_id', 'I')
            ->where([
                'I.order_id' => $order['id'],
            ])
            ->objects();

        $result = [];
        foreach ($uits as $uit) {
            $result[] = $uit['gtin'] . $uit['serial'];
        }

        return $result;
    }
}
