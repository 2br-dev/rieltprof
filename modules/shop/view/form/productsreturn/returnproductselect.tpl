{addcss file="%shop%/productsreturn/productsreturn.css"}
{addjs file="%shop%/productsreturn/productsreturn.js"}
{$return_items=$elem->getReturnItems()}
{$order_data=$elem->getOrderData(false)}
<table class="rs-table productsReturnTable">
    <thead>
        <tr>
            <th></th>
            <th>{t}Название{/t}</th>
            <th>{t}Модель{/t}</th>
            <th>{t}Артикул{/t}</th>
            <th>{t}Цена за единицу{/t}</th>
            <th>{t}Количество{/t}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $order_data.items as $item}
            {if isset($item.single_cost)}
                <tr>
                    <td>
                        <input class="productsReturnCheckbox" type="checkbox" data-uniq="{$item.cartitem.uniq}" data-price="{$item.single_cost_with_discount}" name="return_items[{$item.cartitem.uniq}][uniq]" value="{$item.uniq}" {if isset($return_items[$item.cartitem.uniq])}checked{/if}/>
                    </td>
                    <td>{$item.cartitem.title}</td>
                    <td>{$item.cartitem.model}</td>
                    <td>{$item.cartitem.barcode}</td>
                    <td>{$item.single_cost_with_discount|format_price} {$elem.currency_stitle}</td>
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
            {/if}
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
<div class="returnSumTotal">
    {t}Сумма возврата{/t}: <span id="returnTotal" class="total">{$elem.cost_total|format_price}</span> {$elem.currency_stitle}
</div>

<hr>
<table class="otable">
    <tr>
        <td class="otitle">{t}Создать чек возврата{/t}&nbsp;&nbsp;<a class="help-icon" data-placement="right" title="{t}Установите данный флажок, если вы желаете сразу выбить чек возврата для отмеченных товаров. Если не устанавливать флажок, то выбить чек можно вручную в разделе Управление -> Транзакции{/t}">?</a></td>
        <td><input type="checkbox" name="create_receipt_on_save" value="1" class="returnReceiptCheckbox"></td>
    </tr>
</table>