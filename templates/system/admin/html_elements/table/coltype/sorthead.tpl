{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{if !empty($cell->property.Sortable) && !$cell->property.CurrentSort}   
    <a href="{$cell->sorturl}" class="call-update sortable sortdot {$cell->property.CurrentSort|lower}"><span></span></a>
{else}                 
    <span class="sortable sortdot {$cell->property.CurrentSort|lower}"><span></span></span> 
{/if}