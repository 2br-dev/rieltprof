{$order=$data->order}{$order_before=$data->order->this_before_write}
{if $order_before.status != $order.status} {* Статус *}
   {t order_num=$order.order_num date={$order.dateof|date_format:"%d.%m.%Y"} status=$order->getStatus()->title}Заказ №%order_num от %date, изменился статус на "%status".{/t}
   {$url->getDomainStr()}
{else}
    {t order_num=$order.order_num date={$order.dateof|date_format:"%d.%m.%Y"}}Заказ №%order_num от %date был изменен.{/t}
    {t}Все подробности на сайте{/t} {$url->getDomainStr()}
{/if}
