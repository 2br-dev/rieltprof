{addcss file="%catalog%/property.css?v=2"}
{addjs file="%catalog%/property.js?v=2"}

<div data-name="tab2" id="propertyblock" data-owner-type="group"
    data-get-property-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyList"}"
    data-save-property-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxCreateOrUpdateProperty"}"
    data-get-some-properties="{adminUrl mod_controller="catalog-propctrl" do="AjaxGetSomeProperties"}"
    data-get-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyValueList"}"
    data-create-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxAddPropertyValue"}"
    data-remove-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxRemovePropertyValue"}">

    <div class="property-tools">
        <div class="property-actions">
            <a class="add-property underline va-m-c"><i class="zmdi zmdi-plus m-r-5"></i> <span>{t}Добавить характеристику{/t}</span></a>
            <a class="add-some-property underline text-nowrap"><i class="zmdi zmdi-plus-circle-o-duplicate m-r-5"></i> <span>{t}Вставить несколько характеристик{/t}</span></a>
            <span class="success-text">{t}Характеристика успешно добавлена{/t}</span>
        </div>
        {include file="property_form.tpl" value_types=$field->callPropertyFunction('getPropertyItemAllowTypeData')}
    </div>

    <table class="property-container">
        <tbody class="overable">
        {foreach from=$elem->getPropObjects() item=group key=key}
            {include file="property_group_product.tpl" group=$group owner_type="group"}
        {/foreach}
        </tbody>
    </table>
</div>