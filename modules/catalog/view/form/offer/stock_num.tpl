{$warehouses = $field->callPropertyFunction('getWarehousesList')}
{$config = ConfigLoader::byModule('catalog')}
<div class="table-mobile-wrapper no-bottom-border">
    {if $config.inventory_control_enable}
        <table class="offer-table">
            <tr class="offer-table-head">
                <td class="no-border"></td>
                <td>{t}Доступно{/t}</td>
                <td>{t}Остаток{/t}</td>
                <td>{t}Резерв{/t}</td>
                <td>{t}Ожидание{/t}</td>
            </tr>
            {foreach $warehouses as $warehouse}
                {$stocks = $elem->getStocks()}
                <tr class="offer-table-body">
                    <td class="warehouse-title">{$warehouse.title}</td>
                    <td>{(float)$stocks[$warehouse.id]['stock']}</td>
                    <td>{(float)$stocks[$warehouse.id]['remains']}</td>
                    <td>{(float)$stocks[$warehouse.id]['reserve']}</td>
                    <td>{(float)$stocks[$warehouse.id]['waiting']}</td>
                </tr>
            {/foreach}
        </table>
    {else}
        <table class="otable offer-stock-num">
            {foreach $warehouses as $warehouse}
                <tr>
                    <td class="otitle">{$warehouse.title}</td>
                    <td><input name="stock_num[{$warehouse.id}]" type="text" value="{$elem.stock_num[$warehouse.id]}"/></td>
                </tr>
            {/foreach}
        </table>
    {/if}
</div>
