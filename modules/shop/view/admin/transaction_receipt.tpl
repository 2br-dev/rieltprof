{if $cell->getRow()->receipt == 'fail'}
    <span style="color: red;">{$cell->getValue()}</span>
{else}
    {$cell->getValue()}
{/if}