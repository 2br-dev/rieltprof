{* Допустимо использовать Bootstrap стили *}

<h1>{t}Покупка в 1 клик{/t}</h1>
<p>{t}Имя заказчика{/t}: {$data->oneclick.user_fio}</p>
<p>{t}Телефон{/t}: {$data->oneclick.user_phone}</p>

<h2>{t}Заказаны товары{/t}:</h2>
<table class="table">
    <thead>
        <tr>
            <th>{t}ID{/t}</th>
            <th>{t}Наименование{/t}</th>
            <th>{t}Комплектация{/t}</th>
            <th>{t}Код{/t}</th>
            <th>{t}Кол-во{/t}</th>
        </tr>
    </thead>
    <tbody>
       {foreach $data->products_data as $product}
       {$offers_info=$product.offer_fields}
        <tr>
            <td><a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$product.id}</a></td>
            <td><a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$product.title}</a></td>
            <td>
                {if !empty($offers_info.offer)}
                    {$offers_info.offer}
                {elseif !empty($offers_info.multioffer)}
                    {implode(', ', $offers_info.multioffer)}
                {/if}
            </td>
            <td>{if !empty($offers_info.barcode)}{$offers_info.barcode}{else}{$product.barcode}{/if}</td>
            <td>{$offers_info.amount|default:1}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{if $data->ext_fields}
    <h2>{t}Дополнительные сведения{/t}</h2>
    <table class="table">
       <tbody>
          {foreach from=$data->ext_fields item=field}
              <tr>
                 <td><b>{$field.title}</b></td>
                 <td>{$field.current_val}</td>
              </tr>
          {/foreach}
       </tbody>
    </table>
{/if}