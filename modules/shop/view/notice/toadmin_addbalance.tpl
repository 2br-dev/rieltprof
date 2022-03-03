{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url=$url->getDomainStr()}На сайте %url пополнен баланс пользователем.{/t}</p>

    <p><strong>{t}Сведения о клиенте{/t}:</strong></p>

    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        {if $data->transaction.order_id}
            <tr>
                <td>
                    {t}Номер заказа{/t}
                </td>
                <td>
                    <b>{$data->transaction->getOrder()->order_num}</b>
                </td>
            </tr>
        {/if}
        <tr>
            <td>
               {t}id транзакции{/t}
            </td>
            <td>
               <b>{$data->transaction.id}</b>
            </td>
        </tr>
        <tr>
            <td>
               {t}Имя{/t}
            </td>
            <td>
               <b>{$data->user.name}</b>
            </td>
        </tr>
        <tr>
            <td>
               {t}Фамилия{/t}
            </td>
            <td>
               <b>{$data->user.surname}</b>
            </td>
        </tr>
        <tr>
            <td>
               {t}Сумма пополнения баланса{/t}
            </td>
            <td>
               <b>{$data->transaction.cost}</b>
            </td>
        </tr>
    </table>

    <p><a href="{$router->getAdminUrl(null, null,'shop-transactionctrl', true)}">{t}Перейти к просмотру{/t}</a></p>
    {if $data->transaction.order_id}
        <p><a href="{$router->getAdminUrl('edit', ["id" => $data->transaction->getOrder()->id], 'shop-orderctrl', true)}">{t}Перейти к заказу{/t}</a></p>
    {/if}
{/block}