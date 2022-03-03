{if $field->isOnlyExists() && $elem.id < 1}
    <div class="notice">{t}Управление взаимосвязями возможно только в режиме редактирования объекта{/t}</div>
{else}
    {moduleinsert name="\Crm\Controller\Admin\Block\InteractionBlock" link_id=$elem.id link_type=$field->getLinkType() from_call=$elem.create_from_call}
{/if}