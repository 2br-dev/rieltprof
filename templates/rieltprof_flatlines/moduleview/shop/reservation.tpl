{$catalog_config=$this_controller->getModuleConfig()}
{$shop_config = ConfigLoader::byModule('shop')}

<form method="POST" action="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="form-style modal-body reserve-form">
    <input type="hidden" name="product_id" value="{$product.id}">
    <input type="hidden" name="offer_id" value="{$reserve.offer_id}">
    <input type="hidden" name="currency" value="{$product->getCurrencyCode()}">

    <h2 class="h2">{t}Заказать{/t}</h2>

    <p>{$product.title} {$product.barcode}</p>
    <p>{t}В данный момент товара нет в наличии. Заполните форму и мы оповестим вас о поступлении товара.{/t}</p>

    {if $reserve->hasError()}<div class="page-error">{implode(', ', $reserve->getErrors())}</div>{/if}

    {if $product->isMultiOffersUse()}
        <p>{$product.offer_caption|default:'Комплектация'}</p>

        {$offers_levels=$product.multioffers.levels}
        <table class="table-underlined">
            {foreach $offers_levels as $level}
                <tr class="table-underlined-text">
                    <td><span>{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</span></td>
                    <td><span name="multioffers[{$level.prop_id}]">{$reserve.multioffers[$level.prop_id]}</span>

                    </td>
                </tr>
            {/foreach}
        </table>
    {elseif $product->isOffersUse()}

        {$offers=$product.offers.items}
        <table class="table-underlined">
            <tr class="table-underlined-text">
                <td><span>{$product.offer_caption|default:'Комплектация'}</span></td>
                <td><input name="offer" value="{$reserve.offer}" readonly type="hidden">
                    <span>{$reserve.offer}</span>
                </td>
            </tr>
        </table>
    {/if}

    <div class="form-group">
        <label class="label-sup">{t}Кол-во{/t}</label>

        <div class="quantity rs-amount">
            <input type="number" min="{$product->getAmountStep()}" step="{$product->getAmountStep()}" value="{$reserve.amount}" name="amount" class="rs-field-amount">
            <div class="quantity-nav">
                <div class="quantity-unit">
                    {$offer_n = $reserve->getOffer()->sortn}
                    {if $catalog_config.use_offer_unit || $product.offers.items[$offer_n]}
                        {$product.offers.items[$offer_n]->getUnit()->stitle}
                    {else}
                        {$product->getUnit()->stitle}
                    {/if}
                </div>
                <div class="quantity-button quantity-up rs-inc" data-amount-step="{$product->getAmountStep()}">+</div>
                <div class="quantity-button quantity-down rs-dec" data-amount-step="{$product->getAmountStep()}">-</div>
            </div>
        </div>
    </div>

    {$required_fields = $shop_config.reservation_required_fields}
    {if in_array($required_fields, ['phone_email', 'phone'])}
        <div class="form-group">
            <label class="label-sup">{t}Телефон{/t}</label>
            <input type="text" name="phone" class="inp" value="{$reserve.phone}">
        </div>
    {/if}
    {if in_array($required_fields, ['phone_email', 'email'])}
        <div class="form-group">
            <label class="label-sup">{if $required_fields == 'phone_email'}<small>{t}или{/t}</small> {/if}{t}E-mail{/t}</label>
            <input type="text" name="email" class="inp" value="{$reserve.email}">
        </div>
    {/if}

    {if !$is_auth}
        <div class="form-group">
            <label class="label-sup">{$reserve->__kaptcha->getTypeObject()->getFieldTitle()}</label>
            {$reserve->getPropertyView('kaptcha')}
        </div>
    {/if}
    <div class="form__menu_buttons mobile-center">
        {if $CONFIG.enable_agreement_personal_data}
            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Оповестить меня{/t}"}
        {/if}
        <button type="submit" class="link link-more">{t}Оповестить меня{/t}</button>
    </div>

    <script type="text/javascript">
        $(function() {
            $('.reserve-form .rs-inc').off('click').on('click', function() {
                var amountField = $(this).closest('.reserve-form').find('.rs-field-amount');
                amountField.val( (+amountField.val()|0) + ($(this).data('amount-step')-0) );
            });

            $('.reserve-form .rs-dec').off('click').on('click', function() {
                var amountField = $(this).closest('.reserve-form').find('.rs-field-amount');
                var val = (+amountField.val()|0);
                if (val > $(this).data('amount-step')) {
                    amountField.val( val - $(this).data('amount-step') );
                }
            });
        });
    </script>
</form>