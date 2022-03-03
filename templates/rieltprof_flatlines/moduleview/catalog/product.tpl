{* Шаблон карточки товара *}

{$shop_config = ConfigLoader::byModule('shop')}
{$check_quantity = $shop_config.check_quantity}
{$catalog_config = $this_controller->getModuleConfig()}
{$config = \RS\Config\Loader::byModule('rieltprof')}
{$current_user = \RS\Application\Auth::getCurrentUser()}
{$owner = $product->getOwner()}

{addcss file="libs/owl.carousel.min.css"}
{addcss file="common/lightgallery/css/lightgallery.min.css" basepath="common"}

{addjs file="libs/owl.carousel.min.js"}
{addjs file="lightgallery/lightgallery-all.min.js" basepath="common"}
{addjs file="rs.product.js"}

{if $product->isVirtualMultiOffersUse()} {* Если используются виртуальные многомерные комплектации *}
    {addjs file="rs.virtualmultioffers.js"}
{/if}
{$product->fillOffersStockStars()} {* Загружаем сведения по остаткам на складах *}


<div class="top-toolbar">
    {include file="%catalog%/features-product.tpl" product=$product}


    <div class="separator"></div>
    <div class="profile-block">
        <div class="name">
            <span>автор: </span>
            <a
                {if $current_user['id'] !== $owner->id}href="/owner-profile/{$owner->id}/" {else}href="/my/"{/if}
                class="seller_profile"
                title="Профиль продавца"
            >{$owner->name} {$owner->surname}</a><br>
            {if $current_user['id'] !== $owner->id}
                <div class="" style="display: flex;">
                    {if $current_user->canSendReview($current_user['id'], $owner->id)}
                        <a href="javascript:void(0);" class="feedback" data-target-modal="feedback-modal">Оставить отзыв</a>
                    {/if}
                    <div class="rating_user" data-initial="{$owner->rating}"></div>
                </div>
            {else}
                <div class="rating_user" data-initial="{$current_user['rating']}"></div>
            {/if}
        </div>
        <div class="avatar lazy-image" data-src="{$owner.__photo->getUrl('160', '160', 'axy')}"></div>
    </div>
</div>
<div class="content">
    {if !$config->isActualAd($product)}
        <div class="outdated"></div>
    {/if}
    <div id="updateProduct" itemscope itemtype="http://schema.org/Product" class="main-block product
                                                                              {if !$product->isAvailable()} rs-not-avaliable{/if}
                                                                              {if $product->canBeReserved()} rs-can-be-reserved{/if}
                                                                              {if $product.reservation == 'forced'} rs-forced-reserve{/if}" data-id="{$product.id}">
        <div class="swiper-container photos">
            {$images = $product->getImages()}
            <div class="swiper-wrapper">
                {$main_image = $product->getMainImage()}
                {if !$product->hasImage()}
                    <div class="swiper-slide photo lazy-image" data-src="{$main_image->getUrl(856, 277, 'xy')}"></div>
                {else}
                    {foreach $images as $key => $image}
                        <div class="swiper-slide photo lazy-image" data-src="{$image->getUrl(856, 277, 'xy')}"></div>
                    {/foreach}
                {/if}
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="data">
        <span class="product-title-action">
            {if $product->getMainDir()->parent == 1}Продажа{else}Аренда{/if}
        </span>
            <h1>
                {$product['object']}
                {if $product['rooms'] !== NULL}
                    , <span class="title-rooms">{$product['rooms']} ком.</span>
                {else}
                    {if $product->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                        , <span class="title-rooms">Студия</span>
                    {else}
                        , <span class="title-rooms">{$product->getProductPropValue($config['prop_rooms_list'], 'rooms_list')} ком.</span>
                    {/if}
                {/if}
                <span class="object-id">(#{$product['id']})</span>
            </h1>
            {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
            <div class="price">
                {if $product['cost_product']}
                    <div class="total">{$config->formatCost($product['cost_product'], ' ')} р.</div>
                    <div class="quadro">({$config->formatCost($product['cost_one'], ' ')} р./м²)</div>
                {else}
                    <div class="total">{$config->formatCost($product['cost_rent'], ' ')} р./мес.</div>
                {/if}
            </div>
            <div class="address">
                <div class="top">
                    {$product['city']},
                    {if $product['county'] != NULL}
                        {$product->getProductPropValue($config['prop_county'], 'county')} округ,
                    {/if}
                    {$product->getProductPropValue($config['prop_district'], 'district')}
                </div>
                <div class="bottom">
                    {if !empty({$product['street']})}
                        ул. {$product['street']},
                    {/if}
                    {if !empty({$product['house']})}
                        д. {$product['house']},
                    {/if}
                    {if !empty($product['liter'])}
                        , литер {$product['liter']}
                    {/if}
                </div>
            </div>
            <div class="chars">
                {if $product["square"]}
                    <div class="char">
                        <div class="value">{$product["square"]} м²</div>
                        <div class="key">Общая</div>
                    </div>
                {/if}
                {if $product["square_living"]}
                    <div class="char">
                        <div class="value">{$product["square_living"]} м²</div>
                        <div class="key">Жилая</div>
                    </div>
                {/if}
                {if $product["square_kitchen"]}
                    <div class="char">
                        <div class="value">{$product["square_kitchen"]} м²</div>
                        <div class="key">Кухня</div>
                    </div>
                {/if}
                {if $product['rooms']}
                    <div class="char">
                        <div class="value">{$product['rooms']}</div>
                        <div class="key">Комнат</div>
                    </div>
                {else}
                    {if $product->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
                        <div class="char">
                            <div class="value">{$product->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}</div>
                            <div class="key">Комнат</div>
                        </div>
                    {/if}
                {/if}
                {if $product['flat']}
                    <div class="char">
                        <div class="value">{$product['flat']}</div>
                        <div class="key">Этаж</div>
                    </div>
                {/if}
                {if $product['flat_house']}
                    <div class="char">
                        <div class="value">{$product['flat_house']}</div>
                        <div class="key">Этажность</div>
                    </div>
                {/if}
                {if $product['land_area']}
                    <div class="char">
                        <div class="value">{$product['land_area']} сот.</div>
                        <div class="key">Участок</div>
                    </div>
                {/if}
            </div>
            <div class="description-wrapper">
                {if !empty($product['note'])}
                    <div class="header">Описание</div>
                    <div class="description">
                        {$product['note']}
                    </div>
                {/if}
                {if !empty($product['personal_note']) && $current_user['id'] == $product->getOwner()->id}
                    <div class="header personal-note">Личные заметки</div>
                    <div class="description">
                        {$product['personal_note']}
                    </div>
                {/if}
            </div>
        </div>
        <div class="features-wrapper">
            <div class="features">
                {if $product['state']}
                    <div class="feature">
                        <div class="icon">
                            <img src="{$THEME_IMG}/features/condition.svg" alt="">
                        </div>
                        <div class="feature-data">
                            <div class="value">состояние</div>
                            <div class="key">{$product->getProductPropValue($config['prop_state'], 'state')}</div>
                        </div>
                    </div>
                {/if}
                {if $product['object'] != 'Комната' && $product['object'] != 'Участок' && $product['object'] != 'Гараж'}
                    <div class="feature">
                        <div class="icon">
                            <img src="{$THEME_IMG}/features/rooms.svg" alt="">
                        </div>
                        <div class="feature-data">
                            <div class="value">Все комнаты изолированы</div>
                            <div class="key">
                                {if $product['rooms_isolated']}
                                    Да
                                {else}
                                    Нет
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
                {if $product['object'] != 'Участок'}
                    {if $product['year']}
                        <div class="feature">
                            <div class="icon">
                                <img src="{$THEME_IMG}/features/built.svg" alt="">
                            </div>
                            <div class="feature-data">
                                <div class="value">год постройки</div>
                                <div class="key">{$product['year']}</div>
                            </div>
                        </div>
                    {/if}
                {/if}
                {if $product['object'] != 'Участок'}
                    <div class="feature">
                        <div class="icon">
                            <img src="{$THEME_IMG}/features/walls.svg" alt="">
                        </div>
                        <div class="feature-data">
                            <div class="value">Материал стен</div>
                            <div class="key">{$product->getProductPropValue($config['prop_material'], 'material')}</div>
                        </div>
                    </div>
                {/if}
                {*            <div class="feature critical">*}
                {*                <div class="icon">*}
                {*                    <img src="{$THEME_IMG}/features/bank.svg" alt="">*}
                {*                </div>*}
                {*                <div class="feature-data">*}
                {*                    <div class="key"  style="color: #000;">Наличие обременений банка</div>*}
                {*                    {if $product['encumbarance']}*}
                {*                        <div class="value">{$product['encumbarance_notice']}</div>*}
                {*                    {else}*}
                {*                        <div class="value" style="color: #10a110">Отсутствует</div>*}
                {*                    {/if}*}

                {*                </div>*}
                {*            </div>*}
                {*            <div class="feature critical">*}
                {*                <div class="icon">*}
                {*                    <img src="{$THEME_IMG}/features/children.svg" alt="">*}
                {*                </div>*}
                {*                <div class="feature-data">*}
                {*                    <div class="key" style="color: #000;">Несовершеннолетние дети, опека</div>*}
                {*                    {if $product['child']}*}
                {*                        <div class="value">Присутствует</div>*}
                {*                    {else}*}
                {*                        <div class="value" style="color: #10a110">Отсутствует</div>*}
                {*                    {/if}*}

                {*                </div>*}
                {*            </div>*}
                {if $product['object'] == 'Квартира' || $product['object'] == 'Комната' || $product['object'] == 'Коммерция'}
                    <div class="feature success">
                        <div class="icon">
                            <img src="{$THEME_IMG}/features/replan.svg" alt="">
                        </div>
                        <div class="feature-data">
                            <div class="key" style="color: #000;">Наличие перепланировок</div>
                            {if $product['remodeling']}
                                {if $product['remodeling_legalized']}
                                    <div class="value">Есть (узаконена)</div>
                                {else}
                                    <div class="value" style="color: #e01f1f">Есть (не узаконена)</div>
                                {/if}

                            {else}
                                <div class="value">Перепланировок нет</div>
                            {/if}
                        </div>
                    </div>
                {/if}
                {*            <div class="feature">*}
                {*                <div class="icon">*}
                {*                    <img src="/img/features/money-break.svg" alt="">*}
                {*                </div>*}
                {*                <div class="feature-data">*}
                {*                    <div class="value">Разбивка по сумме</div>*}
                {*                    <div class="key">Не нужна</div>*}
                {*                </div>*}
                {*            </div>*}
            </div>
        </div>
    </div>
</div>
{include file='%rieltprof%/review-form.tpl' from=$current_user to=$owner}

