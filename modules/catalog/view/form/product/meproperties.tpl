{addcss file="{$mod_css}selectproduct.css" basepath="root"}
{addcss file="{$mod_css}property.css" basepath="root"}
{addjs file="{$mod_js}meproperty.js" basepath="root"}

<table class="otable">
    <tr class="editrow">
        <td class="ochk" width="20">
            <input id="me-product-properties" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.___property_->getName()}" {if in_array($elem.___property_->getName(), $param.doedit)}checked{/if}></td>
        <td class="otitle"><label for="me-product-properties">{t}Изменить характеристики{/t}</label></td>
        <td>
            <div class="multi_edit_rightcol coveron">
                <div class="cover"></div>
                <div data-name="tab2" id="multipropertyblock" data-owner-type="product" 
                     data-get-property-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyList"}"
                     data-get-property-value-url="{adminUrl mod_controller="catalog-propctrl" do="ajaxGetPropertyValueList"}">
                    <a class="btn btn-default add-proprow select-button va-m-c">
                        <i class="zmdi zmdi-plus m-r-5"></i>
                        <span>{t}Добавить характеристику{/t}</span>
                    </a>
                    {include file="meproperty_form.tpl" value_types=$field->callPropertyFunction('getPropertyItemAllowTypeData')}
                </div>
            </div>
        </td>
    </tr>
</table>