{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url=$url->getDomainStr()}На сайте %url оплачен заказ{/t} №{$data->order.order_num}.
    <a href="{$router->getAdminUrl('edit', ["id" => $data->order.id], 'shop-orderctrl', true)}">{t}Перейти к заказу{/t}</a></p>
{/block}