{if $products}
    {nocache}
        {addjs file="%catalog%/rscomponent/productslider.js"}
    {/nocache}
    <div class="h1 mb-4">{if $block_title}{$block_title}{else}<a href="{$dir->getUrl()}" class="text-decoration-none text-dark">{$dir.name}</a>{/if}</div>
    <div class="product-slider">
        <div class="product-slider__container">
            <div class="swiper-container swiper-products">
                <div class="swiper-wrapper" >
                    {foreach $products as $product}
                        <div class="swiper-slide">
                            {include file="%catalog%/one_product.tpl"}
                        </div>
                    {/foreach}
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </div>
{else}
    <div class="h1 mb-4">{t}Товары{/t}</div>

    {capture assign = "skeleton_html"}
        <div class="item-card-container">
            <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-md-3 row-cols-2 g-0">
                <div>
                    <img class="w-100" width="300" height="509" src="{$THEME_IMG}/skeleton/skeleton-card.svg" alt="">
                </div>
                <div>
                    <img class="w-100" width="300" height="509" src="{$THEME_IMG}/skeleton/skeleton-card.svg" alt="">
                </div>
                <div class="d-md-block d-none">
                    <img class="w-100" width="300" height="509" src="{$THEME_IMG}/skeleton/skeleton-card.svg" alt="">
                </div>
                <div class="d-xl-block d-none">
                    <img class="w-100" width="300" height="509" src="{$THEME_IMG}/skeleton/skeleton-card.svg" alt="">
                </div>
                <div class="d-xxl-block d-none">
                    <img class="w-100" width="300" height="509" src="{$THEME_IMG}/skeleton/skeleton-card.svg" alt="">
                </div>
            </div>
        </div>
    {/capture}

    {include "%THEME%/helper/usertemplate/include/block_stub.tpl"
    name = "{t}Товары{/t}"
    skeleton_html = $skeleton_html
    do = [
        [
            'title' => "{t}Добавить категорию с товарами{/t}",
            'href' => "{adminUrl do=false mod_controller="catalog-ctrl"}"
        ],
        [
            'title' => "{t}Настроить блок{/t}",
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}