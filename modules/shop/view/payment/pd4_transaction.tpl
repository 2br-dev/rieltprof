<HTML>
<head>
<title>Квитанция Сбербанка</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{literal}
<style type="text/css">
H1 {font-size: 12pt;}
p, ul, ol, h1 {margin-top:6px; margin-bottom:6px} 
td {font-size: 9pt;}
small {font-size: 7pt;}
body {font-size: 10pt;}
</style>
{/literal}
</head>
<body>
{assign var=user value=$transaction->getUser()}
{assign var=company value=$transaction->getPayment()->getShopCompany()}
{assign var=sum value=explode('.', $transaction.cost)}
{assign var=sum_int value=$sum.0}
{assign var=sum_dec value=$sum.1}

<table style="width: 180mm; height: 145mm;" border="0" cellpadding="0" cellspacing="0">
{section loop=2 name=i}
<tbody>
    <tr valign="top">
    <td style="{if $smarty.section.i.first}border-style: solid none none solid; border-color: rgb(0, 0, 0) -moz-use-text-color -moz-use-text-color rgb(0, 0, 0); border-width: 1pt medium medium 1pt; width: 50mm; height: 70mm;{else}border-style: solid none solid solid; border-color: rgb(0, 0, 0) -moz-use-text-color rgb(0, 0, 0) rgb(0, 0, 0); border-width: 1pt medium 1pt 1pt; width: 50mm; height: 70mm;{/if}" align="center">
    <b>Извещение</b><br>

    {$qr_url=$formpd4->getQrCodeUrl()}
    {if $smarty.section.i.index == 0 && $qr_url}
        <div>
            <img src="{$qr_url}" width="200" height="200">
        </div>
        <font style="font-size: 13mm;">&nbsp;<br></font>
    {else}
        <font style="font-size: 53mm;">&nbsp;<br></font>
    {/if}

    <b>Кассир</b>
    </td>

    <td style="{if $smarty.section.i.first}border-style: solid solid none; border-color: rgb(0, 0, 0) rgb(0, 0, 0) -moz-use-text-color; border-width: 1pt 1pt medium;{else}border: 1pt solid rgb(0, 0, 0);{/if}    " align="center">
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td align="right"><small>{if $smarty.section.i.first}<i>Форма № ПД-4</i>{/if}</small></td>
            </tr>
            <tr>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0);">&nbsp;{$company.firm_name}</td>
            </tr>

            <tr>
                <td align="center"><small>(наименование получателя платежа)</small></td>
            </tr>
        </tbody></table>

        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0); width: 37mm;">&nbsp;{$company.firm_inn}</td>
                <td style="width: 9mm;">&nbsp;</td>

                <td style="border-bottom: 1pt solid rgb(0, 0, 0);">&nbsp;{$company.firm_rs}</td>
            </tr>
            <tr>
                <td align="center"><small>(ИНН получателя платежа)</small></td>
                <td><small>&nbsp;</small></td>
                <td align="center"><small>(номер счета получателя платежа)</small></td>
            </tr>
        </tbody></table>

        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td>в&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0); width: 73mm;">&nbsp;{$company.firm_bank}</td>
                <td align="right">БИК&nbsp;&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0); width: 33mm;">&nbsp;{$company.firm_bik}</td>
            </tr>
            <tr>
                <td></td>
                <td align="center"><small>(наименование банка получателя платежа)</small></td>
                <td></td>
                <td></td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td nowrap="nowrap" width="1%">Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0);" width="100%">&nbsp;{$company.firm_ks}</td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0); width: 80mm;">&nbsp;Пополнение баланса лицевого счёта №Т{$transaction.id} от {$transaction.dateof|dateformat:"@date"}</td>
                <td style="width: 2mm;">&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0);">&nbsp;</td>
            </tr>
            <tr>
                <td align="center"><small>(наименование платежа)</small></td>
                <td><small>&nbsp;</small></td>
                <td align="center"><small>(номер лицевого счета (код) плательщика)</small></td>
            </tr>
        </tbody></table>

        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td nowrap="nowrap" width="1%">Ф.И.О. плательщика&nbsp;&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0);" width="100%">&nbsp;{$user.surname} {$user.name} {$user.midname}</td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td nowrap="nowrap" width="1%">Адрес плательщика&nbsp;&nbsp;</td>
                <td style="border-bottom: 1pt solid rgb(0, 0, 0);" width="100%">&nbsp;</td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td>Сумма платежа&nbsp;<font style="text-decoration: underline;">&nbsp;{$sum_int}&nbsp;</font>&nbsp;руб.&nbsp;<font style="text-decoration: underline;">&nbsp;{$sum_dec}&nbsp;</font>&nbsp;коп.</td>

                <td align="right">&nbsp;&nbsp;Сумма платы за услуги&nbsp;&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп.</td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>

                <td align="right">&nbsp;&nbsp;«{$transaction.dateof|dateformat:"%d"}» {$transaction.dateof|dateformat:"%v"} {$transaction.dateof|dateformat:"Y"} г.</td>
            </tr>
        </tbody></table>
        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td><small>С условиями приема указанной в платежном документе суммы, 
                в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
            </tr>
        </tbody></table>

        <table style="width: 122mm; margin-top: 3pt;" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td align="right"><b>Подпись плательщика _____________________</b></td>
            </tr>
        </tbody></table>
    </td>
</tr>
{/section}

</tbody></table>
</body>
</HTML>