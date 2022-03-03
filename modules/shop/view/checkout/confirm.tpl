{assign var=catalog_config value=ConfigLoader::byModule('catalog')}
{if $order->hasError()}
<div class="pageError">
    {foreach from=$order->getErrors() item=item}
    <p>{$item}</p>
    {/foreach}
</div>
{/if}

<form method="POST">
    <div id="basket">
        <div class="orderInfo">
                {assign var=user value=$order->getUser()}
                
                <ul class="section first">
                    <li><span class="key">{t}Заказчик{/t}:</span> {$user.surname} {$user.name} {$user.midname}</li>
                    <li><span class="key">{t}Телефон{/t}:</span> {$user.phone}</li>
                    <li><span class="key">E-mail:</span> {$user.e_mail}</li>
                </ul>
                
                {assign var=fmanager value=$order->getFieldsManager()}
                {if $fmanager->notEmpty()}
                    <ul class="section">
                        {foreach from=$fmanager->getStructure() item=field}
                        <li><span class="key">{$field.title}:</span> <a href="?do=address">{$fmanager->textView($field.alias)}</a></li>
                        {/foreach}
                    </ul>
                {/if}
                
                {assign var=delivery value=$order->getDelivery()}
                {assign var=address value=$order->getAddress()}
                <ul class="section">
                    {if $order.delivery}
                        <li><span class="key">{t}Доставка{/t}:</span> <a href="{$router->getUrl(null, ['Act' => 'delivery'])}">{$delivery.title}</a></li>
                    {/if}
                    {if $order.only_pickup_points && $order.warehouse} {* Если только самовывоз *}
                        <li><span class="key">{t}Пункт самовывоза{/t}:</span> <a href="{$router->getUrl(null, ['Act' => 'address'])}">{$order->getWarehouse()->adress}</a></li>
                    {elseif $order.use_addr}
                        <li><span class="key">{t}Адрес{/t}:</span> <a href="{$router->getUrl(null, ['Act' => 'address'])}">{$address->getLineView()}</a></li>
                    {/if}
                </ul>
                {if $order.payment}
                    {assign var=pay value=$order->getPayment()}
                    <ul class="section last">
                        <li><span class="key">{t}Оплата{/t}:</span> <a href="{$router->getUrl(null, ['Act' => 'payment'])}">{$pay.title}</a></li>
                    </ul>
                {/if}
        </div>
        
        <div class="cartInfo">
            {assign var=products value=$cart->getProductItems()}
            {assign var=cartdata value=$cart->getCartData()}
            {hook name="shop-checkout-confirm:products" title="{t}Подтверждение заказа:товары{/t}"}
            <table class="orderBasket">
                <tbody class="head">
                    <tr>
                        <td></td>
                        <td>{t}Количество{/t}</td>
                        <td>{t}Цена{/t}</td>
                    </tr>
                </tbody>
                <tbody>
                    {foreach from=$products item=item key=n name="basket"}
                    <tr {if $smarty.foreach.basket.first}class="first"{/if} data-id="{$item.obj.id}">
                        <td>
                            {assign var=barcode value=$item.product->getBarCode($item.cartitem.offer)}
                            {assign var=offer_title value=$item.product->getOfferTitle($item.cartitem.offer)}
                            {assign var=multioffer_titles value=$item.cartitem->getMultiOfferTitles()}
                            <a href="{$item.product->getUrl()}" target="_blank" class="prod-name">{$item.cartitem.title}</a>
                            <div class="codeLine">
                                {if $barcode != ''}{t}Артикул{/t}:<span class="value">{$barcode}</span>&nbsp;&nbsp;{/if}
                                {if $multioffer_titles || ($offer_title && $item.product->isOffersUse())}
                                    <div class="multioffersWrap">
                                        {t}Комплектация{/t}:
                                        {foreach $multioffer_titles as $multioffer}
                                            <p class="value">{$multioffer.title} - {$multioffer.value}</p>
                                        {/foreach}
                                        {if !$multioffer_titles}
                                            <p class="value">{$offer_title}</p>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        </td>
                        <td class="amountCell">
                            {$item.cartitem.amount} 
                            {if $catalog_config.use_offer_unit}
                                {$item.product.offers.items[$item.cartitem.offer]->getUnit()->stitle}
                            {else}
                                {$item.product->getUnit()->stitle}
                            {/if}
                            {if !empty($cartdata.items[$n].amount_error)}<div class="errorNum" style="display:block">{$cartdata.items[$n].amount_error}</div>{/if}
                        </td>
                        <td class="totalPriceCell">
                            <span class="priceBlock">
                                <span class="priceValue">{$cartdata.items[$n].cost}</span>
                            </span>
                            <div class="discount">
                                {if $cartdata.items[$n].discount>0}
                                {t}скидка{/t} {$cartdata.items[$n].discount}
                                {/if}
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
                <tbody class="additional">
                    {foreach from=$cart->getCouponItems() key=id item=item}
                    <tr class="first">
                        <td colspan="2">{t}Купон на скидку{/t} {$item.coupon.code}</td>
                        <td></td>
                    </tr>
                    {/foreach}
                    {if $cartdata.total_discount>0}
                        <tr class="bold">
                            <td colspan="2">{t}Скидка на заказ{/t}</td>
                            <td class="price">{$cartdata.total_discount}</td>
                        </tr>
                    {/if}
                    {foreach from=$cartdata.taxes item=tax}
                        <tr {if !$tax.tax.included}class="bold"{/if}>
                            <td colspan="2">{$tax.tax->getTitle()}</td>
                            <td class="price">{$tax.cost}</td>
                        </tr>
                    {/foreach}
                    {if $order.delivery}
                        <tr class="bold">
                            <td colspan="2">{t}Доставка{/t}: {$delivery.title}</td>
                            <td class="price">{$cartdata.delivery.cost}</td>
                        </tr>
                    {/if}
                    {if $cartdata.payment_commission}
                        <tr class="bold">
                            <td colspan="2">{if $cartdata.payment_commission.cost>0}{t}Комиссия{/t}{else}{t}Скидка{/t}{/if} при оплате через "{$order->getPayment()->title}":</td>
                            <td class="price">{$cartdata.payment_commission.cost}</td>
                        </tr>
                    {/if}
                </tbody>
                <tfoot class="summary">
                    <tr>
                        <td colspan="2">{t}Итого{/t}</td>
                        <td>{$cartdata.total}</td>
                    </tr>
                </tfoot>
            </table>  
            {/hook}      
            
            <br>
            <div class="orderComment">
                <label>{t}Комментарий к заказу{/t}</label>
                {$order->getPropertyView('comments')}
            </div>
            <br>
            {if $this_controller->getModuleConfig()->require_license_agree}
            <input type="checkbox" name="iagree" value="1" id="iagree"> <label for="iagree">{t alias="Согласие с условиями" link=$router->getUrl('shop-front-licenseagreement')}Я согласен с <a href="%link" class="licAgreement inDialog">условиями предоставления услуг</a>{/t}</label>
            <script type="text/javascript">
                $(function() {
                    $('.formSave').click(function() {
                        if (!$('#iagree').prop('checked')) {
                            alert('{t}Подтвердите согласие с условиями предоставления услуг{/t}');
                            return false;
                        }
                    });
                });
            </script>
            {/if}
            <button class="formSave" type="submit">{t}Подтвердить заказ{/t}</button>
        </div>
    </div>
</form>