{if $warehouses}
<div class="article">
    <h3>{t}Магазины в вашем регионе{/t}</h3>
    <table class="alliliateWhTable">
        <thead>
            <th>{t}Наименование, адрес{/t}</th>
            <th>{t}Телефон{/t}</th>
            <th>{t}Время работы{/t}</th>
        </thead>
        <tbody>
        {foreach $warehouses as $warehouse}
            <tr>
                <td><a href="{$router->getUrl('catalog-front-warehouse', ["id" => {$warehouse.alias|default:$warehouse.id}])}">{$warehouse.title}</a><br>
                    <span class="address">{$warehouse.adress}</span>
                </td>
                <td>{$warehouse.phone}</td>
                <td>{$warehouse.work_time}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{/if}