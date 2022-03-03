<p>{t}Уважаемый клиент!{/t}</p>
<p>{t order_num=$data->order.order_num date=$data->order.dateof|date_format:"%d.%m.%Y"}Скачать оплаченные файлы по заказу №%order_num от %date можно по следующим ссылкам:{/t}</p>

<table style="border-collapse:collapse;">
    <thead>
        <tr>
            <td style="border-bottom:1px solid #aaa;padding:15px;background-color:#F5F5F5;">{t}Файл{/t}</td>
            <td style="border-bottom:1px solid #aaa;padding:15px;background-color:#F5F5F5;">{t}Описание{/t}</td>
            <td style="border-bottom:1px solid #aaa;padding:15px;background-color:#F5F5F5;">{t}Ссылка{/t}</td>
        </tr>
    </thead>
    <tbody>
        {foreach $data->files as $file}
        <tr>
            <td style="border-bottom:1px solid #aaa;padding:15px;">{$file.name}</td>
            <td style="border-bottom:1px solid #aaa;padding:15px;">{$file.description|default:t("нет")}</td>
            <td style="border-bottom:1px solid #aaa;padding:15px;"><a href="{$file->getUrl(true)}">{t}скачать{/t}</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>

<p>{t}Ссылки на данные файлы также доступны в личном кабинете в разделе Мои заказы.{/t}</p>

<p>{t}С Наилучшими пожеланиями,{/t}<br>
        {t}Администрация интернет-магазина{/t} {$url->getDomainStr()}</p>