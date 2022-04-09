{$shop_config = ConfigLoader::byModule('shop')}
{$catalog_config = ConfigLoader::byModule('catalog')}
{$offers_data = $product->getOffersJson(['noVirtual' => true], true)}
{$only_main_offer = $THEME_SETTINGS.show_offers_in_list}

<div class="item-list rs-product-item
                {if !$product->isAvailable($only_main_offer)} rs-not-avaliable{/if}
                {if $product->canBeReserved()} rs-can-be-reserved{/if}
                {if $product.reservation == 'forced'} rs-forced-reserve{/if}" {$product->getDebugAttributes()} data-id="{$product.id}">

    <div class="item-list__main">
        <div class="position-relative item-list__image">
            {$spec_dirs = $product->getMySpecDir()}
            {if $spec_dirs}
                <div class="item-product-labels js-product-labels">
                    <ul>
                        {foreach $spec_dirs as $spec}
                            {if $spec.is_label}
                                <li class="item-product-label item-product-label_{$spec.alias}" style="color:{$spec.label_text_color}; background-color: {$spec.label_bg_color}; border-color: {$spec.label_border_color}">{$spec.name}</li>
                            {/if}
                        {/foreach}
                    </ul>
                    <button class="item-product-labels-btn d-none" type="button">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.86423 4.60983C3.01653 4.46339 3.26347 4.46339 3.41577 4.60983L6 7.09467L8.58423 4.60983C8.73653 4.46339 8.98347 4.46339 9.13577 4.60983C9.28808 4.75628 9.28808 4.99372 9.13577 5.14017L6.27577 7.89017C6.12347 8.03661 5.87653 8.03661 5.72423 7.89017L2.86423 5.14017C2.71192 4.99372 2.71192 4.75628 2.86423 4.60983Z" fill="#1B1B1F"/>
                        </svg>
                    </button>
                </div>
            {/if}


            <a href="{$product->getUrl()}" class="item-product-img rs-to-product">
                <canvas width="268" height="268"></canvas>
                <img src="{$product->getMainImage()->getUrl(268, 268)}"
                     srcset="{$product->getMainImage()->getUrl(536, 536)} 2x" loading="lazy" alt="{$product.title}" class="rs-image">
            </a>
            <div class="item-list__barcode">{t}Артикул{/t} {$product.barcode}</div>
        </div>
        <div class="col">
            <a href="{$product->getUrl()}" class="item-list__title rs-to-product">{$product.title}</a>
            {if $THEME_SETTINGS.show_rating}
                <a href="{$product->getUrl()}" class="item-product-reviews rs-to-product">
                    <div class="item-product-rating">
                        <img width="24" height="24" src="{$THEME_IMG}/icons/star{if $product->getRatingBall() > 0}-active{/if}.svg" alt="">
                        {if $product->getRatingBall()}
                            <div>{$product->getRatingBall()}</div>
                        {/if}
                    </div>
                    <div>
                        {if $product->getCommentsNum()}
                            {t n=$product->getCommentsNum()}%n отзывов{/t}
                        {else}
                            {t}нет отзывов{/t}
                        {/if}
                    </div>
                </a>
            {/if}
            {if $list_properties = $product->getListProperties()}
                <ul class="item-product-chars item-product-chars_list">
                    {foreach $list_properties as $prop}
                        {$value = $prop->textView()}
                        {if $value !== ""}
                            <li>
                                <span class="text-gray pe-1 bg-body">{$prop.title}{if $prop.unit}({$prop.unit}){/if}</span>
                                <span class="ms-2 bg-body">{$value}</span>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}

            {if $THEME_SETTINGS.enable_short_description_in_product_card}
                <div class="mt-4 fs-5 text-gray col-md-7">
                    <div>{$product.short_description}</div>
                </div>
            {/if}
        </div>
    </div>
    <div class="item-list__bar rs-offers-preview">
        <div>
            <div class="item-product-price mb-3 mb-md-4">
                {$old_cost = $product->getOldCost()}
                {$new_cost = $product->getCost()}
                <div class="item-product-price__new-price">
                    <span class="rs-price-new">{$new_cost}</span> {$product->getCurrency()}
                    {if $catalog_config.use_offer_unit && $product->isOffersUse()}
                        <span class="rs-unit-block">/ <span class="rs-unit">{$product->getMainOffer()->getUnit()->stitle}</span></span>
                    {/if}
                </div>
                {if $old_cost && $new_cost != $old_cost}
                    <div class="item-product-price__old-price">
                        <span class="rs-price-old">{$old_cost}</span> {$product->getCurrency()}</div>
                {/if}
            </div>
            <div class="row g-3 align-items-center">

                <div class="col-auto">
                    {include file="%catalog%/product_cart_button.tpl"}
                </div>

                <div class="col d-flex">
                    {if $THEME_SETTINGS.enable_favorite}
                        <a class="fav me-md-3 me-2 rs-favorite {if $product->inFavorite()}rs-in-favorite{/if}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.2131 5.5617L12 6.5651L12.7869 5.56171C13.5614 4.57411 14.711 4 15.9217 4C18.1262 4 20 5.89454 20 8.32023C20 10.2542 18.8839 12.6799 16.3617 15.5585C14.6574 17.5037 12.8132 19.0666 11.9999 19.7244C11.1866 19.0667 9.34251 17.5037 7.63817 15.5584C5.1161 12.6798 4 10.2542 4 8.32023C4 5.89454 5.87376 4 8.07829 4C9.28909 4 10.4386 4.57407 11.2131 5.5617ZM11.6434 20.7195L11.7113 20.6333L11.6434 20.7195Z" stroke-width="1"/>
                            </svg>
                        </a>
                    {/if}
                    {if $THEME_SETTINGS.enable_compare}
                        <a class="comp rs-compare{if $product->inCompareList()} rs-in-compare{/if}" data-title="{t}сравнить{/t}" data-already-title="{t}В сравнении{/t}">
                            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.1279 18.0433V18.5433H19.6279H19.9688L19.9689 18.5433C19.9692 18.5433 19.9693 18.5433 19.97 18.5436C19.9713 18.5442 19.975 18.5462 19.9798 18.5513C19.9895 18.5616 20 18.581 20 18.6095C20 18.638 19.9895 18.6574 19.9798 18.6677C19.975 18.6728 19.9713 18.6748 19.97 18.6754C19.9693 18.6757 19.9692 18.6757 19.9689 18.6757L19.9688 18.6757H4.03125L4.03109 18.6757C4.03077 18.6757 4.03069 18.6757 4.02996 18.6754C4.02867 18.6748 4.02498 18.6728 4.02023 18.6677C4.01055 18.6574 4 18.638 4 18.6095C4 18.581 4.01055 18.5616 4.02023 18.5513C4.02498 18.5462 4.02867 18.5442 4.02996 18.5436C4.03069 18.5433 4.03077 18.5433 4.03109 18.5433L4.03125 18.5433H4.37236H4.87236V18.0433V10.7968C4.87236 10.7683 4.88291 10.7489 4.89259 10.7385C4.89734 10.7335 4.90103 10.7315 4.90232 10.7309C4.90315 10.7305 4.90314 10.7306 4.90361 10.7306H8.14403C8.14409 10.7306 8.14414 10.7306 8.14419 10.7306C8.14451 10.7306 8.14459 10.7306 8.14532 10.7309C8.14661 10.7315 8.1503 10.7335 8.15505 10.7385C8.16473 10.7489 8.17528 10.7683 8.17528 10.7968V18.0433V18.5433H8.67528H9.84867H10.3487V18.0433V8.15454C10.3487 8.12606 10.3592 8.10665 10.3689 8.09633C10.3737 8.09127 10.3773 8.08926 10.3786 8.08868C10.379 8.08852 10.3792 8.08844 10.3793 8.0884C10.3795 8.08835 10.3797 8.08836 10.3799 8.08836H13.6203C13.6208 8.08836 13.6208 8.08831 13.6216 8.08868C13.6229 8.08926 13.6266 8.09127 13.6314 8.09633C13.641 8.10665 13.6516 8.12606 13.6516 8.15454V18.0433V18.5433H14.1516H15.325H15.825V18.0433V5.51247C15.825 5.48398 15.8355 5.46457 15.8452 5.45425C15.85 5.44919 15.8537 5.44719 15.8549 5.44661C15.8553 5.44643 15.8555 5.44635 15.8557 5.44632C15.8559 5.44627 15.856 5.44629 15.8562 5.44629H19.0967L19.0968 5.44629C19.0971 5.44628 19.0972 5.44628 19.0979 5.44661C19.0992 5.44719 19.1029 5.44919 19.1077 5.45425C19.1173 5.46457 19.1279 5.48398 19.1279 5.51247V18.0433Z" />
                            </svg>
                        </a>
                    {/if}
                </div>
            </div>
        </div>
    </div>
    {if $THEME_SETTINGS.show_offers_in_list}
        {if $offers_data}
            <script rel="offers" type="application/json" data-check-quantity="{$shop_config->check_quantity}">{$offers_data|json_encode:320}</script>
        {/if}
    {/if}
</div>