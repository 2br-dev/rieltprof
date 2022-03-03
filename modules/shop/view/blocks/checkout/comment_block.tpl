<div class="checkout_block">
    <h3 class="h3">{t}Комментарий к заказу{/t}</h3>
    <div class="form-group">
        {$order->getPropertyView('comments')}
    </div>

    {if $is_agreement_require=$shop_config->require_license_agree}
        <input type="checkbox" name="license_agree" class="rs-checkout_licenseAgreementCheckbox" value="1" id="iagree" {if $smarty.post.license_agree}checked{/if}><label for="iagree">{t alias="Заказ на одной странице - ссылка на условия предоставления услуг" agreement_url=$router->getUrl('shop-front-licenseagreement')}Я согласен с <a href="%agreement_url" class="rs-indialog" target="_blank">условиями предоставления услуг</a>{/t}</label>
    {/if}

    {if $CONFIG.enable_agreement_personal_data}
        {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Подтвердить заказ{/t}"}
    {/if}
</div>

{if $cart_data.has_error}
    <div class="checkout_block formFieldError rs-checkout_cartError">{t}В корзине есть ошибки, оформление заказа невозможно{/t}</div>
{/if}

{if $order->getNonFormErrors()}
    <div class="checkout_block formFieldError">{implode(', ', $order->getNonFormErrors())}</div>
{/if}