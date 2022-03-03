{$shop_config = ConfigLoader::byModule('shop')}
{$main_config = ConfigLoader::byModule('main')}

{if $order.delivery}
    {$delivery = $order->getDelivery()}
    {$delivery_type = $delivery->getTypeObject()}
    {$addittional_html = $delivery->getDeliveryParamsHtml($order)}

    {if !empty($delivery_type->getNonCityRequiredAddressFieldsObjects()) || $addittional_html}
        <div class="checkout_block">
            <h3 class="h3">{t}Параметры доставки{/t}</h3>

            {if $delivery_type->hasCheckoutError($order)}
                <div class="formFieldError margin-bottom">{$delivery_type->getCheckoutError($order)}</div>
            {/if}

            {if !empty($delivery_type->getNonCityRequiredAddressFieldsObjects())}
                {if !empty($address_list)}
                    <div>
                        <h4>Адрес доставки:</h4>
                        <ul class="form-group last-address">
                            {foreach $address_list as $address}
                                <li class="rs-checkout_addressItem" data-id="{$address.id}">
                                    <label class="margin-remove">
                                        <input class="radio-btn rs-checkout_triggerUpdate" name="use_addr" type="radio" value="{$address.id}" {if $order.use_addr == $address.id}checked{/if}>
                                        <span>
                                            {if $address.zipcode}{$address.zipcode}, {/if}
                                            {if $address.city}{$address.city}, {/if}
                                            {$address->getLineView(false)}
                                        </span>
                                        <a class="rs-checkout_addressItemDelete"><i class="pe-2x pe-va pe-7s-close"></i></a>
                                    </label>
                                </li>
                            {/foreach}
                            <li class="">
                                <label class="margin-remove">
                                    <input class="radio-btn rs-checkout_triggerUpdate" name="use_addr" type="radio" value="0" {if $order.use_addr == 0}checked{/if}>
                                    <a class="">{t}Другой адрес{/t}</a>
                                </label>
                            </li>
                        </ul>
                    </div>
                {/if}
                {if $order.use_addr == 0 && $order.delivery}
                    {$field_objects = $delivery_type->getNonCityRequiredAddressFieldsObjects()}
                    {$address_plus_zipcode = isset($field_objects.address) && isset($field_objects.zipcode)}

                    <div>
                        {foreach $delivery_type->getNonCityRequiredAddressFieldsObjects() as $field}
                            {$field_name = 'addr_'|cat:$field->getName()}

                            <div class="form-group">
                                <label class="label-sup">{$field->getDescription()}*</label>
                                {if $field_name == 'addr_address'}
                                    <div class="rs-checkout_addressAddressInputWrapper">
                                        <input class="rs-checkout_triggerUpdate rs-checkout_addressAddressInput {if $shop_config.require_choose_address && !empty($main_config.dadata_api_key)}rs-checkout_requireSelectAddress{/if}" type="text" name="{$field_name}" value="{$order[$field_name]}">
                                    </div>
                                {else}
                                    <input class="rs-checkout_triggerUpdate" type="text" name="{$field_name}" value="{$order[$field_name]}">
                                {/if}
                                {if $order->getErrorsByForm($field_name)}
                                    <span class="formFieldError">{$order->getErrorsByForm($field_name, ', ')}</span>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
                {/if}
            {/if}

            {if $addittional_html}
                {$addittional_html}
            {/if}

            {if $order->getErrorsByForm('delivery_checkout')}
                <span class="formFieldError">{$order->getErrorsByForm('delivery_checkout', ', ')}</span>
            {/if}

        </div>
    {/if}
{/if}