{addcss file="{$mod_css}ordershipment.css" basepath="root"}{$elem->fillShipmentItems()}

{$items = $elem->getShipmentItems()}
{$base_currency = $elem->getBaseCurrency()}
<table class="rs-table">
    <thead>
        <tr>
            <th>{t}Название{/t}</th>
            <th>{t}Модель{/t}</th>
            <th>{t}Артикул{/t}</th>
            <th>{t}Количество{/t}</th>
            <th>{t}Стоимость{/t}</th>
        </tr>
    </thead>
    <tbody>
    {$total_sum = 0}
    {foreach $items as $item}
        {$order_item = $item->getOrderItem()}
            <tr>
                <td>{$order_item.title}</td>
                <td>{$order_item.model}</td>
                <td>{$order_item.barcode}</td>
                <td>{$item.amount}</td>
                <td>{$item.cost|format_price} {$base_currency.stitle}</td>
            </tr>
        {$total_sum = $total_sum + $item.cost}
    {/foreach}
    </tbody>
</table>
<div class="shipment-total-sum">
    {t}Сумма отгрузки{/t}: <span>{$total_sum|format_price}</span> {$base_currency.stitle}
</div>

