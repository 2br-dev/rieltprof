{literal}{$app->autoloadScripsAjaxBefore()}{/literal}
{$groups=$prop->getGroups(false, $switch)}
<div class="virtual-form" data-has-validation="true" data-action="{$router->getAdminUrl(false, ['ido' => $elem._ido], 'catalog-block-propertyvaluesblock')}">
    <div class="crud-form-error"></div>
    <input type="hidden" name="value_id" value="{literal}{$elem.id}{/literal}">
    <input type="hidden" name="prop_id" value="{literal}{$elem.prop_id}{/literal}">
    <input type="hidden" name="prop_type" value="{literal}{$elem.prop_type}{/literal}">
    <table class="table-inline-edit">
        {foreach from=$groups key=i item=data}
            {foreach from=$data.items key=name item=item}
                {if !$item->isHidden()}
                {literal}
                <tr>
                    <td class="key">{$elem.__{/literal}{$name}{literal}->getTitle()}&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if} </td>
                    <td>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}</td>
                </tr>{/literal}
                {/if}
            {/foreach}
        {/foreach}
            <tr>
                <td class="key"></td>
                <td><a class="btn btn-success virtual-submit">{t}Сохранить{/t}</a>
                <a class="btn btn-default cancel">{t}Отмена{/t}</a>
                </td>
            </tr>
    </table>
</div>
{literal}{$app->autoloadScripsAjaxAfter()}{/literal}