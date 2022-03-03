{foreach from=$fcontainer->getLines() item=line}
    {$line->getView()}
{/foreach}