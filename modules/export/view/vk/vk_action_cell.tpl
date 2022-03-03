{if !$export_type->getToken()}
    <a href="{$export_type->getOauthUrl($export_profile['id'])}" class="btn btn-default " target="_blank">{t}Получить AccessToken для API{/t}</a>
{else}
    {if $export_type->isRunning($export_profile)}
        <a href="{$router->getAdminUrl('AjaxStopExchange', ['profile_id' => $export_profile['id']], 'export-ctrl')}" class="btn btn-danger crud-get">{t}Остановить обмен данными{/t}</a>
    {else}
        <a href="{$router->getAdminUrl('AjaxDoExchange', ['profile_id' => $export_profile['id']], 'export-ctrl')}" class="btn btn-warning crud-get">{t}Провести обмен данными{/t}</a>
    {/if}

    <a href="{$export_type->getOauthUrl($export_profile['id'])}" class="btn btn-default " target="_blank">{t}Получить новый API-token{/t}</a>
{/if}

<a href="{$router->getAdminUrl('ShowLog', ['profile_id' => $export_profile['id']], 'export-ctrl')}" class="btn btn-default crud-edit" data-crud-dialog-width="1000" target="_blank">{t}Просмотреть лог{/t}</a>