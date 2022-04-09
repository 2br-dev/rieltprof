{if $cell->getRow('is_admin')}
<div class="admin lgbox"></div>
{elseif !$cell->getRow('processed')}
<div class="new lgbox"></div>
{else}
<div class="user lgbox"></div>
{/if}