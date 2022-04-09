{* Шаблон карточки товара *}
{addjs file="%catalog%/rscomponent/product.js"}

{$shop_config = ConfigLoader::byModule('shop')}
{$check_quantity = $shop_config.check_quantity}
{$catalog_config = $this_controller->getModuleConfig()}

{* Загружаем все сведения о комплектациях из кэш данных *}
{$offers_data = $product->getOffersJson(['images' => [], 'disableCheckOffers' => true], true)}

<div class="product-variant-{$THEME_SETTINGS.product_card_view_type}
            product-variant-tab-{$THEME_SETTINGS.product_tabs_view_type}
            {if $THEME_SETTINGS.enable_product_zoom}rs-zoom{/if} rs-product
            {if !$product->isAvailable()} rs-not-avaliable{/if}
            {if $product->canBeReserved()} rs-can-be-reserved{/if}
            {if $product.reservation == 'forced'} rs-forced-reserve{/if}"
            data-id="{$product.id}">

    <div class="mb-sm-4 mb-3">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-auto d-flex flex-lg-column align-items-center align-items-lg-end justify-content-between">
                    {if $product.barcode}
                        <div class="fs-5 text-gray order-lg-last">{t}Артикул{/t} <span class="rs-product-barcode">{$product.barcode}</span></div>
                    {/if}
                    {if $product.brand_id}
                        {$brand = $product->getBrand()}
                        {if $brand.image}
                            <a class="product-brand ms-3 ms-lg-0 mb-lg-3" href="{$brand->getUrl()}">
                                <img src="{$brand.__image->getUrl(77, 40)}" srcset="{$brand.__image->getUrl(154, 80)} 2x"
                                     alt="{$brand.title}" title="{t brand=$brand.title}Бренд: %brand{/t}">
                            </a>
                        {/if}
                    {/if}
                </div>
                <div class="col order-lg-first">
                    <h1 class="mb-lg-4">{$product.title}</h1>
                    <div class="row align-items-center row-cols-auto g-3 gx-5">
                        {if $THEME_SETTINGS.review_enabled}
                            <div class="d-flex align-items-center">
                            <div class="rating-stars me-2">
                                <div class="rating-stars__act" style="width: {$product->getRatingPercent()}%;"></div>
                            </div>
                            <a href="#tab-comments" class="item-product-reviews rs-go-to-tab">
                                {if $product->getRatingBall() > 0}
                                    <div class="item-product-rating">{$product->getRatingBall()}</div>
                                {/if}
                                <div>
                                    {if $product.comments}
                                        {verb item=$product->getCommentsNum() values="отзыв,отзыва,отзывов"}
                                    {else}
                                        {t}нет отзывов{/t}
                                    {/if}
                                </div>
                            </a>
                        </div>
                        {/if}
                        {$spec_dirs = $product->getMySpecDir()}
                        {if $spec_dirs}
                            <div>
                                <ul class="product-labels">
                                    {foreach $spec_dirs as $spec}
                                        {if $spec.is_label}
                                            <li class="item-product-label item-product-label_{$spec.alias}" style="color:{$spec.label_text_color}; background-color: {$spec.label_bg_color}; border-color: {$spec.label_border_color}">{$spec.name}</li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-6">
        <div class="container">
            <div class="row g-5 gx-4">
                {* Подготавливаем разные размеры картинок для разных вариантов отображения *}
                {if $THEME_SETTINGS.product_card_view_type == 'first'}
                    {$image_box = 484}
                    {$preview_image_box = 62}
                {else}
                    {$image_box = 866}
                    {$preview_image_box = 101}
                {/if}
                <div class="variant-product-gallery">
                    {hook name="catalog-product:images" title="{t}Карточка товара:изображения{/t}"}
                        <div class="product-gallery">
                            <div class="swiper-container product-gallery-top">
                                {$images = $product->getImages()}
                                {if !$images}{$images=[$product->getImageStub()]}{/if}
                                <div class="swiper-wrapper">
                                    {foreach $images as $image}
                                        <div class="swiper-slide" data-image-id="{$image.id}">
                                            <div class="swiper-zoom-container">
                                                <img src="{$image->getUrl($image_box, $image_box)}"
                                                     srcset="{$image->getUrl($image_box*2, $image_box*2)} 2x"
                                                     alt="{$image.title|default:"{t title=$product.title n=$image@iteration}%title фото %n{/t}"}">
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                            <div class="product-gallery-thumbs-wrap">
                                {if $product->getYoutubeVideoId()}
                                    <div class="product-gallery-thumbs__video">
                                        <a class="product-gallery-video" data-bs-toggle="modal" href="#modal-video">
                                            <svg width="48" height="48" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M31.9168 23.0613L21.9498 15.816C21.584 15.5508 21.0984 15.5109 20.6975 15.717C20.2934 15.9214 20.041 16.3367 20.041 16.7855V31.2713C20.041 31.7249 20.2934 32.1386 20.6975 32.3431C20.8684 32.4293 21.0553 32.4725 21.2438 32.4725C21.4898 32.4725 21.7389 32.3942 21.9498 32.2393L31.9168 25.0004C32.2315 24.7688 32.4152 24.411 32.4152 24.0308C32.4168 23.6443 32.2283 23.2881 31.9168 23.0613Z" />
                                                <path d="M24.0008 0.00292969C10.7433 0.00292969 0 10.7463 0 24.0037C0 37.2564 10.7433 47.9965 24.0008 47.9965C37.2551 47.9965 48 37.2548 48 24.0037C48.0016 10.7463 37.2551 0.00292969 24.0008 0.00292969ZM24.0008 43.9921C12.9604 43.9921 4.00918 35.0458 4.00918 24.0037C4.00918 12.9665 12.9604 4.00892 24.0008 4.00892C35.0396 4.00892 43.9892 12.9649 43.9892 24.0037C43.9908 35.0458 35.0396 43.9921 24.0008 43.9921Z" />
                                            </svg>
                                        </a>
                                    </div>
                                {/if}
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-container product-gallery-thumbs"
                                     {if $THEME_SETTINGS.product_card_view_type == 'second'}data-swiper-direction="vertical"{/if}>
                                    <div class="swiper-wrapper">
                                        {foreach $images as $image}
                                            <div class="swiper-slide" data-image-id="{$image.id}">
                                                <img class="swiper-lazy"
                                                     src="{$image->getUrl($preview_image_box, $preview_image_box)}"
                                                     srcset="{$image->getUrl($preview_image_box*2, $preview_image_box*2)} 2x"
                                                     loading="lazy"
                                                     alt="{t title=$product.title n=$image@iteration}фото %n{/t}">
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                                <div class="swiper-button-next"></div>
                            </div>
                        </div>
                    {/hook}
                </div>
                <div class="col">

                    <div class="variant-product-main">
                        <div class="variant-product-options">
                            {hook name="catalog-product:offers" title="{t}Карточка товара:комплектации{/t}"}
                                {include file="product_offers.tpl"}
                            {/hook}
                            <div>
                                {hook name="catalog-product:information" title="{t}Карточка товара:краткая информация{/t}"}
                                    {if $list_properties = $product->getListProperties()}
                                        {capture assign = "properties"}{strip}
                                            {foreach $list_properties as $prop}
                                                {$value = $prop->textView()}
                                                {if $value !== ""}
                                                    <li>
                                                        <span class="text-gray pe-1 bg-body">{$prop.title}{if $prop.unit}({$prop.unit}){/if}</span>
                                                        <span class="ms-2 bg-body">{$value}</span>
                                                    </li>
                                                {/if}
                                            {/foreach}
                                        {/strip}{/capture}

                                        {if $properties}
                                            <div class="d-none d-lg-block">
                                                <div class="fw-bold mb-2">{t}Характеристики:{/t}</div>
                                                <ul class="item-product-chars">
                                                    {$properties}
                                                </ul>
                                                <div class="mt-2">
                                                    <a class="fs-5 to-chars rs-go-to-tab" href="#tab-property">{t}Все характеристики{/t}</a>
                                                </div>
                                            </div>
                                        {/if}
                                    {/if}
                                    {if $THEME_SETTINGS.enable_short_description_in_product_card}
                                        <div class="d-none d-lg-block mt-4 fs-5 text-gray">
                                            <div>{$product.short_description}</div>
                                        </div>
                                    {/if}
                                {/hook}
                            </div>
                        </div>
                        <div class="variant-product-aside">
                            {hook name="catalog-product:buyblock" title="{t}Карточка товара:Блок покупки{/t}"}
                                <div class="product-aside">
                                <div class="product-controls">
                                    {hook name="catalog-product:price" title="{t}Карточка товара:цены{/t}"}
                                    <div class="mb-xl-5 mb-md-4 mb-3">
                                        <div class="item-product-price item-product-price_prod">
                                            {$old_cost = $product->getOldCost()}
                                            {$new_cost = $product->getCost()}
                                            <div class="item-product-price__new-price">
                                                <span class="rs-price-new">{$new_cost}</span> {$product->getCurrency()}
                                                {if $catalog_config.use_offer_unit && $offers_data && $offers_data.offers}
                                                    <span class="rs-unit-block">/ <span class="rs-unit">{$offers_data.offers[0].unit}</span></span>
                                                {/if}
                                            </div>
                                            {if $old_cost && $new_cost != $old_cost}
                                                <div class="item-product-price__old-price">
                                                    <span class="rs-price-old">{$old_cost}</span> {$product->getCurrency()}</div>
                                            {/if}
                                        </div>
                                        <div class="rs-concomitant-price d-none mt-2">
                                            <span class="rs-value"></span>
                                            <span> {t}сопутствующие товары{/t}</span>
                                        </div>
                                    </div>
                                    {/hook}
                                    <div class="row g-sm-4 g-3 align-items-center">
                                        <div class="col-lg-12 col col-sm-auto order-first">
                                            {hook name="catalog-product:action-buttons" title="{t}Карточка товара:кнопки{/t}"}
                                                {include file="%catalog%/product_cart_button.tpl" disable_multioffer_dialog = true}
                                            {/hook}
                                        </div>
                                        {if $THEME_SETTINGS.enable_favorite}
                                            <div class="col-auto col-lg-6">
                                                <a class="product-fav rs-favorite {if $product->inFavorite()}rs-in-favorite{/if}" data-title="{t}В избранное{/t}" data-already-title="{t}В избранном{/t}">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M11.2131 5.5617L12 6.5651L12.7869 5.56171C13.5614 4.57411 14.711 4 15.9217 4C18.1262 4 20 5.89454 20 8.32023C20 10.2542 18.8839 12.6799 16.3617 15.5585C14.6574 17.5037 12.8132 19.0666 11.9999 19.7244C11.1866 19.0667 9.34251 17.5037 7.63817 15.5584C5.1161 12.6798 4 10.2542 4 8.32023C4 5.89454 5.87376 4 8.07829 4C9.28909 4 10.4386 4.57407 11.2131 5.5617ZM11.6434 20.7195L11.7113 20.6333L11.6434 20.7195Z" stroke-width="1"/>
                                                    </svg>
                                                    <span class="ms-2 d-none d-sm-block">{t}В избранное{/t}</span>
                                                </a>
                                            </div>
                                        {/if}
                                        {if $THEME_SETTINGS.enable_compare}
                                            <div class="col-auto col-lg-6">
                                                <a class="product-comp rs-compare{if $product->inCompareList()} rs-in-compare{/if}" data-title="{t}сравнить{/t}" data-already-title="{t}В сравнении{/t}">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19.1279 18.0433V18.5433H19.6279H19.9688L19.9689 18.5433C19.9692 18.5433 19.9693 18.5433 19.97 18.5436C19.9713 18.5442 19.975 18.5462 19.9798 18.5513C19.9895 18.5616 20 18.581 20 18.6095C20 18.638 19.9895 18.6574 19.9798 18.6677C19.975 18.6728 19.9713 18.6748 19.97 18.6754C19.9693 18.6757 19.9692 18.6757 19.9689 18.6757L19.9688 18.6757H4.03125L4.03109 18.6757C4.03077 18.6757 4.03069 18.6757 4.02996 18.6754C4.02867 18.6748 4.02498 18.6728 4.02023 18.6677C4.01055 18.6574 4 18.638 4 18.6095C4 18.581 4.01055 18.5616 4.02023 18.5513C4.02498 18.5462 4.02867 18.5442 4.02996 18.5436C4.03069 18.5433 4.03077 18.5433 4.03109 18.5433L4.03125 18.5433H4.37236H4.87236V18.0433V10.7968C4.87236 10.7683 4.88291 10.7489 4.89259 10.7385C4.89734 10.7335 4.90103 10.7315 4.90232 10.7309C4.90315 10.7305 4.90314 10.7306 4.90361 10.7306H8.14403C8.14409 10.7306 8.14414 10.7306 8.14419 10.7306C8.14451 10.7306 8.14459 10.7306 8.14532 10.7309C8.14661 10.7315 8.1503 10.7335 8.15505 10.7385C8.16473 10.7489 8.17528 10.7683 8.17528 10.7968V18.0433V18.5433H8.67528H9.84867H10.3487V18.0433V8.15454C10.3487 8.12606 10.3592 8.10665 10.3689 8.09633C10.3737 8.09127 10.3773 8.08926 10.3786 8.08868C10.379 8.08852 10.3792 8.08844 10.3793 8.0884C10.3795 8.08835 10.3797 8.08836 10.3799 8.08836H13.6203C13.6208 8.08836 13.6208 8.08831 13.6216 8.08868C13.6229 8.08926 13.6266 8.09127 13.6314 8.09633C13.641 8.10665 13.6516 8.12606 13.6516 8.15454V18.0433V18.5433H14.1516H15.325H15.825V18.0433V5.51247C15.825 5.48398 15.8355 5.46457 15.8452 5.45425C15.85 5.44919 15.8537 5.44719 15.8549 5.44661C15.8553 5.44643 15.8555 5.44635 15.8557 5.44632C15.8559 5.44627 15.856 5.44629 15.8562 5.44629H19.0967L19.0968 5.44629C19.0971 5.44628 19.0972 5.44628 19.0979 5.44661C19.0992 5.44719 19.1029 5.44919 19.1077 5.45425C19.1173 5.46457 19.1279 5.48398 19.1279 5.51247V18.0433Z" />
                                                    </svg>
                                                    <span class="ms-2 d-none d-sm-block">{t}Сравнить{/t}</span>
                                                </a>
                                            </div>
                                        {/if}
                                        {if $shop_config && (!$product->shouldReserve() && (!$check_quantity || $product.num>0))}
                                            {if $catalog_config.buyinoneclick}
                                                <div class="order-lg-first d-flex justify-content-center justify-content-sm-start justify-content-lg-center">
                                                    <a class="product-one-click rs-in-dialog rs-buy-one-click" data-href="{$router->getUrl('catalog-front-oneclick',["product_id"=>$product.id])}">
                                                        {include "%THEME%/helper/svg/hand.tpl"}
                                                        <span class="ms-2">{t}Купить в 1 клик{/t}</span>
                                                    </a>
                                                </div>
                                            {/if}
                                        {/if}

                                    </div>
                                </div>
                                {if !$product->shouldReserve()}
                                    {if $n = $offers_data.offers[0].availableOn}
                                        <div class="product-in-stock rs-stock-count-text-wrapper">
                                            <img class="me-2" width="24" height="24" src="{$THEME_IMG}/icons/availability.svg" alt="">
                                            <a class="rs-stock-count-text-container rs-go-to-tab" href="#tab-stock">{t n=$n}В наличии на %n [plural:%n:складе|складах|складах]{/t}</a>
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                            {/hook}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section pt-0">
        <div class="container">
            <div class="row">
                <div class="col offset-xxl-1 order-xl-last">
                    {if $shop_config}
                        {moduleinsert name="\Shop\Controller\Block\Concomitant"}
                    {/if}
                </div>
                <div class="col-xxl-8 col-xl-9 mt-6 mt-xl-0">

                    {$tabs=[]}
                    {$properties = $product->fillProperty()}
                    {$stick_info = $product->getWarehouseStickInfo()}

                    {if $product.description} {$tabs["description"] = t('О товаре')} {/if}
                    {if $properties || ($product->checkPropExist() == 'true')} {$tabs["property"] = t('Характеристики')} {/if}
                    {if $THEME_SETTINGS.review_enabled}{$tabs["comments"] = t('Отзывы')}{/if}
                    {if $files = $product->getFiles()} {$tabs["files"] = t('Файлы')}   {/if}
                    {if !$product->shouldReserve() && !empty($stick_info.warehouses)}  {$tabs["stock"] = t('Наличие')} {/if}
                    {$act_tab = $tab}
                    <div class="tab-pills__wrap mb-lg-5 mb-4">
                        <ul class="nav nav-pills tab-pills tab-pills_product" id="tabs">
                            {foreach $tabs as $key => $tab_title}
                                {if !$act_tab && $tab_title@first}{$act_tab = $key}{/if}
                                <li class="nav-item">
                                    <a class="nav-link {if $key == $act_tab}active{/if}" data-tab-id="{$key}"
                                        {if !in_array($key, $catalog_config->tabs_on_new_page) || $key == $tab}
                                            {if $tab && $tab != $key}
                                                href="{$product->getUrl()}#tab-{$key}"
                                            {else}
                                                data-bs-toggle="pill" data-bs-target="#tab-{$key}"
                                            {/if}
                                        {else}
                                            href="{$router->getUrl('catalog-front-product', ['id' => $product._alias, 'tab' => $key])}#tab-{$key}"
                                        {/if}>
                                        {$tab_title}{if $key == 'comments'} <span class="label-count label-count_static">{$product->getCommentsNum()}</span>{/if}
                                              {if $key == 'files'}<span class="label-count label-count_static">{count($files)}</span>{/if}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {if !empty($tabs)}
                        <div class="tab-content" id="pills-tabContent">
                            {if $tabs.description && (!$tab || $act_tab == 'property')}
                                <div class="tab-pane fade {if $act_tab == 'description'}show active{/if}" id="tab-description">
                                    {hook name="catalog-product:description" title="{t}Карточка товара:описание{/t}"}
                                    <article class="last-child-margin-remove">
                                        {$product.description}
                                    </article>
                                    {/hook}
                                </div>
                            {/if}
                            {if $tabs.property && ((!in_array('property', $catalog_config->tabs_on_new_page) && !$tab) || $act_tab == 'property')}
                                <div class="tab-pane fade {if $act_tab == 'property'}show active{/if}" id="tab-property">
                                    {hook name="catalog-product:properties" title="{t}Карточка товара:характеристики{/t}"}
                                        {if $offers_data.offers}
                                            {foreach $offers_data.offers as $key => $offer}
                                                {capture assign="offer_property"}{strip}
                                                    {foreach $offer.info as $property_title_value}
                                                        <li>
                                                            <div class="row g-4">
                                                                <div class="col-sm-7 col-6">{$property_title_value[0]}</div>
                                                                <div class="col-sm-5 col-6 fw-bold">{$property_title_value[1]}</div>
                                                            </div>
                                                        </li>
                                                    {/foreach}
                                                {/strip}{/capture}
                                                {if $offer_property}
                                                    <div class="rs-offer-property {if $offer.id != $offers_data.mainOfferId} d-none{/if}" data-offer="{$offer.id}">
                                                        <div class="fw-bold mb-md-4 mb-3 order-first">{t}Характеристики комплектации{/t}</div>
                                                        <ul class="product-chars mb-md-6 mb-5">
                                                            {$offer_property}
                                                        </ul>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        {/if}

                                        {foreach $product->fillProperty() as $data}
                                            {if !$data.group.hidden && !empty($data.group)}
                                                <div class="fw-bold mb-md-4 mb-3">{$data.group.title|default:"Общие"}</div>
                                                <ul class="product-chars mb-md-6 mb-5">
                                                    {foreach $data.properties as $property}
                                                        {$property_value = $property->textView()}
                                                        {if !$property.hidden && !empty($property_value)}
                                                            <li>
                                                                <div class="row g-4">
                                                                    <div class="col-sm-7 col-6">{$property.title} {if $property.unit}({$property.unit}){/if}
                                                                        {if $property.description}
                                                                            <a class="btn-popover"
                                                                               data-bs-toggle="popover"
                                                                               tabindex="0"
                                                                               data-bs-content="{$property.description}"> ? </a>
                                                                        {/if}
                                                                    </div>
                                                                    <div class="col-sm-5 col-6 fw-bold">{$property_value}</div>
                                                                </div>
                                                            </li>
                                                        {/if}
                                                    {/foreach}
                                                </ul>
                                            {/if}
                                        {/foreach}
                                    {/hook}
                                </div>
                            {/if}
                            {if $tabs.comments && ((!in_array('comments', $catalog_config->tabs_on_new_page) && !$tab) || $act_tab == 'comments')}
                                <div class="tab-pane fade {if $act_tab == 'comments'}show active{/if}" id="tab-comments">
                                <div class="row g-4">
                                    {hook name="catalog-product:comments" title="{t}Карточка товара:комментарии{/t}"}
                                        {moduleinsert name="Comments\Controller\Block\Comments" type="\Catalog\Model\CommentType\Product"}
                                    {/hook}
                                </div>
                            </div>
                            {/if}
                            {if $tabs.files && ((!in_array('files', $catalog_config->tabs_on_new_page) && !$tab) || $act_tab == 'files')}
                                <div class="tab-pane fade {if $act_tab == 'files'}show active{/if}" id="tab-files">
                                    {hook name="catalog-product:files" title="{t}Карточка товара:файлы{/t}"}
                                        <div class="last-child-margin-remove">
                                        {foreach $files as $file}
                                            <div class="product-doc mb-2">
                                                <div>
                                                    {$file.name} ({$file.size|format_filesize})
                                                    {if $file.description}
                                                        <div class="mt-2 text-gray">{$file.description}</div>
                                                    {/if}
                                                </div>
                                                <a class="product-doc__link" href="{$file->getUrl()}">
                                                    <img src="{$THEME_IMG}/icons/download.svg" width="40" height="40" alt="{t}Скачать{/t} {$file.name}">
                                                </a>
                                            </div>
                                        {/foreach}
                                    </div>
                                    {/hook}
                                </div>
                            {/if}
                            {if $tabs.stock && ((!in_array('stock', $catalog_config->tabs_on_new_page) && !$tab) || $act_tab == 'stock')}
                            <div class="tab-pane fade {if $act_tab == 'stock'}show active{/if}" id="tab-stock">
                                {hook name="catalog-product:stock" title="{t}Карточка товара:остатки{/t}"}
                                    <div class="product-availability-head">
                                        <div class="row g-4 align-items-center">
                                            <div class="col">{t}Адрес магазина{/t}</div>
                                            <div class="col-2">{t}Режим работы{/t}</div>
                                            <div class="col-2">
                                                {t}Наличие{/t}
                                            </div>
                                            <div class="col-3"></div>
                                        </div>
                                    </div>
                                    <div>
                                        {foreach $stick_info.warehouses as $warehouse}
                                            {$sticks = $offers_data.offers[0].sticks[$warehouse.id]}
                                            <div class="product-availability-item rs-warehouse-row{if !$sticks} rs-warehouse-empty{/if}" data-warehouse-id="{$warehouse.id}">
                                                <div class="row g-2 g-lg-4 align-items-lg-center gx-3">
                                                    <div class="col order-0">{$warehouse.adress}</div>
                                                    <div class="col-auto col-lg-2 order-1 order-lg-2">
                                                        <div class="availability-indicator rs-stick-wrap">
                                                            {foreach $stick_info.stick_ranges as $stick_range}
                                                                <div class="rs-stick availability-indicator__point {if $sticks>=$stick_range}availability-indicator__point_act{/if}"></div>
                                                            {/foreach}
                                                            <span class="availability-indicator__not rs-stick-empty">{t}Нет в наличии{/t}</span>
                                                        </div>
                                                    </div>
                                                    <div class="fs-5 col-lg-2 order-2 order-lg-1">{$warehouse.work_time}</div>
                                                    <div class="order-3 col-lg-3 text-lg-end"><a href="{$warehouse->getUrl()}" class="fs-5">{t}Подробнее о складе{/t}</a></div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                {/hook}
                            </div>
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{if $product->getYoutubeVideoId()}
    <div class="modal fade" id="modal-video">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 p-0 bg-transparent ratio ratio-16x9">
                <iframe class="youtube-player" width="100%" height="100%" src="https://www.youtube.com/embed/{$product->getYoutubeVideoId()}?enablejsapi=1&rel=0"
                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
{/if}