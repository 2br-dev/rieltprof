{* Виджет: Недавние заказы *}
<div class="widget-filters">
    <div class="dropdown">
        <a id="last-order-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $filter=='active'}{t}незавершенные{/t}{else}{t}все{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="last-order-switcher">
            <li{if $filter=='active'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-lastorders" filter="active"}" class="call-update">{t}Незавершенные{/t}</a></li>
            <li{if $filter=='all'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-lastorders" filter="all"}" class="call-update">{t}Все{/t}</a></li>
        </ul>
    </div>
</div>

{if count($orders)}
    <table class="wtable mrg overable table-lastorder">
        <tbody>
            {foreach from=$orders item=order}
            {$status=$order->getStatus()}
            <tr onclick="window.open('{adminUrl mod_controller="shop-orderctrl" do="edit" id=$order.id}', '_blank')" class="clickable">
                <td class="number f-14">
                    <div class="title">
                        <span style="background:{$status->bgcolor}" title="{$status->title}" class="w-point"></span>
                        <b>{t num=$order.order_num}Заказ №%num{/t}</b>
                    </div>
                    <div class="price">{$order->getTotalPrice()}</div>
                </td>
                <td class="w-date">
                    {$order.dateof|dateformat:"%e %v %!Y"}<br>
                    {$order.dateof|dateformat:"@time"}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет ни одного заказа{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}