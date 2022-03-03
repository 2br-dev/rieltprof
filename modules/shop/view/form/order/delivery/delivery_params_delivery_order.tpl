<div class="delivery-orders">
    {$delivery_order_list = $type_object->getDeliveryOrderList($order)}
    {if $delivery_order_list}
        <h4>{t}Заказы на доставку{/t}</h4>
        <div class="delivery-orders-list">
            {foreach $delivery_order_list as $delivery_order}
                <div class="delivery-orders-item">
                    <a href="{adminurl do='interfaceDeliveryOrderAction' action='view' order_id=$order.id delivery_order_id=$delivery_order.id}" class="crud-edit crud-sm-dialog">
                        {t num=$delivery_order.number
                           date={$delivery_order.creation_date|dateformat:"@date @time"}}№%num от %date{/t}
                    </a>
                    <a href="{adminurl do='interfaceDeliveryOrderAction' action='delete' order_id=$order.id delivery_order_id=$delivery_order.id}" class="delivery-orders-item-delete crud-get rs-icon-cross" data-confirm-text="{t}Вы действительно хотите удалить заказ на доставку из службы доставки и интернет-магазина?{/t}"></a>
                </div>
            {/foreach}
        </div>
    {/if}
    <a href="{adminurl do='interfaceDeliveryOrderAction' action='create' order_id=$order.id}" class="btn btn-primary crud-get delivery-orders-crate rs-order-check-сhanges">{t}Создать заказ на доставку{/t}</a>
</div>
