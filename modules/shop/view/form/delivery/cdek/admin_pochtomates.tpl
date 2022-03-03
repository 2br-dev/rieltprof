{if !empty($errors)}
    <ul class="deliveryError">
        {foreach $errors as $error}
           <li>{$error}</li>
        {/foreach}
    </ul>
{else}
    {if !empty($pickpoints)}
        <div class="deliveryChooseWrap">
            {$delivery_extra=$url->request('delivery_extra', 'array')}
            <div id="cdekWidjet{$delivery.id}" class="cdekWidjet" data-delivery-id="{$delivery.id}">
                <div class="title">
                    {t}Выберите место получения товара{/t}:
                </div>
                <div class="additionalTitleInfo">
                   {$date_min=$extra_info.deliveryDateMin} 
                   {$date_max=$extra_info.deliveryDateMax} 
                   ({t}Ориентировочная дата доставки{/t}
                   {if $date_min!=$date_max}
                        {t date_min={$date_min|dateformat:"@date"} date_max={$date_max|dateformat:"@date"}}с %date_min по %date_max{/t})
                   {else}
                        {$date_min|dateformat:"@date"})
                   {/if}
                </div>
                
                {$value=json_decode(htmlspecialchars_decode($delivery_extra.value), true)}
                <select id="cdekSelect" name="delivery_extra[value]" class="cdekSelect">
                    {foreach $pickpoints as $pickpoint}
                        <option {if $value && ($value.code == $pickpoint->getCode())}selected="selected"{/if} value='{$pickpoint->getDeliveryExtraJson()}'>{$pickpoint->getPickPointTitle()}</option>
                    {/foreach}
                </select>
                <div id="deliveryInsertInputs">
                    {* Сюда будет вставлена дополнительная информация *}
                </div>
            </div>
        </div>
    {/if}
{/if}


