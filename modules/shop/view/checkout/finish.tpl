{$user = $order->getUser()}
<section class="section">
    <div class="container">
        <div class="col-xl-6 col-lg-8 col-md-10">
    <p>{t number=$order.order_num}Заказ №%number успешно оформлен.{/t}
        {if $user.id > 0}{t link=$router->getUrl('shop-front-myorders')}Следить за изменениями статуса заказа можно в разделе <a href="%link" target="_blank">история заказов</a>.{/t}{/if}
        {t}Все уведомления об изменениях в данном заказе будут отправлены на электронную почту покупателя.{/t}</p>

    <div class="accordion accordion-checkout-finish mt-4">
        <div class="accordion-item">
            <div class="accordion-header" id="accordionHeadCheckout-1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#accordionContentCheckout-1">
                    <span class="me-2">{t}Сведения о заказе{/t}</span>
                </button>
            </div>
            <div id="accordionContentCheckout-1" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <ul class="last-child-margin-remove list-unstyled">
                        <li class="mb-3">
                            <div class="fw-bold">{t}Дата заказа{/t}:</div>
                            <div class="text-gray">{$order.dateof|dateformat:"@date @time"}</div>
                        </li>
                        <li class="mb-3">
                            <div class="fw-bold">{t}Заказчик{/t}:</div>
                            <div class="text-gray">{$user.surname} {$user.name} {$user.midname}</div>
                        </li>
                        {if $user.phone}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Телефон{/t}:</div>
                                <div class="text-gray">{$user.phone}</div>
                            </li>
                        {/if}
                        {if $user.e_mail}
                            <li class="mb-3">
                                <div class="fw-bold">E-mail:</div>
                                <div class="text-gray">{$user.e_mail}</div>
                            </li>
                        {/if}

                        {$fmanager = $order->getFieldsManager()}
                        {if $fmanager->notEmpty()}
                            {foreach $fmanager->getStructure() as $field}
                                <li class="mb-3">
                                    <div class="fw-bold">{$field.title}</div>
                                    <div class="text-gray">{$fmanager->textView($field.alias)}</div>
                                </li>
                            {/foreach}
                        {/if}

                        {$delivery = $order->getDelivery()}
                        {$address = $order->getAddress()}
                        {$pay = $order->getPayment()}

                        {if $order.delivery}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Доставка{/t}:</div>
                                <div class="text-gray">{$delivery.title}</div>
                            </li>
                        {/if}

                        {$pvz = $order->getSelectedPvz()}
                        {if $pvz}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Пункт самовывоза{/t}:</div>
                                <div class="text-gray">{$pvz->getFullAddress()}</div>
                            </li>
                        {elseif $order.only_pickup_points && $order.warehouse}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Пункт самовывоза{/t}:</div>
                                <div class="text-gray">{$order->getWarehouse()->adress}</div>
                            </li>
                        {elseif $order.use_addr}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Адрес{/t}:</div>
                                <div class="text-gray">{$address->getLineView()}</div>
                            </li>
                        {/if}

                        {if $order.payment}
                            <li class="mb-3">
                                <div class="fw-bold">{t}Способ оплаты{/t}:</div>
                                <div class="text-gray">{$pay.title}</div>
                            </li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>

        {if $order->getPayment()->hasDocs()}
            <div class="accordion-item">
                <div class="accordion-header" id="accordionHeadCheckout-2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#accordionContentCheckout-3">
                        <span class="me-2">{t}Документы на оплату{/t}</span>
                    </button>
                </div>
                <div id="accordionContentCheckout-3" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        {if $user.id > 0}
                            <p class="helpText underText">{t alias="Конец оформления заказа, документы на оплату" link=$router->getUrl('shop-front-myorders')}Воспользуйтесь следующими документами для оплаты заказа. Эти документы всегда доступны в разделе <a href="%link" target="_blank">история заказов</a>{/t}</p>
                        {/if}

                        {$type_object = $order->getPayment()->getTypeObject()}
                        {foreach $type_object->getDocsName() as $key => $doc}
                            <div><a href="{$type_object->getDocUrl($key)}" target="_blank" class="text-decoration-none d-flex align-items-center">
                                    <img src="{$THEME_IMG}/icons/download.svg" width="40" height="40" alt="">
                                    {$doc.title}</a></div>
                        {/foreach}

                    </div>
                </div>
            </div>
        {/if}

        <div class="accordion-item">
            <div class="accordion-header" id="accordionHeadCheckout-2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#accordionContentCheckout-2">
                    <span class="me-2">{t}Состав заказа{/t}</span>
                </button>
            </div>
            <div id="accordionContentCheckout-2" class="accordion-collapse collapse">
                <div class="accordion-body">
                    {$orderdata = $cart->getOrderData()}
                    {$products = $cart->getProductItems()}
                    {hook name="shop-checkout-finish:products" title="{t}Завершение заказа:товары{/t}"}
                        {foreach $orderdata.items as $n=>$item}
                            {$orderitem=$item.cartitem}
                            {$product=$item.product}
                            {$barcode=$orderitem.barcode}
                            {$offer_title=$orderitem.model}
                            {$multioffer_titles=$orderitem->getMultiOfferTitles()}

                            <div class="checkout-finish-item">
                        <div class="row g-2">
                            <div class="d-flex col-sm-7">
                                <div class="checkout-finish-item__img">
                                    {if $products[$n]}
                                        <img src="{$products[$n].product->getOfferMainImage($orderitem.offer, 64, 64)}"
                                             srcset="{$products[$n].product->getOfferMainImage($orderitem.offer, 128, 128)} 2x" alt="" loading="lazy">
                                    {/if}
                                </div>
                                <div>
                                    <div class="mb-2">
                                        {$orderitem.title}
                                    </div>
                                    <div class="fs-5 row g-2 row-cols-auto">
                                        {if $barcode != ''}
                                            <div><span class="text-gray">{t}Артикул{/t}:</span> {$barcode}</div>
                                        {/if}
                                        {if $multioffer_titles || $offer_title}
                                            <div>
                                                {foreach $multioffer_titles as $multioffer}
                                                    <span class="text-gray">{$multioffer.title}:</span> {$multioffer.value}
                                                {/foreach}
                                                {if !$multioffer_titles}
                                                    {$offer_title}
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 col-auto text-sm-center text-gray">{$orderitem.amount} {$orderitem->getUnit()->stitle}</div>
                            <div class="col-sm-3 col fw-bold text-nowrap text-end">
                                {if $item.discount_unformated > 0}
                                    <span class="old-price">{$orderitem.price|format_price}</span>
                                {/if}
                                {$item.total}
                            </div>
                        </div>
                    </div>
                        {/foreach}
                    {/hook}
                </div>
            </div>
        </div>
    </div>
    <ul class="checkout-total mt-3 mt-sm-0">
        {foreach $orderdata.other as $item}
            <li>
                <div class="text-gray me-3">{$item.cartitem.title}:</div>
                <div class="text-nowrap">
                    {if (float)$item.total != 0}
                        {$item.total}
                    {/if}
                </div>
            </li>
        {/foreach}
        <li>
            <div class="text-gray me-3">{t}Итого{/t}:</div>
            <div class="fs-2 fw-bold text-nowrap">{$orderdata.total_cost}</div>
        </li>
    </ul>
    <div class="d-flex justify-content-end mt-5">
        {if $order->canOnlinePay()}
            <a href="{$order->getOnlinePayUrl()}" class="btn btn-primary col-12 col-sm-auto">{t}Перейти к оплате{/t}</a>
        {else}
            <a href="{$router->getRootUrl()}" class="btn btn-primary col-12 col-sm-auto">{t}Завершить заказ{/t}</a>
        {/if}
    </div>
</div>
    </div>
</section>