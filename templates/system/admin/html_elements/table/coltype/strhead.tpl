{if !empty($cell->property.Sortable)}
    <a href="{$cell->sorturl}" class="call-update sortable {$cell->property.CurrentSort|lower}">{$cell->getTitle()}</a>
{else}
    {$cell->getTitle()}
{/if}