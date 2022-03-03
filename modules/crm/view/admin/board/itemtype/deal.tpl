<div class="crm-column-item-title">
    <span><a href="{adminUrl do="edit" id=$item.id mod_controller="crm-dealctrl"}" class="crud-edit u-link">{$item.deal_num}</a></span>
    <strong>{$item.title}</strong>
</div>

<span>{t}Создано{/t}: {$item.date_of_create|dateformat:"@date @time"}</span>
{if $item.date_of_planned_end}
    <br><span>{t}Выполнить до{/t}:
    <span class="c-{$item->getPlannedEndStatus()}" title="{$item->getPlannedEndStatusTitle()}">{$item.date_of_planned_end|dateformat:"@date @time"}</span>
    </span>
{/if}
{if $item.cost>0}
    <br><span>{t}Сумма:{/t} {$item.cost|format_price}</span>
{/if}
{$client_user=$item->getClientUser()}
{$client_name=$client_user->getFio()}
{if $client_name}
    <br><span>{t}Клиент{/t}: {$client_name} {if $client_user.id}({$client_user.id}){/if}</span>
{/if}

{if $item.manager_id}
    {$creator=$item->getCreatorUser()}
    <br><span>{t}Создатель{/t}: {$creator->getFio()} ({$creator.id})</span>
{/if}