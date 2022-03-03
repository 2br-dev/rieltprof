{if $cell->getRow('installed')}
    {if $cell->getValue()}Да{else}Нет{/if}
{else}
    <a class="not_installed crud-get" href="{adminUrl do=ajaxreinstall module=$cell->getRow('class')}" title="Нажмите, чтобы установить модуль" style="white-space:nowrap">Не установлен</a>
{/if}