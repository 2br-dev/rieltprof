<div class="title">
    {t}Выберите место получения товара{/t}:
</div>
{$date_min=$extra_info.deliveryDateMin}
{$date_max=$extra_info.deliveryDateMax}
{if $date_min || $date_max}
    <div class="additionalTitleInfo">
       ({t}Ориентировочная дата доставки{/t}
       {if $date_min!=$date_max}
            {t date_min={$date_min|dateformat:"@date"} date_max={$date_max|dateformat:"@date"}}с %date_min по %date_max{/t})
       {else}
            {$date_min|dateformat:"@date"})
       {/if}
    </div>
{/if}

{$delivery_extra=$order->getExtraKeyPair('delivery_extra')}
<select id="pickpointsSelect{$delivery.id}" class="pickpointsSelect" name="delivery_extra[value]" {if empty($order.delivery)}disabled="disabled"{/if}>
    {foreach $pickpoints as $pickpoint}
        <option value='{$pickpoint->getDeliveryExtraJson()}' {if !empty($delivery_extra.code) && ($delivery_extra.code == $pickpoint->getCode())}selected{/if}>{$pickpoint->getPickPointTitle()}</option>
    {/foreach}
</select>
<input type="hidden" name="delivery_extra[code]" {if empty($order.delivery)}disabled="disabled"{/if} value="{if !empty($delivery_extra.code)}{$delivery_extra.code}{/if}"/>
<a class="pickpointsOpenMap formSave" title="{t}Скрыть карту{/t}" {if $order.delivery==$delivery.id}style="display: none;"{/if}>{t}Открыть карту{/t}</a>
<div class="pickpointsAdditionalInfo"></div>
{$filters=$delivery_type->getPvzListFilters($order)}
{if !empty($filters)}
    <div class="pickpointsMapFilters" style="display:none;">
        {foreach $filters as $i=>$filter}
            {$filter->getView($delivery.id, $i)}
        {/foreach}
    </div>
{/if}
<div id="pickpointsMap{$delivery.id}" class="pickpointsMap" style="display:none;"></div>
