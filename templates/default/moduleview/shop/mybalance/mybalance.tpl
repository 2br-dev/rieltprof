<ul class="balanceLine">
    <li class="balance">
        <span class="cap">{t}Баланс:{/t}</span> <strong>{$balance_string}</strong>
    </li>
    <li class="addfunds">
        <a href="{$router->getUrl('shop-front-mybalance', [Act=>addfunds])}"><img src="{$THEME_IMG}/addfunds.png" alt="">{t}Пополнить баланс{/t}</a>
    </li>
</ul>

<br><br>
<h2><span>{t}История операций{/t}</span></h2>

<table class="orderList balanceTable">
<thead>
    <tr>
        <th></th>
        <th></th>
        <th class="addFundsHead">{t}Приход{/t}</th>
        <th class="takeFundsHead">{t}Расход{/t}</th>
    </tr>
</thead>
<tbody>
{foreach from=$list item=item}
    <tr>
        <td class="date">№ {$item.id}<br>{$item.dateof|date_format:"d.m.Y H:i"}</td>
        <td class="reason">{$item->reason}</td>
        <td>
            {* Приход *}
            {if !$item->order_id && $item->cost > 0}
                <span class="scost">{$item->getCost(false, true)}</span>
            {/if}
        </td>
        <td>
            {* Расход *}
            
            {if $item->order_id}
                <span class="tcost">-{$item->getCost(false, true)}</span>
            {else}
                {if $item->cost < 0}
                    <span class="tcost">{$item->getCost(false, true)}</span>
                {/if}
            {/if}
        </td>
    </tr>
{/foreach}
</tbody>
</table>
<br><br>
{include file="%THEME%/paginator.tpl"}

