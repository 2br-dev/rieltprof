<div class="widget-filters">
    <div class="dropdown">
        <a id="reservation-switcher" data-toggle="dropdown" class="widget-dropdown-handle">
            {if $filter=='open'}{t}открытые{/t}{else}{t}закрытые{/t}{/if} <i class="zmdi zmdi-chevron-down"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="reservation-switcher">
            <li{if $filter=='open'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-reservation" filter="open"}" class="call-update">{t}Открытые{/t}</a></li>
            <li{if $filter=='close'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-reservation" filter="close"}" class="call-update">{t}Закрытые{/t}</a></li>
        </ul>
    </div>
</div>

{if count($list)}
    <table class="wtable mrg overable clickable">
        <tbody>
            {foreach from=$list item=item}
                <tr class="crud-edit clickable" data-crud-options='{ "updateThis": true }' data-url="{adminUrl mod_controller="shop-reservationctrl" do="edit" id=$item.id}">
                    <td class="f-14">
                        <div class="title">
                            <span title="{$item.__status->textView()}" class="w-point{if $item.status == 'open'} bg-deeporange{else} bg-green{/if}"></span>
                            <b>№ {$item.id}</b>
                        </div>
                        <div class="sub-title">{$item.product_title}<br/>{$item.phone}</div>
                    </td>
                    <td class="w-date">
                        {$item.dateof|dateformat:"%e %v %!Y"}<br>
                        {$item.dateof|dateformat:"@time"}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет ни одной предзаказа{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}