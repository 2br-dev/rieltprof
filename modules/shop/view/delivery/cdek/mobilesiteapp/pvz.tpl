{addcss file="%shop%/delivery/mobilesiteapp/cdek_widjet.css"}

<div class="cdekWidjet" padding-bottom>
    {if empty($errors) && !empty($pvz_list)}
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
                   {foreach $pvz_list as $pvz}
                    <ion-option value='{json_encode(["pvz_data" => $pvz->getDeliveryExtraJson()], JSON_UNESCAPED_UNICODE)}' {if $pvz@first}selected="true"{/if}>
                        {$pvz->getCity()}, {$pvz->getAddress()}
                    </ion-option>
                  {/foreach}
                </ion-select>
            </ion-item>
        </ion-list>
        <div>
            {t}Неголосовой бесплатный контакт-центр{/t} 8 (800) 250-14-05
        </div>
    {/if}
</div>
