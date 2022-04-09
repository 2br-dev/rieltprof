{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
{addjs file="%shop%/rscomponent/returns.js"}
{$return_items = $return.return_items}
{$order = $return->getOrder()}
{$order_data = $return->getOrderData(false)}

<div class="col">
    <h1 class="mb-md-5 mb-3">{t}Заявление на возврат по заказу №{$order.order_num} от {$order.dateof|dateformat:"@date"}{/t}</h1>
    <form method="POST" action="{urlmake}">
        {csrf}
        {$this_controller->myBlockIdInput()}
        <input type="hidden" name="order" value="{$order.order_num}">
        {if isset($return)}<input type="hidden" name="edit" value="{$return.id}">{/if}
        <div class="section pt-0">
            <p class="mb-6 col-xl-11">{t url={$router->getUrl('shop-front-myproductsreturn', ['Act' => 'rules'])} alias="Правила возврата товаров"}С помощью данного раздела, вы сможете оформить заявку на возврат товара, а также распечатать бланк заявления на возврат товара. После оформления заявки с вами свяжется менеджер и расскажет о дальнейших действиях. Пожалуйста, ознакомьтесь с <a class="rs-in-dialog" href="%url">правилами возврата товаров</a> перед оформлением заявки.{/t}</p>

            {foreach $return->getNonFormErrors() as $error}
                <div class="alert alert-danger">{$error}</div>
            {/foreach}

            <div class="lk-returns">
                <div class="lk-returns__title">{t}Товары доступные для возврата{/t}</div>
                <div class="lk-returns__head">
                    <div class="d-flex">
                        <div>
                            <div class="lk-return-checkbox"></div>
                        </div>
                        <div class="col">
                            <div class="row g-3">
                                <div class="col-sm-5">{t}Название{/t}</div>
                                <div class="col-sm-2">{t}Артикул{/t}</div>
                                <div class="col">{t}Количество{/t}</div>
                                <div class="col-auto">{t}Цена{/t}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="lk-returns__list">
                    {foreach $order_data.items as $item}
                        <li class="d-flex align-items-md-center"
                            data-uniq="{$item.cartitem.uniq}"
                            data-price="{$item.single_cost_with_discount}">
                            <div class="lk-return-checkbox">
                                <input class="checkbox rs-return-checkbox" type="checkbox"
                                       name="return_items[{$item.cartitem.uniq}][uniq]"
                                       value="{$item.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                            </div>
                            <div class="col">
                                <div class="row g-md-3 g-2 align-items-center align-items-md-start">
                                    <div class="col-md-5 col-12">
                                        {$item.cartitem.title}
                                        {if !empty($item.cartitem.model)}
                                            <div class="text-gray fs-6">{$item.cartitem.model}</div>
                                        {/if}
                                    </div>
                                    <div class="col-md-2 col-12">
                                        {$item.cartitem.barcode}
                                    </div>
                                    <div class="col">
                                        <div class="cart-amount rs-cart-amount">
                                            <button class="rs-step-down" type="button" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                                <svg width="12" height="12" viewBox="0 0 12 12"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.4752 6.46875H1.47516V4.96875H10.4752V6.46875Z"/>
                                                </svg>
                                            </button>
                                            <div class="cart-amount__input">
                                                {$step = $item.cartitem->getEntity()->getAmountStep()}
                                                <input type="number" class="rs-return-amount" value="{$return_items[$item.cartitem.uniq].amount|default:$step}" step="{$step}" min="{$step}" max="{$item.cartitem.amount}" name="return_items[{$item.cartitem.uniq}][amount]" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                                <span>{$item.cartitem->getUnit()->stitle}</span>
                                            </div>
                                            <button class="rs-step-up" type="button" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                                <svg width="12" height="12" viewBox="0 0 12 12"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.7326 6.94364H6.87549V10.8008H5.58978V6.94364H1.73264V5.65792H5.58978V1.80078H6.87549V5.65792H10.7326V6.94364Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        {*
                                        <select id="amount{$item.cartitem.uniq}" class="select rs-return-amount" name="return_items[{$item.cartitem.uniq}][amount]" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                            {$step = $item.cartitem->getEntity()->getAmountStep()}
                                            {$range = range($step, $item.cartitem.amount, $step)}
                                            {foreach $range as $amount}
                                                <option {if $return_items[$item.cartitem.uniq].amount == $amount}selected{/if}>{$amount}</option>
                                            {/foreach}
                                        </select>*}
                                    </div>
                                    <div class="fw-bold col-auto text-nowrap">{$item.single_cost_with_discount|format_price} {$return.currency_stitle}</div>
                                </div>
                            </div>
                        </li>
                    {/foreach}
                    {foreach $order_data.other as $key => $item}
                        {if $item.cartitem.type == 'delivery'}
                            <li class="d-flex align-items-md-center"
                                data-uniq="{$item.cartitem.uniq}"
                                data-price="{$item.total}">
                                <div class="lk-return-checkbox">
                                    <input class="checkbox rs-return-checkbox" type="checkbox"
                                           name="return_items[{$item.cartitem.uniq}][uniq]"
                                           value="{$item.cartitem.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                                </div>
                                <div class="col">
                                    <div class="row g-md-3 g-2 align-items-center align-items-md-start">
                                        <div class="col-md-5 col-12">
                                            {$item.cartitem.title}
                                        </div>
                                        <div class="col-md-2 col-12"></div>
                                        <div class="col">
                                            <input id="amount{$item.cartitem.uniq}"
                                                   type="hidden"
                                                   class="rs-return-amount"
                                                   name="return_items[{$item.cartitem.uniq}][amount]"
                                                   value="1" {if !isset($return_items[$item.cartitem.uniq])}disabled{/if}>
                                        </div>
                                        <div class="fw-bold col-auto text-nowrap">{$item.total|format_price} {$return.currency_stitle}</div>
                                    </div>
                                </div>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
            <div class="lk-returns__list mt-3 fw-bold fs-3">
                {t}Итого{/t}: <span class="rs-return-total">{$return.cost_total|format_price}</span> {$return.currency_stitle}
            </div>
        </div>
        <div>
            <h2>{t}Заявление{/t}</h2>
            <div class="col-xl-8">
                <div class="row g-3">
                    <div>
                        <div class="row">
                            <div class="col">
                                <label class="form-label">{t}Фамилия{/t}</label>
                                {$return->getPropertyView('surname')}
                            </div>
                            <div class="col">
                                <label class="form-label">{t}Имя{/t}</label>
                                {$return->getPropertyView('name')}
                            </div>
                            <div class="col">
                                <label class="form-label">{t}Отчество{/t}</label>
                                {$return->getPropertyView('midname')}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">{t}Причина возврата{/t}</label>
                        {$return->getPropertyView('return_reason')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}Серия паспорта{/t}</label>
                        {$return->getPropertyView('passport_series')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}Номер паспорта{/t}</label>
                        {$return->getPropertyView('passport_number')}
                    </div>
                    <div>
                        <label class="form-label">{t}Кем икогда выдан{/t}</label>
                        {$return->getPropertyView('passport_issued_by')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}Контактный телефон{/t}</label>
                        {$return->getPropertyView('phone')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}Наименование банка{/t}</label>
                        {$return->getPropertyView('bank_name')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}БИК{/t}</label>
                        {$return->getPropertyView('bik')}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{t}Расчетный счет{/t}</label>
                        {$return->getPropertyView('bank_account')}
                    </div>
                    <div>
                        <label class="form-label">{t}Корреспондентский счет{/t}</label>
                        {$return->getPropertyView('correspondent_account')}
                    </div>
                </div>
                <div class="mt-md-5 mt-4">
                    {if isset($return.id)}
                        <button class="btn btn-primary col-12 col-sm-auto"  name="formrefunds" type="submit" value="Подтвердить">{t}Подтвердить{/t}</button>
                        <a class="btn btn-outline-danger col-12 col-sm-auto mt-3 mt-sm-0" onclick="return confirm('{t}Вы действительно хотите удалить заявку?{/t}')" href="{$router->getUrl('shop-front-myproductsreturn', ['Act' => 'delete', 'return_id' => $return.return_num])}">{t}Удалить заявку{/t}</a>
                    {else}
                        <button class="btn btn-primary col-12 col-sm-auto"  name="formrefunds" type="submit" value="Подать заявку">{t}Подать заявку{/t}</button>
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div>
{/block}

{*
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
                    <label class="label-sup">{t}Расчетный счет{/t}</label>
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
*}