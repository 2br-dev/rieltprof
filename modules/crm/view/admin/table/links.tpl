{$row=$cell->getRow()}
{$links = $row->getProp($cell->getField())->getLinkedObjects($row)}
{if $links}
    {foreach $links as $link}
        <p>
            {if $link->getLinkUrl()}
                <a href="{$link->getLinkUrl()}" target="_blank">{$link->getLinkText()}</a>
            {else}
                {$link->getLinkText()}
            {/if}

            {if $link->isObjectOtherSite()}
                <span class="zmdi zmdi-alert-circle-o c-red" title="{t}Объект на другом мультисайте{/t}"></span>
            {/if}
        </p>
    {/foreach}
{else}
    {t}Нет связей{/t}
{/if}