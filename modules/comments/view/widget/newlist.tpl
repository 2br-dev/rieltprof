{$config=ConfigLoader::byModule('comment')}
{if $list}
    <table class="wtable mrg overable">
        <tbody>
            {foreach from=$list item=item}
                {$item_time=strtotime($item.dateof)}
                {$type = $item->getTypeObject()}

                <tr class="clickable crud-edit {if (!$config.need_moderate && ($item_time<$time && $item_time>$day_before_time_int) && !$item.moderated) || ($config.need_moderate && !$item.moderated)}highlight{/if}" data-crud-options='{ "updateThis": true }' data-url="{adminUrl mod_controller="comments-ctrl" do="edit" id=$item.id}">
                    <td>
                        <div class="m-b-5 f-11 c-black">{$item.dateof|dateformat:"%datetime"}</div>
                        <p class="m-b-5">{$item.message|teaser:"250"}</p>
                        {if $type}
                            {$admin_object_url = $type->getAdminUrl()}
                            <p><a class="link-ul" {if $admin_object_url}href="{$admin_object_url}" onclick="event.stopPropagation(); return;"{/if}>{$type->getLinkedObjectTitle()}</a></p>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет комментариев{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}