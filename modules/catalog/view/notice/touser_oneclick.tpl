{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t d=$url->getDomainStr()}Вы сделали заказ в 1 клик в интернет-магазине %d.{/t}</h1>
    <p>{t}Скоро с Вами свяжется наш менеджер.{/t}</p>

    <h3>{t}Заказаны товары{/t}:</h3>
    <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
        <thead>
        <tr>
            <th>ID</th>
            <th>{t}Наименование{/t}</th>
            <th>{t}Комплектация{/t}</th>
            <th>{t}Код{/t}</th>
            <th>{t}Кол-во{/t}</th>
            <th>{t}Стоимость{/t}</th>
        </tr>
        </thead>
        <tbody>
        {$sum = 0}
        {foreach $data->oneclick.products as $key => $product}
            {$offers_info=$product.offer_fields}
            {$amount = $offers_info.amount}
            <tr>
                <td><a href="{$product->getUrl(true)}">{$product.id}</a></td>
                <td><a href="{$product->getUrl(true)}">{$product.title}</a></td>
                <td>
                    {if !empty($offers_info.offer)}
                        <h3>{t}Сведения о комплектации:{/t}</h3>
                        <a href="{$router->getAdminUrl('edit',["id" => $product.id], 'catalog-ctrl', true)}">{$offers_info.offer}</a>
                    {elseif !empty($offers_info.multioffer)}
                        <h3>{t}Сведения о многомерной комплектации{/t}</h3>
                        {foreach $offers_info.multioffer as $offer}
                            {$offer}<br/>
                        {/foreach}
                    {/if}
                </td>
                <td>{if !empty($offers_info.barcode)}{$offers_info.barcode}{else}{$product.barcode}{/if}</td>
                <td>{$amount|default:1}</td>
                {$cost = $product->getCost(null, $offers_info.offer_id)}
                <td>{$cost} {$product->getCurrency()}</td>
                {$sum = $sum + $product->getCost(null, $offers_info.offer_id, false) * $amount}
            </tr>
        {/foreach}
        <tr>
            <th colspan="5">Итого</th>
            <th>{$sum|format_price} {$data->oneclick.products.0->getCurrency()}</th>
        </tr>
        </tbody>
    </table>

    {if $data->ext_fields}
        <h3>{t}Дополнительные сведения{/t}</h3>
        <table cellpadding="5" border="1" bordercolor="#969696" style="border-collapse:collapse; border:1px solid #969696">
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
{/block}