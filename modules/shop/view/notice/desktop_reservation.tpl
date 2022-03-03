{* Допустимо использовать Bootstrap стили *}
{$product=$data->reserve->getProduct()}

<h1>{t order_num=$data->reserve.id}Предварительный заказ %order_num от{/t} {$data->reserve.dateof|date_format:"%d.%m.%Y %H:%M:%S"}</h1>

<h2>{t}Контакты заказчика{/t}</h2>
<p>{t}Телефон{/t}: {$data->reserve.phone}</p>
<p>E-mail: {$data->reserve.email}</p>

<h2>{t}Заказан товар{/t}</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>{t}Наименование{/t}</th>
            {if $data->reserve.offer || !empty($data->reserve.multioffer)}
               <th>{t}Комплектации{/t}</th> 
            {/if}
            <th>{t}Код{/t}</th>
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
        </tr>
    </tbody>
</table>