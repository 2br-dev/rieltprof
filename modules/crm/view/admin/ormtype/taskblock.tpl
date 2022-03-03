{if $field->isOnlyExists() && $elem.id < 1}
    <div class="notice">{t}Управление задачами возможно только в режиме редактирования объекта{/t}</div>
{else}
    {moduleinsert name="\Crm\Controller\Admin\Block\TaskBlock" link_id=$elem.id link_type=$field->getLinkType()}
{/if}