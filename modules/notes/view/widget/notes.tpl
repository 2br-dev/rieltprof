<div class="widget-filters">
    <div class="dropdown">
        {$value=$notes_filter_creator}
        <a id="notes-switcher" data-toggle="dropdown" class="widget-dropdown-handle">
            {if $value=='my'}{t}Только мои{/t}
            {else}{t}все{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>

        <ul class="dropdown-menu" aria-labelledby="notes-switcher">
            <li{if $value=='my'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_filter_creator="my"}" class="call-update">{t}Только мои{/t}</a></li>
            <li{if $value=='all'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_filter_creator="all"}" class="call-update">{t}Все{/t}</a></li>
        </ul>
    </div>

    <div class="dropdown">
        {$value=$notes_filter_status}
        <a id="notes-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $value=='closed'}{t}завершенные{/t}{elseif $value=='unclosed'}{t}незавершенные{/t}{else}{t}любой статус{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="notes-switcher">
            <li{if $value=='all'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_filter_status="all"}" class="call-update">{t}все{/t}</a></li>
            <li{if $value=='closed'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_filter_status="closed"}" class="call-update">{t}завершенные{/t}</a></li>
            <li{if $value=='unclosed'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_filter_status="unclosed"}" class="call-update">{t}незавершенные{/t}</a></li>
        </ul>
    </div>

    <div class="dropdown">
        {$value=$notes_sort}
        <a id="notes-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $notes_sort=='update'}{t}по дате обновления{/t}{else}{t}по дате создания{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="notes-switcher">
            <li{if $value=='update'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_sort="update"}" class="call-update">{t}по дате обновления{/t}</a></li>
            <li{if $value=='create'} class="act"{/if}><a data-update-url="{adminUrl mod_controller="notes-widget-notes" notes_sort="create"}" class="call-update">{t}по дате создания{/t}</a></li>
        </ul>
    </div>
</div>

{if count($notes)}
    <table class="wtable mrg overable table-lastorder">
        <tbody>
        {foreach $notes as $note}
            <tr data-url="{adminUrl mod_controller="notes-notectrl" do="edit" id=$note.id context="widget"}" data-crud-options='{ "updateThis": true }' class="clickable crud-edit">
                <td width="20">
                    <span title="{$note.__status->textView()}" class="f-21 zmdi
                    {if $note.status=="open"}zmdi-circle-o c-red{elseif $note.status=="inwork"}zmdi-time c-amber{else}zmdi-check-all c-green{/if}"></span>
                </td>
                <td class="f-14">
                    {if $note.is_private}<i class="zmdi zmdi-shield-security m-r-5" title="{t}Видна только мне{/t}"></i>{/if} <b>{$note.title}</b>
                    {if $notes_filter_creator == 'all'}
                    <br><small>{t}Автор{/t}: {$note->getCreatorUser()->getFio()}</small>
                    {/if}
                </td>
                <td class="w-date text-nowrap">
                    {if $notes_sort == 'update'}
                        <span title="{t}Обновлено{/t}">{$note.date_of_update|dateformat:"%e %v %!Y"}<br>
                        {$note.date_of_update|dateformat:"@time"}</span>
                    {else}
                        <span title="{t}Создано{/t}">{$note.date_of_create|dateformat:"%e %v %!Y"}<br>
                            {$note.date_of_create|dateformat:"@time"}</span>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет ни одной заметки{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}