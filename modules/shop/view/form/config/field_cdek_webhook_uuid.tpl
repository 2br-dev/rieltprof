<div class="">
    {if $field->get()}
        <a href="{$router->getAdminUrl('cdekInfoWebHooks', [], 'shop-tools')}" class="crud-get">
            {t}Информация о подписке на веб-хуки{/t}
        </a>
        <br>
        <a href="{$router->getAdminUrl('cdekUnsubscribeWebHooks', [], 'shop-tools')}" class="crud-get">
            {t}Отписаться от веб-хуков{/t}
        </a>
    {else}
        <a href="{$router->getAdminUrl('cdekSubscribeWebHooks', [], 'shop-tools')}" class="crud-get">
            {t}Подписаться на веб-хуки{/t}
        </a>
    {/if}
    <div class="c-gray">
        Подписка на веб-хуки позволяет мгновенно обновлять заказ СДЭК на стороне ReadyScript в момент,
        когда этот заказ меняется на стороне СДЭК
    </div>
</div>
<div class="m-t-20">
    <a href="{$router->getAdminUrl('updateCdekRegions', [], 'shop-tools')}" class="crud-get">
        {t}Обновить базу регионов СДЭК{/t}
    </a>
    <div class="c-gray">
        Загружает в базу ReadyScript справочник регионов СДЭК. Этот справочник используется для
        получения СДЭК ID
    </div>
</div>
<div class="m-t-20">
    <div>
        <a href="{$router->getAdminUrl('RebaseCdekFile', [], 'shop-tools')}" class="crud-add crud-sm-dialog">
            {t}Актуализировать базы городов СДЭК (устаревшее){/t}
        </a>
        <div class="c-gray">
            {t}Позволяет обновить базу городов СДЭК{/t}
        </div>
    </div>
    <div class="m-t-10">
        <a href="{$router->getAdminUrl('CdekCityChecker', [], 'shop-tools')}" class="crud-add crud-sm-dialog">
            {t}Проверить соответствие городам СДЭК (устаревшее){/t}
        </a>
        <div class="c-gray">
            {t}Позволяет проверить, удается ли найти по вашему названию города и региона ID города в справочнике СДЭК{/t}
        </div>
    </div>
</div>



{*
[
'url' => RouterManager::obj()->getAdminUrl('updateCdekRegions', [], 'shop-tools'),
'title' => t('Обновить базу регионов СДЭК'),
'description' => t('Загружает актуальную базу регионов СДЭК'),
],
[
'url' => RouterManager::obj()->getAdminUrl('RebaseCdekFile', [], 'shop-tools'),
'title' => t('Актуализировать базы городов СДЭК (устаревшее)'),
'description' => t('Позволяет обновить базу городов СДЭК с сервером CDЭK'),
'class' => 'crud-add crud-sm-dialog',
],
[
'url' => RouterManager::obj()->getAdminUrl('CdekCityChecker', [], 'shop-tools'),
'title' => t('Проверить соответствие городам СДЭК (устаревшее)'),
'description' => t('Позволяет проверить, удается ли найти по вашему названию города и региона ID города в справочнике СДЭК'),
'class' => 'crud-add crud-sm-dialog',
]*}
