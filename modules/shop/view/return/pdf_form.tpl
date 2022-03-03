<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<style type="text/css">
    body {
        font-family:Arial;
        font-size:12px;
    }
    h3{
        text-align: center;
    }
    .toRight{
        text-align: right;
    }
    .main{
        margin-top: 30px;
    }
    .table {
        border-collapse: collapse;
        width:100%;
    }

    .table td {
        border:1px solid black;
        padding:3px;
    }
</style>
{$order=$return->getOrder()}
{$return_items=$return->getReturnItems()}

<div class="toRight">
    {t}Кому{/t}: {$site_config.firm_director}<br>
    {$site_config.firm_name}<br>

    {t}От покупателя{/t}: {$return->getFio()}
    <br>
    {t}Паспорт серия{/t}: {$return.passport_series}, {t}номер{/t}: {$return.passport_number}
    <br>
    {t}Кем и когда выдан{/t}: {$return.passport_issued_by}
    <br>
    {t}Телефон{/t}: {$return.phone}
    <br>
</div>
<div class="main">
    <h3>{t}Заявление{/t}</h3>
    <p>{t alias="Возврат - прошу вернуть первая строка"
            fio=$return->getFio()
            order_num=$order.order_num date={$order.dateof|date_format:"d.m.Y"}
            order_total={$order.totalcost|format_price}
            return_cost_total={$return.cost_total|format_price}
            return_text_totalcost=$return_text_totalcost
            currency=$return.currency_stitle}
        Я, %fio, прошу оформить возврат на товары из заказа № %order_num от %date,
        вернуть уплаченные за товары денежные средства в размере %return_cost_total %currency (%return_text_totalcost){/t}.
    </p>
    <br/>
    <p><b>{t}Список товаров на возврат{/t}:</b></p>
    <p>
        <table class="table">
            <thead>
                <tr>
                    <td>{t}Наименование товара{/t}</td>
                    <td>{t}Количество{/t}</td>
                </tr>
            </thead>
            <tbody>
                {foreach $return_items as $return_item}
                    <tr>
                        <td>{$return_item.title}</td>
                        <td>{$return_item.amount} {t}шт.{/t}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </p>
    <br/>
    <p>{t}Причина возврата{/t}: {$return.return_reason}</p>
    <br/>
    <p><b>{t}Реквизиты для возврата{/t}:</b></p>
    <p>{t}Наименование банка{/t}: {$return.bank_name}</p>
    <p>{t}БИК{/t}: {$return.bik}</p>
    <p>{t}Рассчетный счет{/t}: {$return.bank_account}</p>
    <p>{t}Корреспондентский счет{/t}: {$return.correspondent_account}</p>
</div>
<br/>
<div>
    <p>{t}Дата{/t}: {$return.dateof|date_format:"d.m.Y"}</p>
    <p>{t}Подпись{/t} _______________</p>
</div>
</body>
</html>