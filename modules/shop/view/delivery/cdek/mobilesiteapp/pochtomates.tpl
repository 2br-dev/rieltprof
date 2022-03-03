{addcss file="%shop%/delivery/mobilesiteapp/cdek_widjet.css"}

<div class="cdekWidjet" padding-bottom>
    {if empty($errors) && !empty($pochtomates)}
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
        <ion-list padding-vertical>
            <ion-item no-padding>
                <ion-label>{t}Выберите пункт выдачи{/t}:</ion-label>
                <ion-select #delivery_pickup (ionChange)="changePickUpPoint()"  placeholder="{t}-Не выбрано-{/t}" cancelText="{t}Отмена{/t}" okText="{t}Выбрать{/t}">
                   {foreach $pochtomates as $pochtomat}
                    <ion-option value='{ "value" : "{ \"code\":\"{$pochtomat['Code']}\", \"cityCode\":\"{$pochtomat['CityCode']}\", \"addressInfo\":\"{$pochtomat['City']}, {$pochtomat['Address']}\", \"tariffId\":\"{$cdek->getTariffId()}\"{if isset($pochtomat['cashOnDelivery'])}, \"cashOnDelivery\":"{$pochtomat['cashOnDelivery']}"{/if}}" }' {if $pochtomat@first}selected="true"{/if}>
                        {$pochtomat['City']}, {$pochtomat['Address']}
                    </ion-option>
                  {/foreach}
                </ion-select>
            </ion-item>
        </ion-list>
        <div>
            {t}Неголосовой бесплатный контакт-центр{/t} 8 (800) 250-14-05
        </div>
    {else}
        {$address=$order->getAddress()}
        <input type="hidden" #delivery_extra value='{ "value" : "{ \"tariffId\":\"{$extra_info.tariffId}\", \"zipcode\":\"{$address.zipcode}\"}" }'/>
    {/if}
</div>
