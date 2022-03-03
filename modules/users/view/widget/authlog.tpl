<div class="widget-ph">
    <table class="wtable">
        <thead>
            <tr>
                <th>{t}Дата{/t}</th>
                <th>{t}Логин/Имя{/t}</th>
                <th>{t}IP{/t}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $list as $event}
            <tr>
            {$user=$event->getObject()}
                <td class="f-11">
                    {$event->getEventDate()|date_format:"%d.%m.%Y"}<br>
                    <span style="color:#ACACAC">{$event->getEventDate()|date_format:"%H:%M"}</span>
                </td>
                <td class="f-10">
                    <a class="link-ul m-r-10" href="{adminUrl mod_controller="users-ctrl" do="edit" id=$user.id}">{$user.login}</a>
                    <span>{$user.name} {$user.surname}</span>
                </td>
                <td>{$event->getIP()}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}