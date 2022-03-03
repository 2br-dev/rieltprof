<div class="tel-line">

    <div class="tel-row">
        {$order_count = $client->getLastOrders(false)}
        {if $order_count && $client.id>0} {$filter = ["user_id" => $client.id]} {else} {$filter = null} {/if}
        <a href="{adminUrl do=false mod_controller="shop-orderctrl" f=$filter}">{t number=$client->getLastOrders(false)}Заказов: %number{/t}</a>
        <div class="tel-dot"></div>
        <div>
            {if $order_count}
                <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
            {/if}
            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="shop-orderctrl"}" class="btn btn-warning btn-rect btn-inline zmdi zmdi-plus" title="{t}Создать заказ{/t}"></a>
        </div>
    </div>

    {if $order_count}
        <div class="tel-more-block">
            {foreach $client->getLastOrders() as $order}
                <a href="{adminUrl do=edit id=$order.id mod_controller="shop-orderctrl"}">{$order.order_num}</a>
            {/foreach}
        </div>
    {/if}
</div>