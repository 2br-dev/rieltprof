<div id="bestsellers-dialog">
    <div class="bestsellers-container">
        {if $items}
            {* Выводим информацию из кэша *}
            {include file="%main%/widget/bestsellers_items.tpl"}
        {/if}
    </div>
</div>