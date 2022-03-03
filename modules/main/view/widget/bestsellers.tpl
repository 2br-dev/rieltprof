{addcss file="common/owlcarousel/owl.carousel.min.css" basepath="common"}
{addcss file="common/owlcarousel/owl.theme.default.min.css" basepath="common"}
{addjs file="owlcarousel/owl.carousel.min.js"}
{addcss file="%main%/bestsellers.css"}
{addjs file="%main%/bestsellers.js"}

<div id="bestsellers" class="{if !$error && !$items}need-refresh{/if}"
        data-need-show-dialog="{$need_show_dialog}"
        data-url="{adminUrl mod_controller="main-widget-bestsellers" bsdo="getItems"}"
        data-url-dialog="{adminUrl mod_controller="main-widget-bestsellers" bsdo="getDialog"}">
    <div class="bestsellers-container">
        {if $items}
            {* Выводим информацию из кэша *}
            {include file="%main%/widget/bestsellers_items.tpl"}
        {/if}
    </div>

    <div class="empty-widget loading {if $items}hidden{/if}">
        {t}Загрузка...{/t}
    </div>
</div>