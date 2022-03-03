{if $elem->id}
    <b><a href="{$elem->getTypeObject()->getAdminUrl()}" target="_blank">{t}Ссылка на объект{/t}</a></b>
{else}
    {t}Адрес будет доступен после сохранения{/t}
{/if}