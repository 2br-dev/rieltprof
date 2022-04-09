{$status=$cell->getRow()->status}
{$color='#000080'}
{if $status == 'success'}
    {$color='green'}
{elseif $status == 'fail'}
    {$color='red'}
{/if}
<span style="color: {$color}">{$cell->getValue()}</span>
