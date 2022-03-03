<div class="tools">
    {foreach from=$cell->getActions() item=item}
        {if !$item->isHidden()}
            {include file=$item->getTemplate() tool=$item}
        {/if}
    {/foreach}
</div>