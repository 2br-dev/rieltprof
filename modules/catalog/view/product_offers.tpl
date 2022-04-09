{* Комплектации товара *}
{$shop_config = ConfigLoader::byModule('shop')}

{if $offers_data}
    <script rel="product-offers" type="application/json" data-check-quantity="{$shop_config->check_quantity}">{$offers_data|json_encode:320}</script>

    {if $offers_data['levels']}
        <div class="variant-product-options__list mb-4">
            {foreach $offers_data['levels'] as $level}
                <div>
                    <div class="fw-bold mb-2">{$level.title}:</div>
                    <ul class="item-product-choose">
                        {foreach $level.values as $key => $value}
                            <li>
                                <div class="radio-{if isset($value['image'])}image{else}{$level.type|default:"list"}{/if}">
                                    <input autocomplete="off"
                                           id="mo_{$level.id}_{$key}"
                                           type="radio"
                                           name="multioffers[{$level.id}]"
                                           data-property-title="{$level.title}"
                                           data-property-id="{$level.id}"
                                           value="{$value.text}"
                                           {if isset($level.isVirtual) && $value.text == $first_offer_params[$level.title]}checked{/if}>

                                    <label for="mo_{$level.id}_{$key}" title="{$value.text}">
                                        {if !$level.isPhoto} {* Если отображать не как фото (выпадающим списком) *}
                                            {if $level.type == 'color'}
                                                {if $value.image}
                                                    <img src="{$value.image.url}" alt="">
                                                {else}
                                                    <div class="radio-bg-color" style="background-color:{$value.color}"></div>
                                                {/if}
                                            {elseif $level.type == 'image' && $value.image} {* Отображаем картинки *}
                                                <img src="{$value.image.url}" alt="">
                                            {else} {* Обычный список, радиокнопки и др. *}
                                                {$value.text}
                                            {/if}

                                        {else} {* Как фото, привязанные к комплектации *}
                                            {if isset($value.image)}
                                                <img src="{$value.image.url}" alt="">
                                            {else}
                                                {$value.text}
                                            {/if}
                                        {/if}
                                    </label>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/foreach}
        </div>

        {if $offers_data['offers']}
            <input type="hidden" name="offer" value="{$offers_data.mainOfferId}"/>
        {/if}
    {elseif $offers_data['offers'] && count($offers_data['offers'])>1}
        <div class="variant-product-options__list mb-4">
            <div>
                <div class="fw-bold mb-2">{$offers_data['offersCaption']}:</div>
                <ul class="item-product-choose">
                    {foreach $offers_data['offers'] as $key => $offer}
                        <li {if $shop_config->check_quantity && $offer.num <= 0}class="no-exists"{/if}>
                            <div class="radio-image radio-image_txt">
                                <input autocomplete="off" value="{$offer.id}"
                                       type="radio" name="offer" {if $offer@first}checked{/if}
                                       id="offer_{$key}">

                                <label for="offer_{$key}">{$offer.title}</label>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {/if}
{/if}