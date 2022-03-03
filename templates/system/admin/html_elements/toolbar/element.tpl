{foreach from=$toolbar->getItems() key=num item=item}
    {include file=$item->getTemplate() button=$item}
{/foreach}