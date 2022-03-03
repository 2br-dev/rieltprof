{addjs file="jquery.rs.filter.js"}

<div class="filter">

    <a class="openfilter">
        <i class="zmdi zmdi-search"></i>
        <span class="filter-title hidden-xs">{$fcontrol->getCaption()}</span>
        <span class="visible-xs-inline-block">{t}Поиск{/t}</span>
    </a>

    <form id="{$fcontrol->uniq}" method="GET" class="filter-form form-call-update{$fcontrol->getAddClass()}" {if $fcontrol->getUpdateContainer()}data-update-container="{$fcontrol->getUpdateContainer()}"{/if} data-clean-url="{$fcontrol->getCleanFilterUrl()}">
        {foreach $fcontrol->getAddParam('hiddenfields') as $key=>$val}
            <input type="hidden" name="{$key}" value="{$val}">
        {/foreach}

        {$fcontrol->getContainerView()}
    </form>

</div>