{$deal=$cell->getRow()}
{$user=$deal->getClientUser()}
{$fio=$user->getFio()}

{if $fio}
    {if $user.id > 0}
        <a href="{adminUrl do=edit id=$user.id mod_controller="users-ctrl"}" class="crud-edit">{$fio} ({$user.id})</a>
    {else}
        {$fio}
    {/if}
{else}
    {t}Не задано{/t}
{/if}
