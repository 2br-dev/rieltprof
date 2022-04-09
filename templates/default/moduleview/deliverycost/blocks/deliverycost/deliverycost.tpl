<p class="deliveryCostListTitle">{t}Стоимость доставки в{/t} <b>{t}{$city.title}{/t}</b> (<a data-href="{$router->getUrl('deliverycost-front-choosecityautocomplete', ['redirect' => urlencode($redirect)])}" class="inDialog">{t}Другой город{/t}</a>)</p>
{if !empty($list)}
    <ul class="deliveryCostListRows">
        {foreach $list as $item}
            {assign var=something_wrong value=$item->getTypeObject()->somethingWrong($order)} 
            {if !$something_wrong || ($something_wrong && $current_user->isAdmin())}
            <li class="deliveryCostListRow">
                <div class="key">
                    {$item.title}
                </div>
                <div class="val">
                    {if $something_wrong}
                        <span style="color:red;">{$something_wrong}</span>
                    {else}
                        {assign var=dcost value=$order->getDeliveryCostText($item)}
                        <span class="help">{$order->getDeliveryExtraText($item)}</span>                             
                        {if $dcost>0}
                            <span id="scost_{$item.id}" class="scost">{$dcost}</span>
                        {else}
                            {$dcost}
                        {/if}
                    {/if}
                </div>
                <div style="clear: both;"></div>
            </li>
            {/if}
        {/foreach}
    </ul>
{else}
    <p class="noDeliveryService">{t}Нет служб доставки, доставляющих данный товар{/t}</p>
{/if}