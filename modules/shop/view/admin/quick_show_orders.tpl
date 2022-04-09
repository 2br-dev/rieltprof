{if $paginator->total}
    <div class="updatable" data-update-block-id="quick-orders" data-update-replace>
        {$back_url=$url->getSavedUrl("{$this_controller->getControllerName()}index")}
        <p>{t}Здесь отображаются другие отфильрованные в <a href="{$back_url}" class="u-link">административной панели заказы</a>{/t}</p>

        <div class="quick-orders">
            {foreach $orders as $order}
                <div class="m-b-20">
                    <a href="{adminUrl do="edit" id=$order.id}" class="f-18 va-m-c">
                        {$status = $order->getStatus()}
                        <span class="point-circle" style="background-color: {$status.bgcolor}" title="{$status.title}"></span>
                        <span>{t num=$order.order_num date="{$order.dateof|dateformat:"@date"}"}Заказ №%num от %date{/t}</span>
                    </a>
                    <p>{t price="{$order.totalcost|format_price}" curr=$order.currency_stitle}Сумма заказа: %price %curr{/t}<br>
                    {$order->getUser()->getFio()}</p>
                </div>
            {/foreach}
        </div>

        {include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line" noUpdateHash=true}
    </div>
{else}
    <div class="rs-side-panel__empty">
        {t}Нет заказов{/t}
    </div>
{/if}