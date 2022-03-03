<div class="property-filter {if $fitem->isActiveFilter()}property-filter-open"{/if}">
        <a class="property-filter-toggle"
           data-toggle-class="property-filter-open"
           data-target-closest=".property-filter"><i class="zmdi"></i> {t}искать по связям{/t}</a>

        <div class="property-filter-forms">
                {foreach $fitem->getAllowLinkTypesObject() as $link_type}
                        <div class="form-group">
                                {$link_type->getTabName()}<br>
                                {$fitem->getLinkTypeForm($link_type)}
                        </div>
                {/foreach}
        </div>
</div>