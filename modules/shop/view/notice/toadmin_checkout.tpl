{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$order=$data->order}
    {$delivery=$order->getDelivery()}
    {$address=$order->getAddress()}
    {$cart=$order->getCart()}
    {$order_data=$cart->getOrderData(true, false)}
    {$products=$cart->getProductItems()}
    {$user=$order->getUser()}
    {$pay=$order->getPayment()}
    {$hl=["n","hl"]}
    <style type="text/css">
    .order-table {
        border-collapse:collapse;
        border:1px solid #aaa;
    }

    .order-table td {
        padding:3px;
        border:1px solid #aaa;
    }
    </style>
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    {t url=$url->getDomainStr()}На сайте %url оформлен заказ.{/t}<br>
    {t link=$router->getAdminUrl('edit', ["id" => $order.id], 'shop-orderctrl', true) order_num=$order.order_num}Номер заказа: <a href="%link"><strong>%order_num</strong></a> от{/t} <strong>{$order.dateof|dateformat:"@date @time:@sec"}</strong>

    <h3 style="margin:10px 0;">{t}Покупатель{/t}</h3>
    <table class="order-table">
            {if $user.is_company}
                <tr class="{cycle values=$hl name="user"}">
                    <td class="otitle">
                        {t}Наименование компании{/t}:
                    </td>
                    <td>{$user.company}</td>
                </tr>
                <tr class="{cycle values=$hl name="user"}">
                    <td class="otitle">
                        {t}ИНН компании{/t}:
                    </td>
                    <td>{$user.company_inn}</td>
                </tr>
            {/if}
            <tr class="{cycle values=$hl name="user"}">
                <td class="otitle">
                    {t}Фамилия Имя Отчество{/t}:
                </td>
                <td>{$user.surname} {$user.name} {$user.midname} ({if $order.user_id>0}{$user.id}{else}{t}Без регистрации{/t}{/if})</td>
            </tr>
            {if !empty($user.sex)}
                <tr class="{cycle values=$hl name="user"}">
                    <td class="otitle">
                        {t}Пол{/t}:
                    </td>
                    <td>{$user.__sex->textView()}</td>
                </tr>
            {/if}
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

    <h3 style="margin:10px 0;">{t}Информация о заказе{/t}</h3>
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
        {$fm=$order->getFieldsManager()}
        {foreach from=$fm->getStructure() item=item}
            <tr class="{cycle values=$hl name="order"}">
                <td class="otitle">
                    {$item.title}
                </td>
                <td>{$item.current_val}</td>
            </tr>
        {/foreach}
    </table>

    {if $order.delivery}
        <h3 style="margin:10px 0;">{t}Доставка{/t}</h3>
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
            {$pvz = $order->getSelectedPvz()}
            {if $pvz}
                <tr class="{cycle values=$hl name="delivery"}">
                    <td class="otitle">{t}Пункт самовывоза{/t}</td>
                    <td class="d_address">{$pvz->getAddress()}</td>
                </tr>
            {elseif $delivery->getTypeObject()->isMyselfDelivery()}
                {$warehouse=$data->order->getWarehouse()}
                <tr class="{cycle values=$hl name="delivery"}">
                    <td class="otitle">{t}Склад самовывоза{/t}</td>
                    <td class="d_address">"{$warehouse.title}" (Адрес: {$warehouse.adress})</td>
                </tr>
            {/if}
        </table>
    {/if}


    {if $order.payment}
        <h3 style="margin:10px 0;">{t}Оплата{/t}</h3>
        <table class="order-table">
                <tr class="{cycle values=$hl name="payment"}">
                    <td width="20" class="otitle">
                        {t}Тип{/t}
                    </td>
                    <td>{$pay.title}</td>
                </tr>
                {if $pay->hasDocs()}
                <tr class="{cycle values=$hl name="payment"}">
                    <td width="20" class="otitle">
                        {t}Документы на оплату{/t}
                    </td>
                    <td>
                        {$type_object=$pay->getTypeObject()}
                        {foreach from=$type_object->getDocsName() key=key item=doc}
                        <a href="{$type_object->getDocUrl($key, true)}" target="_blank">{$doc.title}</a><br>
                        {/foreach}
                    </td>
                </tr>
                {/if}
                <tr class="{cycle values=$hl name="payment"}">
                    <td class="otitle">
                        {t}Заказ оплачен?{/t}
                    </td>
                    <td>
                        {if $order.is_payed}{t}Да{/t}{else}{t}Нет{/t}{/if}
                    </td>
                </tr>
        </table>
    {/if}
    <br><br>
    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        <thead>
        <tr>
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
                {$product=$products[$n].product}
                <tr data-n="{$n}" class="item">
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
                    <td colspan="5">{$item.cartitem.title}</td>
                    <td>{if $item.total>0}{$item.total}{/if}</td>
                </tr>
            {/foreach}
        </tbody>
        <tfoot>
            <tr class="last">
                <td colspan="2" class="tools"></td>
                <td class="total">{t}Вес{/t}: <span class="total_weight">{$order_data.total_weight}</span></td>
                <td></td>
                <td></td>
                <td class="total">
                    {t}Итого{/t}: <span class="summary">{$order_data.total_cost}</span>
                </td>
            </tr>
        </tfoot>
    </table>
{/block}