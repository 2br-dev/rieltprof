{$call_history = $cell->getRow()}
{$user = $call_history->getOtherUser()}
{if $user.id}
    <a href="{adminUrl do="edit" id=$user.id mod_controller="users-ctrl"}" class="crud-edit">{$user->getFio()} ({$user.id})</a>
{else}
    {$user->getFio()}
{/if}