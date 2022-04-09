{* Личный кабинет - список моих заказов *}
{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
    {$paginator = $paginator_with_archive}

    <div class="col">
        <h1 class="mb-lg-5 mb-4">{t}Мои Заказы{/t}</h1>
        {if count($order_list_with_archive)}
            <div>
                {foreach $order_list_with_archive as $order}
                    {if !($order instanceof Shop\Model\Orm\ArchiveOrder)}
                        {$cart = $order->getCart()}
                        {$products = $cart->getProductItems()}
                        {$order_data = $cart->getOrderData()}
                    {/if}

                    <div class="lk-orders-item">
                        <div class="lk-orders-item__head">
                            <div class="lk-orders-item__title">{t num=$order.order_num date="{$order.dateof|dateformat:"@date"}"}Заказ № %num от %date{/t}</div>
                            <div class="lk-orders-item__head-products">
                                {if $order instanceof Shop\Model\Orm\ArchiveOrder}
                                    {$products_count = $order->getProductsCount()}
                                {else}
                                    {$products_count = count($products)}
                                {/if}
                                <div class="fs-5">{t n=$products_count}%n [plural:%n:товар|товара|товаров] на сумму:{/t}</div>
                                <div class="lk-orders-item__price">
                                    {if $order instanceof Shop\Model\Orm\ArchiveOrder}
                                        {$order.totalcost|format_price} {$order.currency_stitle}
                                    {else}
                                        {$order_data.total_cost}
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="lk-orders-item__body">
                            <div class="row row-cols-md-2 row-cols-1 g-4">
                                <div class="d-none d-md-block">
                                    <div class="row row-cols-2 g-4 fs-5 ">
                                        {if $delivery_title = $order->getDelivery()->title}
                                            <div>
                                                <div class="fw-bold mb-2">{t}Доставка{/t}</div>
                                                <div>{$delivery_title}</div>
                                            </div>
                                        {/if}
                                        {if $order_title = $order->getPayment()->title}
                                            <div>
                                                <div class="fw-bold mb-2">{t}Оплата{/t}</div>
                                                <div>{$order_title}</div>
                                            </div>
                                        {/if}

                                        {if !($order instanceof Shop\Model\Orm\ArchiveOrder)}
                                            {$pvz = $order->getSelectedPvz()}
                                            {if $pvz}
                                                <div>
                                                    <div class="fw-bold mb-2">{t}Пункт самовывоза{/t}</div>
                                                    <div>{$pvz->getFullAddress()}</div>
                                                </div>
                                            {elseif $order.use_addr || $order.warehouse}
                                                <div>
                                                    <div class="fw-bold mb-2">{t}Адрес получения{/t}</div>
                                                    <div>{if $order.use_addr}
                                                            {$order->getAddress()->getLineView()}
                                                        {elseif $order.warehouse}
                                                            {$order->getWarehouse()->adress}
                                                        {/if}</div>
                                                </div>
                                            {/if}
                                        {/if}

                                        {if $order.track_number}
                                            <div>
                                                <div class="fw-bold mb-2">{t}Трек-номер заказа{/t}</div>
                                                <div>{$order.track_number}</div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-md-end justify-content-between">
                                    <div class="row row-cols-md-auto g-3 justify-content-md-end align-items-center mb-4">
                                        <div>
                                            {$status = $order->getStatus()}
                                            <div class="lk-order-status"
                                                 style="color: white; background: {$status.bgcolor}">
                                                {$status.title}</div>
                                        </div>
                                        {if !($order instanceof Shop\Model\Orm\ArchiveOrder)}
                                            {if $order->getPayment()->hasDocs()}
                                                {$type_object = $order->getPayment()->getTypeObject()}
                                                {foreach $type_object->getDocsName() as $key=>$doc}
                                                    <div class="order-md-first"><a href="{$type_object->getDocUrl($key)}" target="_blank">{$doc.title}</a></div>
                                                {/foreach}
                                            {/if}
                                        {/if}
                                    </div>
                                    <div class="row row-cols-md-auto row-cols-1 g-3 justify-content-md-end align-items-center">
                                        {if $order instanceof Shop\Model\Orm\ArchiveOrder}
                                            <div>
                                                <button class="btn btn-warning btn-sm col-12 col-sm-auto" disabled>{t}Заказ перенесён в архив{/t}</button>
                                            </div>
                                        {else}
                                            {hook name="shop-myorders:actions" title="{t}Мои заказы:действия над одним заказом{/t}"}
                                            {if $order->canOnlinePay()}
                                                <div>
                                                    <a href="{$order->getOnlinePayUrl()}" class="btn btn-primary col-12 col-sm-auto">
                                                        {if $status.type == 'payment_method_selected' || $status.copy_type == 'payment_method_selected'}
                                                            {t}выбрать другую карту{/t}
                                                        {else}
                                                            {t}оплатить{/t}
                                                        {/if}
                                                    </a>
                                                </div>
                                            {/if}
                                            {/hook}
                                            <div>
                                                <a href="{$router->getUrl('shop-front-myorderview', ["order_id" => $order.order_num])}" class="btn btn-outline-primary col-12 col-sm-auto">{t}Подробнее о заказе{/t}</a>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
            {include file="%THEME%/paginator.tpl"}
        {else}
            {include file="%THEME%/helper/usertemplate/include/empty_list.tpl" reason="{t}Еще не оформлено ни одного заказа{/t}"}
        {/if}
    </div>
{/block}