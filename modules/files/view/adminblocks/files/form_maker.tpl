{$groups=$prop->getGroups(false, $switch)}
<div class="virtual-form" data-has-validation="true" data-action="{adminUrl files_do="Edit" mod_controller="files-block-files"}">
    <div class="crud-form-error"></div>
    <input type="hidden" name="file" value="{literal}{$elem.id}{/literal}">
    <input type="hidden" name="link_type" value="{literal}{$elem->getLinkType()->getShortName()}{/literal}">
    <input type="hidden" name="link_id" value="{literal}{$elem.link_id}{/literal}">
    <table class="table-inline-edit">
        {foreach $groups as $i => $data}
            {foreach $data.items as $name => $item}
                {if !$item->isHidden()}
                {literal}
                <tr>
                    <td class="key">{$elem.__{/literal}{$name}{literal}->getTitle()}</td>
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