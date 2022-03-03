{$api = $document->getApi()}
{$offer_titles = $api->getOfferTitles($document.items)}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family:Arial;
            font-size:12px;
        }
        td{
            padding: 10px 20px 5px 5px;
            border: solid 1px black;
        }
        table {
            border-collapse: collapse;
        }
        .container{
            padding: 30px;
        }
        .center{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="center">{$title} № {$document.id} {t}от{/t} {$document.date|dateformat:"@date @time:@sec"}</h3>
    {if $document.comment}<p><b>{t}Комментарий:{/t}</b> {$document.comment}</p>{/if}
    {if  $is_document || $is_inventorization}
        <p><b>{t}Склад:{/t}</b> {$warehouse.title}</p>
    {elseif $is_movement}
        <p><b>{t}Со склада:{/t}</b> {$warehouse_from}</p>
        <p><b>{t}На склад:{/t}</b> {$warehouse_to}</p>
    {/if}
    {if $provider}<p><b>{t}Пставщик:{/t}</b> {$provider}</p>{/if}
    <h4>{t}Товары:{/t}</h4>
    <table>
        <tr>
            <td>{t}Наименование{/t}</td>
            <td class="center">{t}Комплектация{/t}</td>
            {if $is_document || $is_movement}
                <td class="center">{t}Количество{/t}</td>
            {elseif $is_inventorization}
                <td class="center">{t}Расчетное кол-во{/t}</td>
                <td class="center">{t}Фактическое кол-во{/t}</td>
                <td class="center">{t}Разница{/t}</td>
            {/if}
        </tr>
        {foreach $document.items as $uniq => $item}
            <tr>
                <td>{$item.title}</td>
                <td class="center">{$offer_titles[$item.offer_id].title}</td>
                {if $is_document || $is_movement}
                    <td class="center">{abs($item.amount)}</td>
                {elseif $is_inventorization}
                    <td class="center">{$item.calc_amount}</td>
                    <td class="center">{$item.fact_amount}</td>
                    <td class="center">{$item.dif_amount}</td>
                {/if}
            </tr>
        {/foreach}
    </table>
    <p>{t}Руководитель{/t} ____________________________</p>
    <p>{t}Ответственное лицо{/t} ____________________________</p>
</div>
</body>
</html>

