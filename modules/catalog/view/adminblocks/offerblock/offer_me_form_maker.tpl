{$groups=$prop->getGroups(false, $switch)}
<div class="virtual-form multiedit" data-has-validation="true" data-action="{$router->getAdminUrl(false, ['odo' => 'offerMultiEdit'], 'catalog-block-offerblock')}">
    {literal}
    <p>
        {t}Выбрано элементов:{/t} <strong>{$param.sel_count}</strong>
    </p>
    {foreach $param.hidden_fields as $key=>$val}
    <input type="hidden" name="{$key}" value="{$val}">
    {/foreach}
    {/literal}
    <div class="crud-form-error"></div>    
    <input type="hidden" name="offer_id" value="{literal}{$elem.id}{/literal}">
    <input type="hidden" name="product_id" value="{literal}{$elem.product_id}{/literal}">
    <table class="table-inline-edit">
        {foreach from=$groups key=i item=data}
            {foreach from=$data.items key=name item=item}
                {literal}
                <tr class="editrow">
                    <td class="ochk" width="20">
                        <input title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__{/literal}{$name}{literal}->getName()}" {if in_array($elem.__{/literal}{$name}{literal}->getName(), $param.doedit)}checked{/if}></td>
                    <td class="key">{$elem.__{/literal}{$name}{literal}->getTitle()}</td>
                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate(true) field=$elem.__{/literal}{$name}{literal}}</div></td>
                </tr>{/literal}
            {/foreach}
        {/foreach}
            <tr>
                <td></td>
                <td class="key"></td>
                <td><a class="btn btn-success virtual-submit">{t}Сохранить{/t}</a>
                    <a class="btn btn-default cancel">{t}Отмена{/t}</a>
                </td>
            </tr>
    </table>
</div>