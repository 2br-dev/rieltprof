{if $topics}
    <table class="wtable mrg overable">
        <tbody>
        {foreach $topics as $topic}
            <tr onclick="location.href='{adminUrl do=false mod_controller="support-supportctrl" id=$topic.id}'" class="clickable">
                {$user=$topic->getUser()}
                <td>
                    <span title="{if $topic.newadmcount>0}{t}есть новые сообщения{/t}{else}{t}прочитано{/t}{/if}" class="w-point {if $topic.newadmcount>0} bg-red {else} bg-gray{/if}"></span>&nbsp; {$topic.title}
                </td>
                <td class="w-date">
                    {$topic.updated|dateformat:"%e %v %!Y"}<br>
                    {$topic.updated|dateformat:"@time"}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет сообщений{/t}
    </div>
{/if}    

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}