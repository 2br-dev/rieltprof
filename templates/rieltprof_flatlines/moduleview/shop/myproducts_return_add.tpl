{addcss file="%shop%/returns.css"}
{addjs file="%shop%/returns.js"}
{$return_items=$return.return_items}
{$order=$return->getOrder()}
{$order_data=$return->getOrderData(false)}
<form class="form-style" method="POST" action="{urlmake}">
    <div class="page-responses form-style productsReturnTable">
        <h2 class="h2">{t}Заявление на возврат товара{/t}</h2>
        <br/><br/>
        <p class="returnsTable">
            {t url={$router->getUrl('shop-front-myproductsreturn', ['Act' => 'rules'])} alias="Правила возврата товаров"}
                С помощью данного раздела, вы сможете оформить заявку на возврат товара, а также распечатать бланк заявления на возврат товара. После оформления заявки с вами свяжется менеджер и расскажет о дальнейших действиях. Пожалуйста, ознакомьтесь с <a class="inDialog" href="%url">правилами возврата товаров</a> перед оформлением заявки.{/t}
        </p>

        {csrf}
        {$this_controller->myBlockIdInput()}

        <input type="hidden" name="order" value="{$order.order_num}">
        {if isset($return)}<input type="hidden" name="edit" value="{$return.id}">{/if}
        <div class="row">
            <div class="col-xs-12 col-md-12 col-lg-12 t-order_contact-information user-contacts">
                {foreach $return->getNonFormErrors() as $error}
                    <p class="formFieldError">{$error}</p>
                {/foreach}
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                            </th>
                            <th>{t}Название{/t}</th>
                            <th class="hidden-xs">{t}Артикул{/t}</th>
                            <th class="hidden-xs">{t}Цена{/t}</th>
                            <th>{t}Кол-во{/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $order_data.items as $item}
                            <tr>
                                <td>
                                    <input class="productsReturnCheckbox" type="checkbox" data-uniq="{$item.cartitem.uniq}" data-price="{$item.single_cost_with_discount}" name="return_items[{$item.cartitem.uniq}][uniq]" value="{$item.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                                </td>
                                <td>
                                    {$item.cartitem.title}
                                    {if !empty($item.cartitem.model)}
                                        <p>{t}Комплектация{/t}<p>
                                        {$item.cartitem.model}
                                    {/if}
                                </td>
                                <td class="hidden-xs">{$item.cartitem.barcode}</td>
                                <td class="hidden-xs">{$item.single_cost_with_discount|format_price} {$return.currency_stitle}</td>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="page-responses form-style">
        <div class="row">
            <div class="col-xs-12 col-md-12 col-lg-12 t-order_contact-information user-contacts">
                <div class="form-group">
                    <label class="label-sup">{t}Имя{/t}</label>
                    {$return->getPropertyView('name')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Фамилия{/t}</label>
                    {$return->getPropertyView('surname')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Отчество{/t}</label>
                    {$return->getPropertyView('midname')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Причина возврата{/t}</label>
                    {$return->getPropertyView('return_reason')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Серия паспорта{/t}</label>
                    {$return->getPropertyView('passport_series')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Номер паспорта{/t}</label>
                    {$return->getPropertyView('passport_number')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Кем и когда выдан паспорт{/t}</label>
                    {$return->getPropertyView('passport_issued_by')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Номер телефона{/t}</label>
                    {$return->getPropertyView('phone')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Наименование банка{/t}</label>
                    {$return->getPropertyView('bank_name')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}БИК{/t}</label>
                    {$return->getPropertyView('bik')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Рассчетный счет{/t}</label>
                    {$return->getPropertyView('bank_account')}
                </div>
                <div class="form-group">
                    <label class="label-sup">{t}Корреспондентский счет{/t}</label>
                    {$return->getPropertyView('correspondent_account')}
                </div>
            </div>
        </div>
        {if isset($return.id)}
            <div class="form__bottom_buttons">
                <button class="but-done orange"  name="formrefunds" type="submit" value="Подтвердить">{t}Подтвердить{/t}</button>
                <a onclick="return confirm('{t}Вы действительно хотите удалить заявку?{/t}')" class="but-done red" href="{$router->getUrl('shop-front-myproductsreturn', ['Act' => 'delete', 'return_id' => $return.return_num])}">{t}Удалить заявку{/t}</a>
            </div>
        {else}
            <button class="but-done orange"  name="formrefunds" type="submit" value="Подать заявку">{t}Подать заявку{/t}</button>
        {/if}
    </div>
</form>
