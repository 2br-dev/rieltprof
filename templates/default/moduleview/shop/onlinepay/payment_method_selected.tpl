<div class="payment-result">
    <div class="payment-result_content">
        <img id="rs-waitReceiptSuccessImg" src="{$THEME_IMG}/icons/big-success.svg" alt="">
        <h2 class="title-success">
            <span>{t}Метод оплаты выбран{/t}</span>
        </h2>
        <p class="descr link">
            <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $order.order_num])}" class="colorButton">{t}перейти к заказу{/t}</a>
        </p>
    </div>
</div>