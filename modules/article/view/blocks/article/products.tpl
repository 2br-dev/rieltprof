{if !empty($products)}
    {nocache}
        {addjs file="%catalog%/rscomponent/productslider.js"}
    {/nocache}
    <div class="mt-5">
        <h2>{t}Прикреплённые товары{/t}</h2>
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
    </div>
{/if}