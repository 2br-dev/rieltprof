<div class="section 100vh">
    <div class="container col-xl-5 col-lg-7 col-md-8 col-sm-11 text-center">
        <div class="mb-md-5 mb-3">
            <img class="rs-waitReceiptSuccessImg" width="64" height="64" src="{$THEME_IMG}/decorative/success.svg" alt="">
        </div>
        <div class="mb-md-6 mb-5">
            {t}Метод оплаты выбран{/t}
        </div>

        <div class="row row-cols-sm-2 g-3 justify-content-center">
            <div>
                <a href="{$router->getUrl('shop-front-myorderview', ['order_id' => $order.order_num])}" class="btn btn-primary w-100">{t}перейти к заказу{/t}</a>
            </div>
        </div>
    </div>
</div>