{if $cell}
    {$links = $cell->getRow()->getLinkedDocuments()}
{else}
    {$links = $elem->getLinkedDocuments()}
{/if}

{if $links}
    {foreach $links as $link}
        {$data = $link->getData()}
        <a href="{$data.link}" class="crud-edit">{$data.title} №{$link.document_id}</a>
        <br>
    {/foreach}
{else}
    {t}Нет{/t}
{/if}