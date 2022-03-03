{$call = $cell->getRow()}
{if $url=$call->getRecordUrl()}
    <div class="ui360"><a href="{$url}"></a></div>
{else}
    {t}Нет{/t}
{/if}