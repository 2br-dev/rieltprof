{$shop_config = ConfigLoader::byModule('shop')}

<form method="POST" action="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}" class="reserveForm">
        <input type="hidden" name="product_id" value="{$product.id}">
        <input type="hidden" name="product_barcode" value="{$product.barcode}">
        <input type="hidden" name="offer_id" value="{$reserve.offer_id}">
        <input type="hidden" name="currency" value="{$product->getCurrencyCode()}">
        <h2 class="dialogTitle" data-dialog-options='{ "width": "400" }'>{t}Заказать{/t}</h2>
        <p class="prodtitle">{$product.title} {$product.barcode}</p>
        <p class="infotext">
            {t}В данный момент товара нет в наличии. Заполните форму и мы оповестим вас о поступлении товара.{/t}
        </p>
        {if $reserve->hasError()}<div class="error">{implode(', ', $reserve->getErrors())}</div>{/if}
        <table class="formTable dialogTable">
            <tr>
                <td class="key">{t}Кол-во{/t}</td>
                <td class="value"><input type="number" min="{$product->getAmountStep()}" step="{$product->getAmountStep()}" name="amount" class="amount" value="{$reserve.amount}">
                    <div class="qpicker">
                        <a class="inc" data-amount-step="{$product->getAmountStep()}"></a>
                        <a class="dec" data-amount-step="{$product->getAmountStep()}"></a>
                    </div>
                </td>
            </tr>
            {if $product->isMultiOffersUse()}
                <tr>
                    <td class="key">{$product.offer_caption|default:t('Комплектация')}</td>
                    <td class="value">
                    </td>
                </tr>
                {assign var=offers_levels value=$product.multioffers.levels} 
                {foreach $offers_levels as $level}
                    <tr>
                        <td class="key">{$level.title|default:$level.prop_title}</td>
                        <td class="value"><input name="multioffers[{$level.prop_id}]" value="{$reserve.multioffers[$level.prop_id]}" readonly></td>
                    </tr>
                {/foreach}
            {elseif $product->isOffersUse()}
                {assign var=offers value=$product.offers.items}
                <tr>
                    <td class="key">{$product.offer_caption|default:t('Комплектация')}</td>
                    <td class="value"><input name="offer" value="{$reserve.offer}" readonly></td>
                </tr>
            {/if}

            {$required_fields = $shop_config.reservation_required_fields}
            {if in_array($required_fields, ['phone_email', 'phone'])}
                <tr>
                    <td class="key">{t}Телефон{/t}</td>
                    <td class="value"><input type="text" name="phone" class="inp" value="{$reserve.phone}"></td>
                </tr>
            {/if}
            {if in_array($required_fields, ['phone_email', 'email'])}
                <tr>
                    <td class="key"><small>{if $required_fields == 'phone_email'}{t}или{/t}{/if}</small> E-mail</td>
                    <td class="value"><input type="text" name="email" class="inp" value="{$reserve.email}"></td>
                </tr>
            {/if}
            
            {if !$is_auth}
            <tr>
                <td class="key">{$reserve->__kaptcha->getTypeObject()->getFieldTitle()}</td>
                <td class="value">{$reserve->getPropertyView('kaptcha')}</td>
            </tr>
            {/if}
        </table>
        <button type="submit" class="formSave">{t}Оповестить меня{/t}</button>
</form>

<script>
    $(function() {
        $('.reserveForm .inc').off('click').on('click', function() {
            var amountField = $(this).closest('.reserveForm').find('.amount');
            amountField.val( (+amountField.val()|0) + ($(this).data('amount-step')-0) );
        });
        
        $('.reserveForm .dec').off('click').on('click', function() {
            var amountField = $(this).closest('.reserveForm').find('.amount');
            var val = (+amountField.val()|0);
            if (val > $(this).data('amount-step')) {
                amountField.val( val - $(this).data('amount-step') );
            }
        });
    });
</script>