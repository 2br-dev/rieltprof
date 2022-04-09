{if $pvz_list}
    {$delivery = $delivery_type->getDelivery()}
    {$city = $order->getAddress()->getCity()}
    {$delivery_extra = $order->getExtraKeyPair('delivery_extra')}
    {$delivery_extra_value = json_decode(htmlspecialchars_decode($delivery_extra.pvz_data), true)}

    <div class="rs-field-pvz"
         data-field-pvz-options='{json_encode([ 'deliveryId' => $delivery['id'], 'cityIdSelector' => '.rs-field-pvz' ])}'
         data-city-id="{$city.id}"
         data-pvz-select-url="{$router->getUrl('shop-front-selectpvz')}"
    >
        <h4>{t}Пункт выдачи{/t}</h4>
        <div class="row">
            <div class="col-md">
                <select id="pvz_delivery" class="form-select rs-checkout_pvzInput rs-field-pvz-input rs-checkout_triggerUpdate" name="delivery_extra[pvz_data]">
                    {foreach $pvz_list as $pvz}
                        <option value='{$pvz->getDeliveryExtraJson()}' data-pvz-code="{$pvz->getCode()}" {if !empty($delivery_extra_value.code) && ($delivery_extra_value.code == $pvz->getCode())}selected{/if}>
                            {$pvz->getPickPointTitle()}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-md-auto mt-3 mt-md-0">
                <div class="btn btn-secondary rs-checkout_pvzSelectButton rs-field-pvz-select">{t}Выбрать на карте{/t}</div>
            </div>
        </div>
    </div>
{/if}