{addcss file="%catalog%/property.css?v=2"}
{addjs file="%catalog%/property.js?v=2"}

<div data-name="tab2" id="propertyblock" data-owner-type="product" 
    data-get-property-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyList"}" 
    data-save-property-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxCreateOrUpdateProperty"}"
    data-get-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyValueList"}"
    data-create-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxAddPropertyValue"}"
    data-remove-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxRemovePropertyValue"}"
    >
    <div class="property-tools">
        <div class="property-actions">
            <a class="add-property underline va-m-c"><i class="zmdi zmdi-plus m-r-5"></i> <span>{t}Добавить характеристику{/t}</span></a>
            <span class="success-text">{t}Характеристика успешно добавлена{/t}</span>
        </div>
        {include file="property_form.tpl" value_types=$field->callPropertyFunction('getPropertyItemAllowTypeData')}
    </div>
    <div class="floatwrap">
        <a class="set-self-val underline va-m-c">
            <i class="zmdi zmdi-check-all m-r-5 f-17"></i>
            <span>{t}Задать индивидуальные значения всем характеристикам{/t}</span>
        </a>
    </div>
    
    <table class="property-container has-tools">
        {foreach $elem->getPropObjects() as $key => $group}
            {include file="property_group_product.tpl" group=$group}
        {/foreach}
    </table>
</div>