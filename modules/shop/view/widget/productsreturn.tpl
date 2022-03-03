{* Виджет: Недавние заказы *}
<div class="widget-filters">
    <div class="dropdown">
        <a id="last-order-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $filter=='new'}{t}новые{/t}{else}{t}все{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="last-order-switcher">
            <li{if $filter=='new'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-productsreturn" filter="new"}" class="call-update">{t}Новый{/t}</a></li>
            <li{if $filter=='all'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-productsreturn" filter="all"}" class="call-update">{t}Все{/t}</a></li>
        </ul>
    </div>
</div>

{if count($list)}
    <table class="wtable mrg overable table-lastorder">
        <tbody>
            {foreach $list as $productsreturn}
                <tr class="clickable crud-edit" data-crud-options='{ "updateThis": true }' data-url="{adminUrl mod_controller="shop-returnsctrl" do="edit" id=$productsreturn.id}">
                    <td class="number f-14">
                        <div class="title">
                            <span title="{$productsreturn.__status->textView()}" class="w-point{if $productsreturn.status=='new'} bg-red{else} bg-gray{/if}"></span>
                            <b>{t num=$productsreturn.return_num}Возврат №%num{/t}</b>
                        </div>
                        <div class="price">{$productsreturn.cost_total|format_price} {$productsreturn.currency_stitle}</div>
                    </td>
                    <td class="w-date">
                        {$productsreturn.dateof|dateformat:"%e %v %!Y"}<br>
                        {$productsreturn.dateof|dateformat:"@time"}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет ни одного возврата{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}