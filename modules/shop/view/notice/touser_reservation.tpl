{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, клиент!{/t}</h1>
    <p>{t}Вы делали предзаказ на сайте{/t} <a href="http://{$url->getDomainStr()}">{$url->getDomainStr()}</a>, {t}уведомляем, что товар поступил на склад{/t}.</p>

    {if count($data->reservations) > 1}
        <h3>{t}Заказанные Вами товары{/t}</h3>
    {else}
        <h3>{t}Заказанный Вами товар{/t}</h3>
    {/if}

    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        <thead>
            <tr>
                <th>ID</th>
                <th>{t}Наименование{/t}</th>
                <th>{t}Комплектации{/t}</th>
                <th>{t}Код{/t}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $data->reservations as $reservation}
                {$product = $reservation->getProduct()}
                <tr>
                    <td>{$product.id}</td>
                    <td>{$product.title}</td>
                    <td>
                        {if $reservation.offer}
                            {$reservation.offer}
                        {elseif !empty($reservation.multioffer)}
                            {$multioffers = unserialize($reservation.multioffer)}
                            {foreach $multioffers as $offer}
                                {$offer}<br/>
                            {/foreach}
                        {/if}
                    </td>
                    <td>{$product.barcode}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/block}