{$app->autoloadScripsAjaxBefore()}

{addjs file="%shop%/delivery/rs.field_pvz.js"}

{if $pvz_list}
    {$delivery = $delivery_type->getDelivery()}
    {$city = $order->getAddress()->getCity()}
    {$delivery_extra = $order->getExtraKeyPair('delivery_extra')}
    {$delivery_extra_value = json_decode(htmlspecialchars_decode($delivery_extra.pvz_data), true)}

    <div class="deliveryParamsPvz rs-field-pvz"
         data-field-pvz-options='{json_encode([ 'deliveryId' => $delivery['id'], 'cityIdSelector' => '.rs-field-pvz' ])}'
         data-city-id="{$city.id}"
         data-pvz-select-url="{$router->getUrl('shop-front-selectpvz', [], true)}"
    >

        <h4>{t}Пункт выдачи{/t}</h4>
        <div class="deliveryParamsPvz_inputWrapper">
            <select id="pvz_delivery" class="rs-checkout_pvzInput rs-field-pvz-input" name="delivery_extra[pvz_data]">
                {foreach $pvz_list as $pvz}
                    <option value='{$pvz->getDeliveryExtraJson()}' data-pvz-code="{$pvz->getCode()}" {if !empty($delivery_extra_value.code) && ($delivery_extra_value.code == $pvz->getCode())}selected{/if}>
                        {$pvz->getPickPointTitle()}
                    </option>
                {/foreach}
            </select>
            <div class="link link-more rs-checkout_pvzSelectButton rs-field-pvz-select">{t}Выбрать на карте{/t}</div>
        </div>
    </div>
{/if}

{$app->autoloadScripsAjaxAfter()}