{$user=$cell->getUser()}
{if $user->id > 0}
    {if $current_user.id == $user->id}{t}Вы, {/t}{/if}
    <a href="{adminUrl do=edit id=$user.id mod_controller="users-ctrl"}" class="crud-edit">{$user->getFio()} ({$user->id})</a>
{else}
    {t}Не назначен{/t}
{/if}