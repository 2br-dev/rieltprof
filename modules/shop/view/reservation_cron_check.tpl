{if !$is_cron_work}
<div style="margin-top:10px" class="notice-box no-padd">
    <div class="notice-bg">
        {t alias="Не запущен cron, в предзаказе"}Не запущен планировщик заданий cron. Рассылка уведомлений о поступлении товара для пользователей невозможна. 
        Подробнее о настройке планировщика заданий можно узнать в <a target="_blank" class="u-link" href="http://readyscript.ru/manual/cron.html">документации</a>.{/t}
    </div>
</div>
{/if}