{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$order=$data->order}
    <h3>{t order_num=$order.order_num date={$order.dateof|date_format:"%d.%m.%Y"} status=$order->getStatus()->title}Заказ N%order_num от %date был переведен в статус "%status".{/t}</h3>
    <p>{t href=$router->getUrl('shop-front-myorders', [], true)}Все подробности заказа Вы можете посмотреть в <a href="%href">личном кабинете</a>.{/t}</p>
{/block}