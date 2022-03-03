<div class="property-filter {if $fitem->isActiveFilter()}property-filter-open"{/if}">

    <a class="property-filter-toggle"
       data-toggle-class="property-filter-open"
       data-target-closest=".property-filter"><i class="zmdi"></i> {t}искать по характеристикам{/t}</a>

    <div class="property-filter-forms">
        {$cat_properties = $fitem->getProperties()}
        {if $cat_properties}
            {foreach $cat_properties as $item}
                <div class="form-group">
                    <label>{$item.title}</label><br>
                    {include file="%catalog%/filter/view_filter.tpl" prop=$item}
                </div>
            {/foreach}
        {else}
            <div class="no-category-properties">{t}У выбранной категории нет характеристик{/t}</div>
        {/if}
    </div>
</div>