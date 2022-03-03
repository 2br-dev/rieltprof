{assign var=max_site_count value=__GET_MAX_SITE_LIMIT()}
{if __GET_MAX_SITE_LIMIT() == 0}
    {$max_site_count=t('неограничено')}
{/if}
<div class="notice notice-warning m-b-10">{t max_site_count=$max_site_count}Максимальное количество сайтов: %max_site_count{/t}</div>