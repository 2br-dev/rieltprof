{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url=$url->getDomainStr()}На сайте %url оформлен предварительный заказ на товар.{/t}</p>

    <p>{t url=$router->getAdminUrl('edit', ["id" => $data->reserve.id], 'shop-reservationctrl', true) order_num=$data->reserve.id}Номер предварительного заказа: <a href="%url"><strong>%order_num</strong></a> от{/t} <strong>{$data->reserve.dateof|date_format:"%d.%m.%Y %H:%M:%S"}</strong></p>

    {assign var=product value=$data->reserve->getProduct()}
    <h3>{t}Контакты заказчика{/t}</h3>
    {if $data->reserve.user_id}
        {$user = $data->reserve->getUser()}
        {t}Ф.И.О.{/t}: {$user->getFio()}<br>
        {if $user.is_company}
            {t}Название организации{/t}: {$user.company}<br>
            {t}ИНН{/t}: {$user.company_inn}<br>
        {/if}
    {/if}
    {t}Телефон{/t}: {$data->reserve.phone}<br>
    E-mail: {$data->reserve.email}

    <h3>{t}Заказан товар{/t}</h3>
    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        <thead>
            <tr>
                <th>ID</th>
                <th>{t}Наименование{/t}</th>
                {if $data->reserve.offer}
                   <th>{t}Комплектации{/t}</th>
                {elseif !empty($data->reserve.multioffer)}
                   <th>{t}Комплектации{/t}</th>
                {/if}
                <th>{t}Код{/t}</th>
                <th>{t}Количество{/t}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$product.id}</a></td>
                <td><a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$product.title}</a></td>
                {if $data->reserve.offer}
                    <td><a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$data->reserve.offer}</a></td>
                {elseif !empty($data->reserve.multioffer)}
                    {assign var=multioffers value=unserialize($data->reserve.multioffer)}
                    <td>
                    {foreach $multioffers as $offer}
                        {$offer}<br/>
                    {/foreach}
                    </td>
                {/if}
                <td>{$product.barcode}</td>
                <td>{$data->reserve.amount}</td>
            </tr>
        </tbody>
    </table>
{/block}