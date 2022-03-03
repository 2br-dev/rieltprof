{addjs file="%marketplace%/newmodules.js"}
{addcss file="%marketplace%/newmodules.css"}

<div id="mp-modules" {if !$items}class="need-refresh"{/if} data-url="{adminUrl mod_controller="marketplace-widget-newmodules" mpdo="getItems"}">
    <div class="mp-container">
        {if $items}
            {* Выводим информацию из кэша *}
            {include file="widget/newmodules/newmodules_item.tpl"}
        {/if}
    </div>
    <div class="empty-widget loading {if $items}hidden{/if}">
        {t}Загрузка...{/t}
    </div>
</div>