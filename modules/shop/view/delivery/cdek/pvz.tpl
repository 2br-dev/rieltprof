<div class="deliveryInfoRow"><span class="key">{t}Город{/t}:</span><span class="val">{$pickpoint->getCity()}</span></div>
<div class="deliveryInfoRow"><span class="key">{t}Адрес{/t}:</span><span class="val">{$pickpoint->getAddress()}</span></div>
<div class="deliveryInfoRow"><span class="key">{t}Время работы{/t}:</span><span class="val">{$pickpoint->getWorktime()}</span></div>
<div class="deliveryInfoRow"><span class="key">{t}Тел.{/t}:</span><span class="val">{$pickpoint->getPhone()}</span></div>

{* Если есть ограничение оплаты налиными *}
{$cash_on_delivery=$pickpoint->getCashOnDelivery()}
{if !empty($cash_on_delivery) && $cash_on_delivery != "undefined"}
    <div class="deliveryInfoRow"><span class="key">{t}Ограничение оплаты наличными при получении{/t}:</span><span class="val">{$pickpoint->getCity()}</span></div>");
{/if}
{$note=$pickpoint->getNote()}
{if !empty($note)}
    <div class="deliveryInfoRow"><span class="key">{t}Заметка{/t}:</span><span class="val">{$pickpoint->getNote()}</span></div>
{/if}

<div class="deliveryInfoRow">
    {t}Неголосовой бесплатный контакт-центр{/t} 8 (800) 250-14-05
</div>