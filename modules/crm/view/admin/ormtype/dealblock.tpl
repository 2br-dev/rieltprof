{if $field->isOnlyExists() && $elem.id < 1}
    <div class="notice">{t}Управление сделками возможно только в режиме редактирования объекта{/t}</div>
{else}
    {moduleinsert name="\Crm\Controller\Admin\Block\DealBlock" link_id=$elem.id link_type=$field->getLinkType()}
{/if}