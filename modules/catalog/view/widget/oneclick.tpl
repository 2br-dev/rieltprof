{* Виджет покупки в 1 клик *}
<div class="widget-filters">
    <div class="dropdown">
        <a id="oneclick-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $filter=='new'}{t}новые{/t}{else}{t}закрытые{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="oneclick-switcher">
            <li{if $filter=='new'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="catalog-widget-oneclick" filter="new"}" class="call-update">{t}Новые{/t}</a></li>
            <li{if $filter=='viewed'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="catalog-widget-oneclick" filter="viewed"}" class="call-update">{t}Закрытые{/t}</a></li>
        </ul>
    </div>
</div>

{if count($list)}
    <table class="wtable mrg overable">
        <tbody>
            {foreach $list as $item}
                <tr class="clickable crud-edit" data-crud-options='{ "updateThis": true }' data-url="{adminUrl mod_controller="catalog-oneclickctrl" do="edit" id=$item.id}">
                    <td class="f-14 p-b-15">
                        <span title="{$item.__status->textView()}" class="w-point{if $item.status == 'new'} bg-orange{else} bg-lime{/if}"></span> <b>№ {$item.id}</b>
                        {$data = $item->tableDataUnserialized()}

                        <ul class="w-list">
                            {foreach $data as $item_data}
                                <li>{$item_data.title}</li>
                            {/foreach}
                        </ul>
                        <div><b>{$item.user_fio}, {$item.user_phone}</b></div>
                    </td>
                    <td class="w-date">
                        {$item.dateof|dateformat:"@date"}<br>
                        {$item.dateof|dateformat:"@time"}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет ни одной покупки в 1 клик{/t}
    </div>
{/if}
{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}