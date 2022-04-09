{if $THEME_SETTINGS.enable_compare}
    {addjs file="%catalog%/jquery.compare.js"}
    <a class="doCompare compareTopBlock{if $this_controller->api->getCount()} active{/if}">
        <span class="title">{t}Сравнение{/t}</span>
        <span class="compareItemsCount">{$this_controller->api->getCount()}</span>
    </a>
{/if}