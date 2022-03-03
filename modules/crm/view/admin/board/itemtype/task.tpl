<div class="crm-column-item-title">
    <span><a href="{adminUrl do="edit" id=$item.id mod_controller="crm-taskctrl"}" class="crud-edit u-link">{$item.task_num}</a></span>
    <strong>{$item.title}</strong>
</div>

<span>{t}Создано{/t}: {$item.date_of_create|dateformat:"@date @time"}</span>
{if $item.date_of_planned_end}
    <br><span>{t}Выполнить до{/t}:
    <span class="c-{$item->getPlannedEndStatus()}" title="{$item->getPlannedEndStatusTitle()}">{$item.date_of_planned_end|dateformat:"@date @time"}</span>
    </span>
{/if}
{if $item.implementer_user_id}
    {$implementer=$item->getImplementerUser()}
    <br><span>{t}Исполнитель{/t}: {$implementer->getFio()} ({$implementer.id})</span>
{/if}