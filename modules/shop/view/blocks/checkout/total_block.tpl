<div class="checkout_block">
    <table class="table-keyvalue">
        <tr>
            <td class="key">{t}Товаров на сумму{/t}</td>
            <td class="value text-nowrap text-right">{$cart_data.total_without_delivery}</td>
        </tr>
        {foreach $cart->getCouponItems() as $id=>$item}
            <tr>
                <td class="key">{t}Купон на скидку{/t} {$item.coupon.code}</td>
                <td class="value text-nowrap text-right"></td>
            </tr>
        {/foreach}
        {if $cart_data.total_discount>0}
            <tr>
                <td class="key">{t}Скидка на заказ{/t}</td>
                <td class="value text-nowrap text-right">{$cart_data.total_discount}</td>
            </tr>
        {/if}
        {foreach $cart_data.taxes as $tax}
            <tr {if !$tax.tax.included}class="bold"{/if}>
                <td class="key">{$tax.tax->getTitle()}</td>
                <td class="value text-nowrap text-right">{$tax.cost}</td>
            </tr>
        {/foreach}
        {if $order.delivery}
            <tr class="bold">
                <td class="key">{t}Доставка{/t}: {$delivery.title}</td>
                <td class="value text-nowrap text-right">{$cart_data.delivery.cost}</td>
            </tr>
        {/if}
        {if $cart_data.payment_commission}
            <tr class="bold">
                <td class="key">{if $cart_data.payment_commission.cost>0}{t}Комиссия{/t}{else}{t}Скидка{/t}{/if} {t}при оплате через{/t} "{$order->getPayment()->title}": </td>
                <td class="value text-nowrap text-right">{$cart_data.payment_commission.cost}</td>
            </tr>
        {/if}
    </table>

    <div class="checkout_totalTotal">
        <div class="t-order-total_wrapper">
            <p>{t}Итого{/t}:</p>
            <div class="t-order-total_price">
                <span>{$cart_data.total}</span>
            </div>
        </div>
    </div>
</div>