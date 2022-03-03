<!doctype html>
{assign var=pay value=$order->getPayment()->getTypeObject()}
{assign var=user value=$order->getUser()}
{assign var=company value=$order->getShopCompany()}
{assign var=order_data value=$order->getCart()->getOrderData(true, false, false)}
{$qr_url=$bill->getQrCodeUrl()}
<html>
<head>
    <title>{t prefix=$pay->getOption('number_prefix') order_id=$order.order_num}Счет на оплату заказа N %prefix%order_id от{/t} {$order.dateof|date_format:"%d.%m.%Y"}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body { width: 210mm; margin-left: auto; margin-right: auto; border: 1px #efefef solid; font-size: 11pt;}
        table.invoice_bank_rekv { border-collapse: collapse; border: 1px solid; }
        table.invoice_bank_rekv > tbody > tr > td, table.invoice_bank_rekv > tr > td { border: 1px solid; }
        table.invoice_items { border: 1px solid; border-collapse: collapse;}
        table.invoice_items td, table.invoice_items th { border: 1px solid;}
    </style>
</head>
<body>
<table width="100%">
    <tr>
        <td>&nbsp;</td>

        <td style="width: 155mm;">
            <div style="width:155mm; "></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="text-align:center;  font-weight:bold; font-size:20px;">
                {t}Образец заполнения платежного поручения{/t}
            </div>
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        {if $qr_url}
            <td><img src="{$qr_url}" width="200" height="200"></td>
        {/if}
        <td>
            <table width="100%" cellpadding="2" cellspacing="2" class="invoice_bank_rekv">
    <tr>
        <td style="min-height:6mm; height:auto; width: 50mm;">
            <div>{t}ИНН{/t} {$company.firm_inn}</div>
        </td>
        <td style="min-height:6mm; height:auto; width: 55mm;">
            <div>{t}КПП{/t} {$company.firm_kpp}</div>
        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 25mm;">
            <div>{t alias='Сокращение "Счёт"'}Сч.{/t} №</div>

        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 60mm;">
            <div>{$company.firm_rs}</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="min-height:13mm; height:auto;">

            <table border="0" cellpadding="0" cellspacing="0" style="height: 13mm; width: 105mm;">

                <tr>
                    <td valign="top">
                        <div>{$company.firm_name}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size: 10pt;">{t}Получатель{/t}</div>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
<tr>
        <td colspan="2" rowspan="2" style="min-height:13mm; width: 105mm;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="height: 13mm;">
                <tr>
                    <td valign="top">

                        <div>{$company.firm_bank}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size:10pt;">{t}Банк получателя{/t}</div>
                    </td>
                </tr>

            </table>
        </td>
        <td style="min-height:7mm;height:auto; width: 25mm;">
            <div>{t}БИK{/t}</div>
        </td>
        <td rowspan="2" style="vertical-align: top; width: 60mm;">
            <div style=" height: 7mm; line-height: 7mm; vertical-align: middle;">{$company.firm_bik}</div>
            <div>{$company.firm_ks}</div>

        </td>
    </tr>
    <tr>
        <td style="width: 25mm;">
            <div>{t}Корр. сч.{/t} №</div>
        </td>
    </tr>
    
</table>
        </td>
    </tr>
</table>
<br/>

<div style="font-weight: bold; font-size: 16pt; padding-left:5px;">
    {t prefix=$pay->getOption('number_prefix') order_num=$order.order_num}Счет № %prefix%order_num от{/t} {$order.dateof|date_format:"%d.%m.%Y"}</div>

<br/>

<div style="background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>

<table width="100%">
    <tr>
        <td style="width: 30mm;">
            <div style=" padding-left:2px;">{t}Покупатель{/t}:    </div>
        </td>
        <td>

            <div style="font-weight:bold;  padding-left:2px;">
                {if $user.is_company}
                    {$user.company}, {t}ИНН{/t}: {$user.company_inn}, {t alias='Сокращение "Телефон"'}Тел.{/t}: {$user.phone}
                {else}
                    {$user.surname} {$user.name} {$user.midname}, {t alias='Сокращение "Телефон"'}Тел.{/t}: {$user.phone}
                {/if}
            </div>
        </td>
    </tr>
</table>


<table class="invoice_items" width="100%" cellpadding="2" cellspacing="2">
    <thead>
    <tr>

        <th style="width:13mm;">№</th>
        <th style="width:20mm;">{t}Код{/t}</th>
        <th>{t}Товар{/t}</th>
        <th style="width:20mm;">{t}Кол-во{/t}</th>
        <th style="width:17mm;">{t alias='Сокращение "Единиц"'}Ед.{/t}</th>
        <th style="width:27mm;">{t}Цена{/t}</th>

        <th style="width:27mm;">{t}Сумма{/t}</th>
    </tr>
    </thead>
    <tbody>
        {assign var=n value=1}
        {foreach from=$order_data.items item=item key=pkey}
        {$unit = $item.cartitem->getEntity()->getUnit()}
        <tr>
            <td align="center">{$n++}</td>
            <td align="left">{$item.cartitem.barcode}</td>
            <td align="left">{$item.cartitem.title}
                {if $item.cartitem.model}
                    <div>{$item.cartitem.model}</div>
                {/if}
            </td>
            <td align="right">{$item.cartitem.amount}</td>
            <td align="right">{$unit.stitle}</td>
            <td align="right">{$item.single_cost_with_discount}</td>
            <td align="right">{$item.total}</td>
        </tr>
        {/foreach}
        {foreach from=$order_data.other item=item key=pkey}
            {if $item.cartitem.type == 'subtotal'}{$has_tax=true}{/if}
            {if $item.cartitem.type != 'coupon'}
            <tr>
                <td colspan="6" align="left">{$item.cartitem.title}</td>
                <td align="right">{$item.total}</td>
            </tr>
            {/if}
        {/foreach}
    </tbody>

</table>

<table border="0" width="100%" cellpadding="1" cellspacing="1">
    <tr>
        <td></td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{t}Итого:{/t}</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{$order_data.total_cost}</td>
    </tr>
</table>

<br />
<div>
{t count=count($order_data.items) total=$order_data.total_cost}Всего наименований %count на сумму %total рублей.{/t}<br />
{if !$has_tax}{t}НДС не облагается{/t}{/if}
<strong>{$sumstr}</strong>
</div>
<br /><br />
<div style="background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>
<br/>

<div>{t}Руководитель{/t} ______________________ ({$company.firm_director})</div>
<br/>

{if !empty($company.firm_accountant)}
<div>{t}Главный бухгалтер{/t} ______________________ ({$company.firm_accountant})</div>
<br/>
{/if}

<div style="width: 85mm;text-align:center;">{if $pay->getOption('seal_url')}<img src="{$pay->getOption('seal_url')}">{else}М.П.{/if}</div>
<br/>


<div style="width:800px;text-align:left;font-size:10pt;">{t}Счет действителен к оплате в течение трех дней.{/t}</div>

</body>
</html>