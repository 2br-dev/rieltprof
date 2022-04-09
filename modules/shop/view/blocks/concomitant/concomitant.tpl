{if $list}
    <div class="h3">{t}Купить вместе с товаром:{/t}</div>
    <div class="product-accessories rs-product-concomitant" data-currency="{$current_product->getCurrency()}">
        <div class="swiper-accessories swiper-container">
            <div class="swiper-wrapper">
                {foreach $list as $product}
                <div class="swiper-slide">
                    <div class="product-accessories__item">
                        <a class="product-accessories__item-link">
                            <div class="product-accessories__item-img">
                                <img src="{$product->getMainImage(62, 62)}" srcset="{$product->getMainImage(124, 124)} 2x" loading="lazy" alt="{$product.title}">
                            </div>
                            <div>{$product.title}</div>
                        </a>
                        <div class="row row-cols-auto justify-content-between g-2">
                            <div class="d-flex align-items-center text-nowrap">
                                <div class="fw-bold">+{$product->getCost()} {$product->getCurrency()}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="concomitantItem{$product.id}" name="concomitant[]"
                                           value="{$product.id}" data-price="{$product->getCost(null, null, false)}" title="{t}Добавить к товару{/t}">
                                    <label class="form-check-label" for="concomitantItem{$product.id}"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
{/if}