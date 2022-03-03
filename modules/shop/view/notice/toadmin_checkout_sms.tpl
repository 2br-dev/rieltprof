{assign var=order value=$data->order}
{assign var=cart value=$order->getCart()}
{assign var=order_data value=$cart->getOrderData(true, false)}
{t}Оформлен заказ на сумму{/t} {$order_data.total_cost}
