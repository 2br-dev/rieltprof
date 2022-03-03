<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link type="text/css" href="{$mod_css}orderform.css" media="all" rel="stylesheet">
<body>

{$hl = ["n","hl"]}
{$orders = (is_array($order)) ? $order : [$order]}

{foreach $orders as $order}
    {$delivery = $order->getDelivery()}
    {$address = $order->getAddress()}
    {$cart = $order->getCart()}
    {$order_data = $cart->getOrderData(true, false)}
    {$products = $cart->getProductItems()}
    {$user = $order->getUser()}
    
    <div class="oneOrder">
        {include file="%shop%/printform/head.tpl"}
        <h1>{t order_num=$order.order_num}Заказ №%order_num от{/t} {$order.dateof}</h1>
        <div class="floatbox" style="margin-bottom:20px">
            <div class="o-leftcol {if !$order.delivery && !$order.payment}width100pr{/if}">
                <div class="bordered">
                    <h3>{t}Покупатель{/t}</h3>
                    <table class="order-table">
                            <tr class="{cycle values=$hl name="user"}">
                                <td class="otitle">
                                    {t}Фамилия Имя Отчество{/t}:
                                </td>
                                <td>
                                    {$user.surname} {$user.name} {$user.midname} {if $user.id}({$user.id}){/if}
                                    {if $user.is_company}<div class="company_info">{$user.company}, {t}ИНН{/t}: {$user.company_inn}</div>{/if}
                                </td>
                            </tr>
                            <tr class="{cycle values=$hl name="user"}">
                                <td class="otitle">
                                    {t}Пол{/t}:
                                </td>
                                <td>{$user.__sex->textView()}</td>
                            </tr>
                            <tr class="{cycle values=$hl name="user"}">
                                <td class="otitle">{t}Телефон{/t}:</td>
                                <td>{$user.phone}</td>
                            </tr>
                            <tr class="{cycle values=$hl name="user"}">
                                <td class="otitle">E-mail:</td>
                                <td>{$user.e_mail}</td>
                            </tr>
                            {foreach from=$user->getUserFields() item=item name=uf}
                            <tr class="{cycle values=$hl name="user"}">
                                <td class="otitle">{$item.title}</td>
                                <td>{$item.current_val}</td>
                            </tr>
                            {/foreach}
                    </table>
                </div>

                <br>
                <div class="bordered">
                    <h3>{t}Информация о заказе{/t}</h3>
                        <table class="order-table">
                            <tr class="{cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {t}Номер{/t}
                                </td>
                                <td>{$order.order_num}</td>
                            </tr>
                            <tr class="{cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {t}Дата оформления{/t}
                                </td>
                                <td>{$order.dateof}</td>
                            </tr>
                            <tr class="{cycle values=$hl name="order"}">
                                <td class="otitle">
                                    IP
                                </td>
                                <td>{$order.ip}</td>
                            </tr>
                            <tr class="status-bar {cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {t}Статус{/t}:
                                </td>
                                <td height="20"><strong id="status_text">{$order->getStatus()->title}</strong>
                                </td>
                            </tr>
                            <tr class="status-bar {cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {t}Заказ оформлен в валюте{/t}:
                                </td>
                                <td>{$order.currency}</td>
                            </tr>
                            <tr class="status-bar {cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {t}Комментарий к заказу{/t}:
                                </td>
                                <td>{$order.comments}</td>
                            </tr>
                            {foreach from=$order->getExtraInfo() item=item}
                            <tr class="status-bar {cycle values=$hl name="order"}">
                                <td class="otitle">
                                    {$item.title}:
                                </td>
                                <td>{$item.value}</td>
                            </tr>
                            {/foreach}
                            {assign var=fm value=$order->getFieldsManager()}
                            {foreach from=$fm->getStructure() item=item}
                                <tr class="{cycle values=$hl name="order"}">
                                    <td class="otitle">
                                        {$item.title}
                                    </td>
                                    <td>{$item.current_val}</td>
                                </tr>
                            {/foreach}
                        </table>
                </div>
                <br>
                <div><strong>{t}Комментарий администратора{/t}</strong> ({t}не отображается у покупателя{/t}): {$order.admin_comments}</div>
                
            </div> <!-- leftcol -->

            <div class="o-rightcol">
                <div class="padd">
                {if $order.delivery}
                    <div class="bordered">
                        <h3>{t}Доставка{/t}</h3>
                        <table class="order-table delivery-params">
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td width="20" class="otitle">
                                        {t}Тип{/t}
                                    </td>
                                    <td class="d_title">{$delivery.title}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td class="otitle">
                                        {t}Индекс{/t}
                                    </td>
                                    <td class="d_zipcode">{$address.zipcode}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td class="otitle">{t}Страна{/t}</td>
                                    <td class="d_country">{$address.country}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td class="otitle">{t}Край/область{/t}</td>
                                    <td class="d_region">{$address.region}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td class="otitle">{t}Город{/t}</td>
                                    <td class="d_city">{$address.city}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="delivery"}">
                                    <td class="otitle">{t}Адрес{/t}</td>
                                    <td class="d_address">{$address->getLineView(false)}</td>
                                </tr>
                                {if !empty($order.warehouse)}
                                    {$warehouse = $order->getWarehouse()}
                                    <tr>
                                        <td class="otitle">{t}Склад{/t}</td>
                                        <td class="d_warehouse">{$warehouse.title}</td>
                                    </tr>
                                {/if}
                        </table>
                    </div>
                    <br>
                {/if}
                {if $order.payment}
                    {assign var=pay value=$order->getPayment()}
                    <div class="bordered">
                        <h3>{t}Оплата{/t}</h3>
                        <table class="order-table">
                                <tr class="{cycle values=$hl name="payment"}">
                                    <td width="20" class="otitle">
                                        {t}Тип{/t}
                                    </td>
                                    <td>{$pay.title}</td>
                                </tr>
                                <tr class="{cycle values=$hl name="payment"}">
                                    <td class="otitle">
                                        {t}Заказ оплачен?{/t}
                                    </td>
                                    <td>
                                        {if $order.is_payed}{t}Да{/t}{else}{t}Нет{/t}{/if}
                                    </td>
                                </tr>
                        </table>
                    </div>
                    <br>
                {/if}
                </div>
            </div> <!-- right col -->
            
        </div> <!-- -->
        
        <table class="pr-table">
            <thead>
            <tr>
                <th></th>
                <th>{t}Наименование{/t}</th>
                <th>{t}Код{/t}</th>
                <th>{t}Вес{/t}</th>
                <th>{t}Цена{/t}</th>
                <th>{t}Кол-во{/t}</th>
                <th>{t}Стоимость{/t}</th>
            </tr>
            </thead>
            <tbody>
                {foreach from=$order_data.items key=n item=item}
                {assign var=product value=$products[$n].product}
                <tr data-n="{$n}" class="item">
                    <td>
                        <img src="{$product->getMainImage(36, 36, 'xy')}">
                    </td>
                    <td>
                        <b>{$item.cartitem.title}</b>
                        <br>
                        {$multioffers_values = unserialize($item.cartitem.multioffers)}
                        {if !empty($multioffers_values)}
                            <div class="parameters">
                                {$offer = array()}
                                {foreach $multioffers_values as $mo_value}
                                    {$offer[] = "{$mo_value.title}: {$mo_value.value}"} 
                                {/foreach}
                                {implode(', &nbsp; ', $offer)}
                            </div>
                        {elseif !empty($item.cartitem.model)}
                            {t}Модель{/t}: {$item.cartitem.model}
                        {/if}
                    </td>
                    <td>{$item.cartitem.barcode}</td>
                    <td>{$item.cartitem.single_weight}</td>
                    <td>{$item.single_cost}</td>
                    <td>{$item.cartitem.amount}</td>
                    <td>
                        <span class="cost">{$item.total}</span>
                        {if $item.discount>0}<div class="discount">{t}скидка{/t} {$item.discount}</div>{/if}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tbody class="additems">
                {foreach from=$order_data.other key=n item=item}
                <tr>
                    <td colspan="6">{$item.cartitem.title}</td>
                    <td>{if $item.total>0}{$item.total}{/if}</td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr class="last">
                    <td colspan="3" class="tools"></td>
                    <td class="total">{t}Вес{/t}: <span class="total_weight">{$order_data.total_weight}</span></td>
                    <td></td>
                    <td></td>
                    <td class="total">
                        {t}Итого{/t}: <span class="summary">{$order_data.total_cost}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
        <br><br>
        <label><strong>{t}Текст для покупателя{/t}</strong> ({t}данный текст будет виден покупателю на странице просмотра заказа{/t})</label>:
        {$order.user_text}
    </div>
{/foreach}
   
 </body>
 </html>