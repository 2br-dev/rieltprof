{* Оформление заказа. Шаг - Подтверждение заказа *}
{addjs file="rs.order.js"}

{$catalog_config=ConfigLoader::byModule('catalog')}
<div class="row page-registration-steps t-registration-steps">

        {* Текущий шаг оформления заказа *}
        {moduleinsert name="\Shop\Controller\Block\CheckoutStep"}

        <div class="form-style t-registration-end">

            <form class="t-order t-order_confirm-box" method="POST">
                <div class="col-xs-12 col-md-9 t-order_confirm-box_items">

                    {if $order->hasError()}
                        <div class="page-error">
                            {foreach $order->getErrors() as $item}
                                <p>{$item}</p>
                            {/foreach}
                        </div>
                    {/if}

                    <div class="t-order_confirm">
                        <div class="t-order_head">
                            <h3 class="h3">{t}Состав заказа{/t}</h3>
                        </div>

                        {$products=$cart->getProductItems()}
                        {$cartdata=$cart->getCartData()}
                        <div class="t-order_products">
                            {hook name="shop-checkout-confirm:products" title="{t}Подтверждение заказа:товары{/t}"}

                            {foreach $products as $n=>$item}
                                <div class="card card-order-product">
                                    {$barcode=$item.product->getBarCode($item.cartitem.offer)}
                                    {$offer_title=$item.product->getOfferTitle($item.cartitem.offer)}
                                    {$multioffer_titles=$item.cartitem->getMultiOfferTitles()}

                                    <div class="card-text">
                                        <a href="{$item.product->getUrl()}">{$item.cartitem.title}</a>
                                        <div class="code-line">
                                            {if $barcode != ''}{t}Артикул{/t}:<span class="value">{$barcode}</span><br>{/if}
                                            {if $multioffer_titles || ($offer_title && $item.product->isOffersUse())}
                                                <div class="multioffers-wrap">
                                                    {foreach $multioffer_titles as $multioffer}
                                                        <p class="value">{$multioffer.title} - <strong>{$multioffer.value}</strong></p>
                                                    {/foreach}
                                                    {if !$multioffer_titles}
                                                        <p class="value"><strong>{$offer_title}</strong></p>
                                                    {/if}
                                                </div>
                                            {/if}
                                        </div>
                                    </div>

                                    <div class="card-quantity">
                                        {$item.cartitem.amount}
                                        {if $catalog_config.use_offer_unit}
                                            {$item.product.offers.items[$item.cartitem.offer]->getUnit()->stitle}
                                        {else}
                                            {$item.product->getUnit()->stitle}
                                        {/if}

                                        {if !empty($cartdata.items[$n].amount_error)}<div class="error">{$cartdata.items[$n].amount_error}</div>{/if}
                                    </div>

                                    <div class="card-price">
                                        <span class="card-price_present">{$cartdata.items[$n].cost}</span>
                                        {if $cartdata.items[$n].discount>0}
                                            <div class="card-price_discount">
                                            {t discount=$cartdata.items[$n].discount}скидка %discount{/t}
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            {/foreach}

                            {foreach $cart->getCouponItems() as $id=>$item}
                                <div class="card card-order-product">
                                    <div class="card-text">{t}Купон на скидку{/t} {$item.coupon.code}</div>
                                    <div class="card-quantity"></div>
                                    <div class="card-price"></div>
                                </div>
                            {/foreach}
                            {if $cartdata.total_discount>0}
                                <div class="card card-order-product">
                                    <div class="card-text">{t}Скидка на заказ{/t}</div>
                                    <div class="card-quantity"></div>
                                    <div class="card-price">
                                        <span class="card-price_present">{$cartdata.total_discount}</span>
                                    </div>
                                </div>
                            {/if}
                            {foreach $cartdata.taxes as $tax}
                                <div class="card card-order-product {if !$tax.tax.included}bold{/if}">
                                    <div class="card-text">{$tax.tax->getTitle()}</div>
                                    <div class="card-quantity"></div>
                                    <div class="card-price">
                                        <span class="card-price_present">{$tax.cost}</span>
                                    </div>
                                </div>
                            {/foreach}
                            {if $order.delivery}
                                <div class="card card-order-product">
                                    <div class="card-text">{t}Доставка{/t}: {$delivery.title}</div>
                                    <div class="card-quantity"></div>
                                    <div class="card-price">
                                        <span class="card-price_present">{$cartdata.delivery.cost}</span>
                                    </div>
                                </div>
                            {/if}
                            {if $cartdata.payment_commission}
                                <div class="card card-order-product">
                                    <div class="card-text">{if $cartdata.payment_commission.cost>0}Комиссия{else}Скидка{/if} при оплате через "{$order->getPayment()->title}":</div>
                                    <div class="card-quantity"></div>
                                    <div class="card-price">
                                        <span class="card-price_present">{$cartdata.payment_commission.cost}</span>
                                    </div>
                                </div>
                            {/if}
                            {/hook}
                        </div>

                        <div class="t-order_comments">
                            <h3 class="h3">{t}Комментарий к заказу{/t}</h3>
                            <div class="form-group">
                                {$order->getPropertyView('comments')}
                            </div>

                            {if $is_agreement_require=$this_controller->getModuleConfig()->require_license_agree}
                                <input type="checkbox" name="iagree" value="1" id="iagree">
                                <label for="iagree">{t}Я согласен с <a href="{$router->getUrl('shop-front-licenseagreement')}" class="rs-in-dialog">условиями предоставления услуг</a>{/t}</label>
                            {/if}
                        </div>

                        <div class="t-order-card_end-sum">
                            <p>{t}Сумма заказа{/t}:</p>
                            <span>{$cartdata.total}</span>
                        </div>
                        <div class="t-order_button-block">
                            <button type="submit" class="link link-more{if $is_agreement_require} disabled{/if}">{t}Подтвердить заказ{/t}</button>
                            <a href="{$router->getRootUrl()}" class="link link-del">{t}Продолжить покупки{/t}</a>
                        </div>

                    </div>
                </div>

                <div class="col-xs-12 col-md-3 t-order_confirm-box_info">
                    <div class="sidebar t-order-sidebar">
                        <h3 class="h3">{t}Сведения о заказе{/t}</h3>

                        {$user=$order->getUser()}
                        <table class="t-order_table">
                            <tr>
                                <td class="key">{t}Заказчик{/t}</td>
                                <td>{$user.surname} {$user.name} {$user.midname}</td>
                            </tr>
                            <tr>
                                <td class="key">{t}Телефон{/t}</td>
                                <td>{$user.phone}</td>
                            </tr>
                            <tr class="preSep">
                                <td class="key">{t}E-mail{/t}</td>
                                <td>{$user.e_mail}</td>
                            </tr>
                            {$fmanager=$order->getFieldsManager()}
                            {if $fmanager->notEmpty()}
                                {foreach $fmanager->getStructure() as $field}
                                    <tr class="{if $field@first}postSep{/if} {if $field@last}preSep{/if}">
                                        <td class="key">{$field.title}</td>
                                        <td><a href="{$router->getUrl(null, ['Act' => 'address'])}">{$fmanager->textView($field.alias)}</a></td>
                                    </tr>
                                {/foreach}
                            {/if}
                            {$delivery=$order->getDelivery()}
                            {$address=$order->getAddress()}
                            {$pay=$order->getPayment()}

                            {if $order.delivery}
                                <tr class="postSep">
                                    <td class="key">{t}Доставка{/t}</td>
                                    <td><a href="{$router->getUrl(null, ['Act' => 'delivery'])}">{$delivery.title}</a></td>
                                </tr>
                            {/if}
                            {if $order.only_pickup_points && $order.warehouse}
                                <tr>
                                    <td class="key">{t}Пункт самовывоза{/t}</td>
                                    <td><a href="{$router->getUrl(null, ['Act' => 'address'])}">{$order->getWarehouse()->adress}</a></td>
                                </tr>
                            {elseif $order.use_addr}
                                <tr>
                                    <td class="key">{t}Адрес{/t}</td>
                                    <td><a href="{$router->getUrl(null, ['Act' => 'address'])}">{$address->getLineView()}</a></td>
                                </tr>
                            {/if}
                            {if $order.payment}
                                <tr>
                                    <td class="key">{t}Оплата{/t}</td>
                                    <td><a href="{$router->getUrl(null, ['Act' => 'payment'])}">{$pay.title}</a></td>
                                </tr>
                            {/if}
                        </table>

                    </div>
                </div>
            </form>

        </div>
</div>