{* Рекомендуемые товары *}
{if $recommended}
    {nocache}
        {addjs file="%catalog%/rscomponent/productslider.js"}
    {/nocache}
    <div class="h1 mb-4">{$title|default:"{t}С этим товаром покупают{/t}"}</div>
    <div class="product-slider">
        <div class="product-slider__container">
            <div class="swiper-container swiper-products">
                <div class="swiper-wrapper">
                    {foreach $recommended as $product}
                        <div class="swiper-slide">
                            {include file="%catalog%/one_product.tpl" product = $product}
                        </div>
                    {/foreach}
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </div>
{/if}