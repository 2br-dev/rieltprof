{$parts=$fcontrol->getParts()}
{if count($parts)}
    <div class="filter-parts">
        {if count($parts)>1}<span class="part clean_all"><a href="{$fcontrol->getCleanFilterUrl()}" class="clean call-update" title="{t}Сбросить все фильтры{/t}"><i class="zmdi zmdi-close{$fcontrol->getAddClass()}" {if $fcontrol->getUpdateContainer()}data-update-container="{$fcontrol->getUpdateContainer()}"{/if}></i></a></span>{/if}
        {foreach $parts as $part}
            <span class="part">
                <span class="text">{$part.title}: {$part.value}</span>
                <a href="{$part.href_clean}" {if $fcontrol->getUpdateContainer()}data-update-container="{$fcontrol->getUpdateContainer()}"{/if} class="clean call-update" title="{t}Сбросить этот фильтр{/t}"><i class="zmdi zmdi-close{$fcontrol->getAddClass()}" {if $fcontrol->getUpdateContainer()}data-update-container="{$fcontrol->getUpdateContainer()}"{/if}></i></a>
            </span>
        {/foreach}
    </div>
{/if}