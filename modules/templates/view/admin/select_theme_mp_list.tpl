{foreach $theme_mp_list as $name => $item}
    <div class="theme{if $item.shades} has-shades{/if}">
        <div class="title">{$item.title|default:t("Неизвестно")}</div>
        <div class="preview">
            <a class="img"><img class="image" src="{$item.preview}"></a>
            <div class="price">
                {if $item.old_price>0 && $item.old_price != $item.price}<del class="f-11">{$item.old_price|format_price} {$item.price_currency}</del>&nbsp;&nbsp;{/if}
                {if $item.price == 0}{t}бесплатно{/t}{else}{$item.price|format_price} {$item.price_currency}{/if}
            </div>
        </div>
        <div class="select-block">
            {if $item.shades}
                <div class="colors">
                    {foreach $item.shades as $shade}
                        <a class="item{if $item.default_shade_id == $shade.id} act{/if}" style="background: {$shade.color}" title="{$shade.title}" data-shade-id="{$shade.id}" data-preview-url="{$shade.preview}"><i></i></a>
                    {/foreach}
                </div>
            {/if}
            <a href="{adminUrl do=false mod_controller="marketplace-ctrl"}#{$item.url}" class="select">{t}Установить{/t}</a>
        </div>
    </div>
{foreachelse}
    <div class="no-themes">
        {t}Нет неустановленных тем оформления{/t}
    </div>
{/foreach}