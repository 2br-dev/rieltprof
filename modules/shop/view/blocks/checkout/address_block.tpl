{$shop_config = ConfigLoader::byModule('shop')}
{$main_config = ConfigLoader::byModule('main')}

{if $order.delivery}
    {$delivery = $order->getDelivery()}
    {$delivery_type = $delivery->getTypeObject()}
    {$addittional_html = $delivery->getDeliveryParamsHtml($order)}

    {if !empty($delivery_type->getNonCityRequiredAddressFieldsObjects()) || $addittional_html}
        <div>
            <div class="mb-4">
                <div class="fs-3">{t}Адрес получения{/t}</div>

                {if $order->getErrorsByForm('delivery_checkout')}
                    <div class="invalid-feedback d-block">{$order->getErrorsByForm('delivery_checkout', ', ')}</div>
                {/if}

                {if $delivery_type->hasCheckoutError($order)}
                    <div class="invalid-feedback d-block">{$delivery_type->getCheckoutError($order)}</div>
                {/if}
            </div>
            {if !empty($delivery_type->getNonCityRequiredAddressFieldsObjects())}
                {if !empty($address_list)}
                    <ul class="list-unstyled m-0">
                        {foreach $address_list as $address}
                            <li class="mb-2 rs-checkout_addressItem" data-id="{$address.id}">
                                <div class="radio">
                                    <input class="rs-checkout_triggerUpdate" name="use_addr" type="radio"
                                           id="adr-{$address.id}"
                                           value="{$address.id}" {if $order.use_addr == $address.id}checked{/if}>
                                    <label for="adr-{$address.id}">
                                        <span class="radio-attr">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#404147" xmlns="http://www.w3.org/2000/svg">
                                                <path class="radio-attr__check" d="M8 4C5.792 4 4 5.792 4 8C4 10.208 5.792 12 8 12C10.208 12 12 10.208 12 8C12 5.792 10.208 4 8 4Z" />
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM8 16C12.4183 16 16 12.4183 16 8C16 3.58172 12.4183 0 8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16Z" />
                                            </svg>
                                        </span>
                                        <span class="d-flex">
                                            <span>
                                                {if $address.zipcode}{$address.zipcode}, {/if}
                                                {if $address.city}{$address.city}, {/if}
                                                {$address->getLineView(false)}
                                            </span>
                                            <a class="ms-3 rs-checkout_addressItemDelete">
                                                <svg width="16" height="16" fill="#FF2F2F" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.0218 2.98208C13.2161 3.17825 13.2146 3.49483 13.0185 3.68918L8.6666 8.00064L13.0185 12.3121C13.2147 12.5065 13.2161 12.823 13.0218 13.0192C12.8274 13.2154 12.5109 13.2169 12.3147 13.0225L7.95617 8.70447L3.68515 12.9358C3.48898 13.1302 3.1724 13.1287 2.97805 12.9325C2.7837 12.7364 2.78518 12.4198 2.98135 12.2254L7.24574 8.00064L2.98137 3.77587C2.7852 3.58152 2.78372 3.26494 2.97807 3.06877C3.17242 2.8726 3.489 2.87112 3.68517 3.06547L7.95617 7.29681L12.3147 2.97879C12.5108 2.78444 12.8274 2.78591 13.0218 2.98208Z" />
                                                </svg>
                                            </a>
                                        </span>
                                    </label>
                                </div>
                            </li>
                        {/foreach}
                        <li class="mb-2">
                            <div class="radio">
                                <input id="adr-0" class="rs-checkout_triggerUpdate" name="use_addr" type="radio" value="0" {if $order.use_addr == 0}checked{/if}>
                                <label for="adr-0">
                                        <span class="radio-attr">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#404147" xmlns="http://www.w3.org/2000/svg">
                                                <path class="radio-attr__check" d="M8 4C5.792 4 4 5.792 4 8C4 10.208 5.792 12 8 12C10.208 12 12 10.208 12 8C12 5.792 10.208 4 8 4Z" />
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM8 16C12.4183 16 16 12.4183 16 8C16 3.58172 12.4183 0 8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16Z" />
                                            </svg>
                                        </span>
                                    <span class="d-flex"><span>{t}Другой адрес{/t}</span></span>
                                </label>
                            </div>
                        </li>
                    </ul>
                {/if}
            {/if}

            {if $order.use_addr == 0 && $order.delivery}
                {$field_objects = $delivery_type->getNonCityRequiredAddressFieldsObjects()}
                {$address_plus_zipcode = isset($field_objects.address) && isset($field_objects.zipcode)}
                <div class="mt-4">
                    <div class="row g-3">
                        {foreach $delivery_type->getNonCityRequiredAddressFieldsObjects() as $field}
                            {$field_name = "addr_{$field->getName()}"}
                            <div class="{if $field_name == 'addr_zipcode'}col-xl-3 col-sm-3 col-lg-12{else}col-9{/if}">
                                <label class="form-label">{$field->getDescription()}</label>

                                {if $field_name == 'addr_address'}
                                    <div class="rs-checkout_addressAddressInputWrapper position-relative">
                                        <input class="form-control rs-checkout_triggerUpdate rs-checkout_addressAddressInput {if $shop_config.require_choose_address && !empty($main_config.dadata_api_key)}rs-checkout_requireSelectAddress{/if}" type="text" name="{$field_name}" value="{$order[$field_name]}">
                                    </div>
                                {else}
                                    <input class="form-control rs-checkout_triggerUpdate" type="text" name="{$field_name}" value="{$order[$field_name]}">
                                {/if}

                                {if $order->getErrorsByForm($field_name)}
                                    <span class="invalid-feedback d-block">{$order->getErrorsByForm($field_name, ', ')}</span>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}

            {if $addittional_html}
                {$addittional_html}
            {/if}
        </div>
    {/if}
{/if}