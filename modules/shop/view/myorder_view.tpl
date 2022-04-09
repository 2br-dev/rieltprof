{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
    {$catalog_config = ConfigLoader::byModule('catalog')}
    {$cart = $order->getCart()}
    {$products = $cart->getProductItems()}
    {$order_data = $cart->getOrderData()}

    <div class="col">
        <div class="row">
            <div class="col-md-7 mb-6 mb-md-0">
                <a class="return-link" href="{$router->getUrl('shop-front-myorders')}">
                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M14.7803 5.72846C15.0732 6.03307 15.0732 6.52693 14.7803 6.83154L9.81066 12L14.7803 17.1685C15.0732 17.4731 15.0732 17.9669 14.7803 18.2715C14.4874 18.5762 14.0126 18.5762 13.7197 18.2715L8.21967 12.5515C7.92678 12.2469 7.92678 11.7531 8.21967 11.4485L13.7197 5.72846C14.0126 5.42385 14.4874 5.42385 14.7803 5.72846Z"/>
                    </svg>
                    <span class="ms-2">{t}К списку заказов{/t}</span>
                </a>
                <h1 class="mb-lg-5 mb-4 mt-3 mt-lg-5">Заказ №{$order.order_num} от {$order.dateof|dateformat:"@date"}</h1>
                {if !empty($order.user_text)}
                    <div class="lk-order-warning">{$order.user_text}</div>
                {/if}
                <div class="mb-4">
                    {$status = $order->getStatus()}
                    <div class="lk-order-status"
                         style="color: white; background: {$status.bgcolor}">
                        {$status.title}
                    </div>
                </div>
                <div class="row mb-5 g-4">
                    {hook name="shop-myorder_view:order-info-items" title="{t}Просмотр заказа:информация о заказе{/t}"}
                    {if $delivery_title = $order->getDelivery()->title}
                        <div>
                            <div class="fw-bold mb-2">{t}Доставка{/t}</div>
                            <div>{$delivery_title}</div>
                        </div>
                    {/if}
                    {if $payment_title = $order->getPayment()->title}
                        <div>
                            <div class="fw-bold mb-2">{t}Оплата{/t}</div>
                            <div>{$payment_title}</div>
                        </div>
                    {/if}
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
                                {/if}
                            </div>
                        </div>
                    {/if}
                    {if $order.track_number}
                        <div>
                            <div class="fw-bold mb-2">{t}Трек-номер заказа{/t}</div>
                            <div>{$order.track_number}</div>
                        </div>
                    {/if}
                    {if $order.contact_person}
                        <div>
                            <div class="fw-bold mb-2">{t}Контактное лицо{/t}</div>
                            <div>{$order.contact_person}</div>
                        </div>
                    {/if}
                    {foreach $order->getFieldsManager()->getStructure() as $item}
                        <div>
                            <div class="fw-bold mb-2">{$item.title}</div>
                            <div>{$item.current_val}</div>
                        </div>
                    {/foreach}
                    {if $files = $order->getFiles()}
                        <div>
                            <div class="fw-bold mb-2">{t}Файлы{/t}</div>
                            <div>
                                {foreach $files as $file}
                                    <a href="{$file->getUrl()}" target="_blank">{$file.name}</a>{if !$file@last},{/if}
                                {/foreach}
                            </div>
                        </div>
                    {/if}
                    {$url = $order->getTrackUrl()}
                    {if !empty($url)}
                        <div>
                            <div class="fw-bold mb-2">{t}Ссылка для отслеживания заказа{/t}:</td>
                            <div>
                                <a href="{$url}" target="_blank">{t}Перейти к отслеживанию{/t}</a>
                            </div>
                        </div>
                    {/if}
                    {/hook}
                </div>
                <div class="row row-cols-md-auto row-cols-1 g-3">
                    {if $order->canOnlinePay()}
                        <div><a href="{$order->getOnlinePayUrl()}" class="btn btn-primary col-12">{t}Оплатить заказ{/t}</a></div>
                    {/if}
                    <div><a href="{$router->getUrl('shop-front-cartpage', ['Act'=>'repeatOrder', 'order_num' => $order.order_num])}" rel="nofollow" class="btn btn-outline-primary col-12">{t}Повторить заказ{/t}</a></div>
                    {if $order->canChangePayment()}
                        <div><a href="{$router->getUrl('shop-front-myorderview', ['Act'=>'changePayment', 'order_id' => $order.order_num])}" rel="nofollow" class="btn btn-outline-primary col-12 rs-in-dialog">{t}Изменить оплату{/t}</a></div>
                    {/if}
                </div>
            </div>
            <div class="col">
                <div class="order-items mb-xl-6 mb-4">
                    <div role="button" class="order-items__title collapsed" id="orderItemsTitle" data-bs-toggle="collapse" data-bs-target="#orderItemsList">{t n=count($products)}Товаров в заказе: %n{/t}</div>
                    <div class="collapse" id="orderItemsList" data-bs-target="orderItemsTitle">
                        <div class="pt-4">
                            <ul class="order-items__list">
                                {foreach $order_data.items as $key => $item}
                                    {$product = $products[$key].product}
                                    {$multioffer_titles = $item.cartitem->getMultiOfferTitles()}
                                    <li>
                                        <a {if $product.id}href="{$product->getUrl()}"{/if}>
                                            <div class="mb-2">
                                                {hook name="shop-myorder_view:product-info-items" title="{t}Просмотр заказа:информация о товаре{/t}"}
                                                {$item.cartitem.title} {$item.cartitem.model}

                                                {if !empty($multioffer_titles)}
                                                    <div class="order-items__multioffers">
                                                    {foreach $multioffer_titles as $multioffer}
                                                        <div class="order-items__multioffer">
                                                            <span>{$multioffer.title} -</span>
                                                            <span>{$multioffer.value}</span>
                                                        </div>
                                                    {/foreach}
                                                    </div>
                                                {/if}
                                                {/hook}
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-gray">{$item.cartitem.amount} {$item.cartitem->getUnit()->stitle|default:$item.cartitem.extra_arr.unit}</div>
                                                {if $item.discount_unformated > 0}
                                                    <small class="col d-flex justify-content-end old-price no-wrap" title="{t}Скидка{/t}">
                                                        {$item.cost|format_price} {$order.currency_stitle}
                                                    </small>
                                                {/if}
                                                <div class="fw-bold ms-3 text-body no-wrap">
                                                    {$item.cost_with_discount} {$order.currency_stitle}
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="lk-order-total">
                    {foreach $order_data.other as $item}
                        <div class="lk-order-total__item">
                            <div class="me-3">{$item.cartitem.title}</div>
                            <div class="no-wrap fw-bold">{if $item.total >0}{$item.total}{/if}</div>
                        </div>
                    {/foreach}
                    <div class="border-top pt-3 lk-order-total__item">
                        <div class="me-3">{t}Итого{/t}</div>
                        <div class="no-wrap fw-bold">{$order_data.total_cost}</div>
                    </div>
                    {if $order->getPayment()->hasDocs()}
                        {$type_object=$order->getPayment()->getTypeObject()}
                        {foreach $type_object->getDocsName() as $key=>$doc}
                            <div class="d-flex justify-content-end">
                                <a href="{$type_object->getDocUrl($key)}" target="_blank">{$doc.title}</a>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/block}