{if !$is_refresh}
<div class="delivery-order-view updatable" data-url="{urlmake action='refresh'}" data-no-update-hash>
{/if}
    {if $delivery_order.address != $delivery_order->getAddressValue($order)}
        <div class="notice notice-danger">{t}Адрес в заказе был изменён, заказ оформлен на другой адрес{/t}</div>
    {/if}

    <div class="delivery-order-view-action-list">
        {foreach $delivery_order->getActions() as $action}
            <a href="{if !empty($action.attributes.href)}{$action.attributes.href}{else}{adminurl do='interfaceDeliveryOrderAction' action=$action.action order_id=$order.id delivery_order_id=$delivery_order.id}{/if}"
                class="btn btn-sm no-update-hash {$action.class}"
                {if $action.confirm_text}data-confirm-text="{$action.confirm_text}"{/if}
                {if $action.attributes}{foreach $action.attributes as $key => $value}{$key}="{$value}"{/foreach}{/if}>

                {$action.title}
            </a>
        {/foreach}
    </div>

    <table class="otable">
        {foreach $delivery_order->getDataLines() as $data_line}
            <tr>
                <td class="otitle">{$data_line.title}</td>
                <td>{$data_line.value}</td>
            </tr>
        {/foreach}
    </table>

{if !$is_refresh}
</div>
{/if}