{$order=$data->order}
{t order_num=$order.order_num date={$order.dateof|date_format:"%d.%m.%Y"} status=$order->getStatus()->title}Заказ N%order_num от %date был переведен в статус "%status".{/t}