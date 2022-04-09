{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "class"}modal-lg{/block}
{block "title"}{t}Заказать{/t}{/block}
{block "body"}
    {$shop_config = ConfigLoader::byModule('shop')}
    <div class="mb-lg-5 mb-4">{t}В данный момент товара нет в наличии. Заполните форму и мы оповестим вас о поступлении товара.{/t}</div>
    <form method="POST" action="{$router->getUrl('shop-front-reservation', ["product_id" => $product.id])}">
        <div class="modal-item mb-4">
            <div class="mb-2">{$product.title} {$product.barcode}</div>
            <div class="row g-2 align-items-center">
                <div class="col">
                    {if $product->isMultiOffersUse()}
                        {$offers_levels = $product.multioffers.levels}
                        {foreach $offers_levels as $level}
                            <div class="d-flex align-items-center">
                                <div class="text-gray">{$level.title|default:$level.prop_title}:</div>
                                <div class="ms-1">
                                    <input name="multioffers[{$level.prop_id}]" value="{$offer_fields.multioffer[$level.prop_id]}" readonly type="hidden">
                                    <span>{$reserve.multioffers[$level.prop_id]}</span>
                                </div>
                            </div>
                        {/foreach}
                    {elseif $product->isOffersUse()}
                        <div class="d-flex align-items-center">
                            <div class="text-gray">{$product.offer_caption|default:"{t}Комплектация{/t}"}</div>
                            <div class="ms-1">
                                <input name="offer" value="{{$reserve.offer}}" readonly type="hidden">
                                <span>{$reserve.offer}</span>
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="col-12 col-lg-auto d-flex justify-content-end">
                    <div class="cart-amount rs-number-input">
                        <button type="button" class="rs-number-down">
                            <svg width="12" height="12" viewBox="0 0 12 12"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.4752 6.46875H1.47516V4.96875H10.4752V6.46875Z"/>
                            </svg>
                        </button>
                        <div class="cart-amount__input">
                            <input type="number" min="{$product->getAmountStep()}" step="{$product->getAmountStep()}" name="amount" class="amount" value="{$reserve.amount}">
                            <span>{$product->getUnit()->stitle}</span>
                        </div>
                        <button type="button" class="rs-number-up">
                            <svg width="12" height="12" viewBox="0 0 12 12"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.7326 6.94364H6.87549V10.8008H5.58978V6.94364H1.73264V5.65792H5.58978V1.80078H6.87549V5.65792H10.7326V6.94364Z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="product_id" value="{$product.id}">
        <input type="hidden" name="product_barcode" value="{$product.barcode}">
        <input type="hidden" name="offer_id" value="{$reserve.offer_id}">
        <input type="hidden" name="currency" value="{$product->getCurrencyCode()}">

        <div class="row g-4">
            {$required_fields = $shop_config.reservation_required_fields}
            {if in_array($required_fields, ['phone_email', 'phone'])}
                <div class="col-lg-6">
                    <label class="form-label">{t}Телефон{/t}</label>
                    {$reserve->getPropertyView('phone')}
                </div>
            {/if}
            {if in_array($required_fields, ['phone_email', 'email'])}
                <div class="col-lg-6">
                    <label class="form-label"><small>{if $required_fields == 'phone_email'}{t}или{/t}{/if}</small> {t}E-mail{/t}</label>
                    {$reserve->getPropertyView('email')}
                </div>
            {/if}
            {if !$is_auth}
                <div>
                    <label class="form-label">{$reserve->__kaptcha->getTypeObject()->getFieldTitle()}</label>
                    {$reserve->getPropertyView('kaptcha')}
                </div>
            {/if}
            {if $CONFIG.enable_agreement_personal_data}
                {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Оповестить меня{/t}"}
            {/if}
            <div>
                <button type="submit" class="btn btn-primary col-12 col-lg-auto">{t}Оповестить меня{/t}</button>
            </div>
        </div>
    </form>
{/block}