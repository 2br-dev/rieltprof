{addcss file="%shop%/returns.css"}
{addjs file="%shop%/returns.js"}
{$return_items=$return.return_items}
{$order=$return->getOrder()}
{$order_data=$return->getOrderData(false)}
<form method="POST" action="{urlmake}">
    <div class="page-responses form-style productsReturnTable">
        <h2 class="h2"><span>{t}Заявление на возврат товара{/t}</span></h2>
        <p class="returnsTable">
            {t url={$router->getUrl('shop-front-myproductsreturn', ['Act' => 'rules'])} alias="Правила возврата товаров"}
            С помощью данного раздела, вы сможете оформить заявку на возврат товара, а также распечатать бланк заявления на возврат товара. После оформления заявки с вами свяжется менеджер и расскажет о дальнейших действиях. Пожалуйста, ознакомьтесь с <a class="inDialog" href="%url">правилами возврата товаров</a> перед оформлением заявки.{/t}
        </p>
        <br/><br/>
        {csrf}
        {$this_controller->myBlockIdInput()}

        <input type="hidden" name="order" value="{$order.order_num}">
        {if isset($return)}<input type="hidden" name="edit" value="{$return.id}">{/if}
        {if !empty($return->getNonFormErrors())}
        <div class="pageError">
            {foreach $return->getNonFormErrors() as $error}
                <p>{$error}</p>
            {/foreach}
        </div>
        {/if}
        <table class="table returnsTable">
            <thead>
                <tr class="returnsHead">
                    <th></th>
                    <th>{t}Название{/t}</th>
                    <th class="mobileHide">{t}Артикул{/t}</th>
                    <th class="mobileHide">{t}Цена{/t}</th>
                    <th>{t}Кол-во{/t}</th>
                </tr>
            </thead>
            <tbody>
                {if $order_data.items}
                    {foreach $order_data.items as $item}
                        <tr>
                            <td>
                                <input class="productsReturnCheckbox" type="checkbox" data-uniq="{$item.cartitem.uniq}" data-price="{$item.single_cost_with_discount}" name="return_items[{$item.cartitem.uniq}][uniq]" value="{$item.cartitem.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                            </td>
                            <td>
                                {$item.cartitem.title}
                                {if !empty($item.cartitem.model)}
                                <p>{t}Комплектация{/t}<p>
                                    {$item.cartitem.model}
                                    {/if}
                            </td>
                            <td class="mobileHide">{$item.cartitem.barcode}</td>
                            <td class="mobileHide summ">{$item.single_cost_with_discount|format_price} {$return.currency_stitle}</td>
                            <td>
                                <select id="amount{$item.cartitem.uniq}" class="productsReturnAmount" name="return_items[{$item.cartitem.uniq}][amount]" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                    {$step=$item.cartitem->getEntity()->getAmountStep()}
                                    {$range=range($step, $item.cartitem.amount, $step)}
                                    {foreach $range as $amount}
                                        <option {if $return_items[$item.cartitem.uniq].amount == $amount}selected{/if}>{$amount}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                    {/foreach}
                    {foreach $order_data.other as $key => $item}
                        {if $item.cartitem.type == 'delivery'}
                            <tr>
                                <td>
                                    <input class="productsReturnCheckbox" type="checkbox" data-uniq="{$item.cartitem.uniq}" data-price="{$item.total}" name="return_items[{$item.cartitem.uniq}][uniq]" value="{$item.cartitem.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                                </td>
                                <td>
                                    {$item.cartitem.title}
                                </td>
                                <td></td>
                                <td>{$item.total|format_price} {$return.currency_stitle}</td>
                                <td>
                                    <input id="amount{$item.cartitem.uniq}" type="hidden" class="productsReturnAmount" name="return_items[{$item.cartitem.uniq}][amount]" value="1" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="5">{t}Нет товаров для возврата{/t}</td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    <div class="page-responses form-style">
        <br/>
        <br/>
        <h2 class="h2"><span>{t}Данные покупателя{/t}</span></h2>
        <table class="formTable">
            <tr>
                <td class="key">
                    {t}Имя{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('name')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Фамилия{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('surname')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Отчество{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('midname')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Причина возврата{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('return_reason')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Серия паспорта{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('passport_series')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Номер паспорта{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('passport_number')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Кем и когда выдан паспорт{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('passport_issued_by')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Номер телефона{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('phone')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Наименование банка{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('bank_name')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}БИК{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('bik')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Рассчетный счет{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('bank_account')}
                </td>
            </tr>
            <tr>
                <td class="key">
                    {t}Корреспондентский счет{/t}
                </td>
                <td class="value">
                    {$return->getPropertyView('correspondent_account')}
                </td>
            </tr>
        </table>
        {if isset($return.id)}
            <div class="form__bottom_buttons">
                <button class="formSave"  name="formrefunds" type="submit" value="Подтвердить">{t}Подтвердить{/t}</button>
                <a onclick="return confirm('{t}Вы действительно хотите удалить заявку?{/t}')" class="formSave delete" href="{$router->getUrl('shop-front-myproductsreturn', ['Act' => 'delete', 'return_id' => $return.return_num])}">{t}Удалить заявку{/t}</a>
            </div>
        {else}
            <button class="formSave"  name="formrefunds" type="submit" value="Подать заявку">{t}Подать заявку{/t}</button>
        {/if}
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $('.productsReturnAmount').styler();
    });
</script>