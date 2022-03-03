{if $elem->id}
    <b><a href="{$elem->getTypeObject()->getExportUrl()}">{$elem->getTypeObject()->getExportUrl()}</a></b>
{else}
    {t}Адрес будет доступен после сохранения профиля{/t}
{/if}