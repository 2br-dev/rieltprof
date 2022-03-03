<div class="payment-result">
    <div class="payment-result_content fail">
        <img src="{$THEME_IMG}/icons/big-fail.svg">
        <h2>{t}Оплата не произведена{/t}</h2>
        <p class="link">
            {if $transaction->getOrder()->id}
                <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $transaction->getOrder()->order_num])}" class="colorButton">{t}перейти к заказу{/t}</a>
            {else}
                <a href="{$router->getUrl('shop-front-mybalance')}" class="colorButton">{t}перейти к лицевому счету{/t}</a>
            {/if}
        </p>
    </div>
</div>