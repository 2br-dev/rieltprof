{* Все бренды в системе *}
<h1 class="mb-4">{t}Бренды{/t}</h1>
{if $brands}
    <div class="row row-cols-xl-6 row-cols-md-4 row-cols-sm-3 row-cols-2 g-3">
        {foreach $brands as $brand}
            <div {$brand->getDebugAttributes()}>
                <a class="brand-item{if $THEME_SETTINGS.force_white_background_brand} white-bg{/if}" href="{$brand->getUrl()}">
                    <div class="brand-item__img">
                        {if $brand.image}
                            <img srcset="{$brand->__image->getUrl(324, 108,'xy')} 2x" src="{$brand->__image->getUrl(162, 54,'xy')}" loading="lazy" alt="{$brand.title}">
                        {else}
                            <img src="{$THEME_IMG}/decorative/brand-empty.svg" alt="{$brand.title}">
                        {/if}
                    </div>
                    <div class="brand-item__title">{$brand.title}</div>
                </a>
            </div>
        {/foreach}
    </div>
{else}
    {include file="%THEME%/helper/usertemplate/include/empty_list.tpl" reason="{t}Нет ни одного бренда{/t}"}
{/if}