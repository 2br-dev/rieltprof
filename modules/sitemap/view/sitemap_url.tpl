<div class="notice-box notice-bg">
    {t}Файл sitemap для текущего сайта находится по адресу:{/t}<br>

    {$url=$router->getUrl('sitemap-front-sitemap', [site_id => $SITE.id], true)}
    XML: <a href="{$url}" target="_blank"><strong>{$url}</strong></a><br>
    GZIP: <a href="{$url}.gz" target="_blank"><strong>{$url}.gz</strong></a><br>
    <br>
    {$google_url=$router->getUrl('sitemap-front-sitemap', [site_id => $SITE.id, type => 'google'], true)}
    {t}Файл sitemap с дополнительными элементами для google находится по адресу:{/t}<br>
    XML: <a href="{$google_url}" target="_blank"><strong>{$google_url}</strong></a><br>
    GZIP: <a href="{$google_url}.gz" target="_blank"><strong>{$google_url}.gz</strong></a>
</div>