<div class="chkhead-block">
    <input type="checkbox" title="{t}Выделить элементы на этой странице{/t}" class="chk_head select-page" data-name="{$cell->getName()}">
    {if $cell->property.showSelectAll}
    <div class="onover">
        <input type="checkbox" name="selectAll" value="on" class="select-all" title="{t}Выделить элементы на всех страницах{/t}">
    </div>
    {/if}
</div>