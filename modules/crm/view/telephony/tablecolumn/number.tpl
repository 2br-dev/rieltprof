{$call_history = $cell->getRow()}
{if $call_history->call_flow == 'in'}
    <i class="zmdi zmdi-phone c-green f-18" title="{t}Входящий{/t}"></i> {$call_history->caller_number}
{else}
    <i class="zmdi zmdi-phone-forwarded c-orange f-18" title="{t}Исходящий{/t}"></i> {$call_history->called_number}
{/if}