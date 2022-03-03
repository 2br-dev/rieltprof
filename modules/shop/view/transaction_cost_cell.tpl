{if $cell->getRow()->order_id}
    <span style="color:brown">
        <b>{$cell->getValue()}</b>
    </span>
{else}
    <span style="color:green">
        <b>{$cell->getValue()}</b>
    </span>
{/if}