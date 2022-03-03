{$call_history = $cell->getRow()}
{$call_history.__call_status->textView()}{if $call_history.call_status == 'HANGUP'}. {$call_history.__call_sub_status->textView()}{/if}