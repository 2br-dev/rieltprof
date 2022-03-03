<a class="remove c-red m-r-10 f-18" title="{t}удалить{/t}">&times;</a>
{if $link->getLinkUrl()}
    <a href="{$link->getLinkUrl()}" target="_blank">{$link->getLinkText()}</a>
{else}
    {$link->getLinkText()}
{/if}
{if $link->isObjectOtherSite()}
    <span class="zmdi zmdi-alert-circle-o c-red" title="{t}Объект на другом мультисайте{/t}"></span>
{/if}