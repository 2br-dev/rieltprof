{$app->autoloadScripsAjaxBefore()}
{addjs file="%shop%/delivery/rs.field_pvz.js"}

{$delivery = $order->getDelivery()}
{$type_object = $delivery->getTypeObject()}
{$selected_pvz = $order->getSelectedPvz()}

{if $type_object->getShortName() != 'myself'}
    <div class="delivery-params-pvz rs-field-pvz" data-pvz-select-url="{$router->getUrl('shop-front-selectpvz', [], true)}" data-field-pvz-options='{json_encode(['deliveryId' => $delivery['id'], 'cityIdSelector' => '[data-city-id]'])}'>
        <h4>{t}ПВЗ{/t}</h4>
        <input type="hidden" name="delivery_extra[pvz_data]" class="rs-field-pvz-input" value='{if $selected_pvz}{$selected_pvz->getDeliveryExtraJson()}{/if}'>
        <div class="field-pvz-line">
            <span class="rs-field-pvz-label">
                {if $selected_pvz}
                    {$selected_pvz->getPickPointTitle()}
                {else}
                    {t}Не указан{/t}
                {/if}
            </span>
            <a class="btn btn-sm btn-primary btn-alt rs-field-pvz-select">{t}Изменить{/t}</a>
        </div>
    </div>
{/if}
{$app->autoloadScripsAjaxAfter()}