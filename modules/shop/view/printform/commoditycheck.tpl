<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link type="text/css" href="{$mod_css}commoditycheck.css" media="all" rel="stylesheet">
</head>
<body>

{$orders = (is_array($order)) ? $order : [$order]}

{foreach $orders as $order}
    {$delivery = $order->getDelivery()}
    {$address = $order->getAddress()}
    {$cart = $order->getCart()}
    {$order_data = $cart->getOrderData(true, false)}
    {$order_data_unformatted = $cart->getOrderData(false, false)}
    {$products = $cart->getProductItems()}
    {$user = $order->getUser()}

    <div class="oneOrder">
        <div class="centerColumn">
            
            <table class="headTable">
                <tr>
                    <td style="width:50%">
                        <img src="{$CONFIG.__logo->getUrl(200,100, 'xy')}">
                    </td>
                    <td class="aright" style="width:50%">
                        {if !empty($CONFIG.firm_name)}
                            {$CONFIG.firm_name}<br/>
                        {/if}
                        {if !empty($CONFIG.firm_inn)}
                            {t}ИНН{/t} {$CONFIG.firm_inn}<br/>
                        {/if}
                        {if !empty($CONFIG.firm_kpp)}
                            {t}КПП{/t} {$CONFIG.firm_kpp}<br/>
                        {/if}
                        {if !empty($CONFIG.admin_phone)}
                            {t alias='Сокращение "Телефон"'}Тел.{/t} {$CONFIG.admin_phone}<br/>
                        {/if}
                        {if !empty($SITE.admin_email)}
                            e-mail: {$CONFIG.admin_email}<br/>
                        {/if}
                        {if !empty($SITE.full_title)}
                            {t}сайт{/t}: {$SITE.full_title}
                        {/if}
                           
                    </td>
                </tr>
            </table>

            <p class="acenter h1">{t order_num=$order.order_num}Товарный чек №%order_num от{/t} {$order.dateof|dateformat:"d.m.Y"}</p>

            <table class="topTable" style="width:100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td>
                       №  
                    </td>
                    <td>
                       {t}Наименование{/t}
                    </td>
                    <td>
                       {t}Кол-во{/t} 
                    </td>
                    <td>
                       {t}Вес{/t} 
                    </td>
                    <td>
                       {t}Цена{/t} 
                    </td>
                    <td>
                       {t}Сумма{/t} 
                    </td>
                </tr>
                {if !empty($products)}
                    {$m=0}
                    {foreach $order_data.items as $n=>$item}
                        {$m=$m+1}
                        <tr>
                            <td>
                               {$m} 
                            </td>
                            <td>
                                <div>{$item.cartitem.title}</div>
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
                                    <div>{t}Модель{/t}: {$item.cartitem.model}</div>
                                {/if}
                                <div>
                                    {if $item.cartitem.barcode}<b>{t}Артикул{/t}: {$item.cartitem.barcode}</b>{/if} 
                                </div>
                            </td>
                            <td align="right">
                               {$item.cartitem.amount}
                            </td>
                            <td align="right">
                               {$item.cartitem.single_weight}
                            </td>
                            <td align="right">
                               {$item.single_cost}
                            </td>
                            <td align="right">
                               {$item.total}
                            </td>
                        </tr>
                    {/foreach}
                    {foreach from=$order_data.other key=n item=item}
                    <tr>
                        <td colspan="5">{$item.cartitem.title}</td>
                        <td align="right">{if $item.total>0}{$item.total}{/if}</td>
                    </tr>
                    {/foreach}
                {/if}
                <tr>
                    <td colspan="5" align="right">
                       <b>{t}ИТОГО{/t}:</b>
                    </td>
                    <td align="right">
                       <b>{$order_data.total_cost}</b>
                    </td>
                </tr>
            </table>
            
            {foreach from=$cartdata.taxes item=tax}
                {$tax.tax->getTitle()} {$tax.cost} <br/>
            {/foreach}
            
            {static_call var=total_cost_string callback=['\RS\Helper\Tools','priceToString'] params=[$order_data_unformatted.total_cost]}
            <p class="aright itogo"><b>{$total_cost_string}</b></p>
            <p>{t}Товар Покупателем осмотрен, комплектация проверена. К внешнему виду и комплектации претензий не имею.{/t}</p>
            <p class="pt40">{t}Отпустил{/t} __________________/___________________</p>
            <p class="pt40">{t}Покупатель{/t} ________________/___________________</p>
              
        </div>
    </div>
{/foreach}
 </body>
 </html>