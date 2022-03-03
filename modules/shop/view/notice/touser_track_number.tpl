{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$user=$data->order->getUser()}

    <h1>{t}Уважаемый покупатель!{/t}</h1>
    {t order_num=$data->order.order_num}Вашему заказу №%order_num присвоен трекномер:{/t}<br>

    <p><b>{$data->order.track_number}</b></p>

    {$check_url=$data->order->getTrackUrl()}
    {if !empty($check_url)}
        <p>{t}Ссылка для отслеживания{/t}: <a href="{$check_url}" target="_blank">{t}Перейти{/t}</a></p>
    {/if}

    <p>{t href=$router->getUrl('shop-front-myorders',[], true)}Ваш трекномер доступен у Вашего заказа в <a href="%href">«Личном кабинете»</a>.{/t}</p>
{/block}