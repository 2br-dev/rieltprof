{addjs file="jquery.rs.userdialog.js" basepath="common"}
<div class="form-inline orm-type-user-dialog">
    <input type="hidden" name="{$field->getFormName()}" value="{$field->get()}" class="user-id-field">
    <span class="user-name-field">{if $field->get()>0}<a href="{adminUrl do="edit" id=$field->get() mod_controller="users-ctrl"}" target="_blank">{$field->getUser()->getFio()}</a>{else}{t}Не выбрано{/t}{/if}</span>
    &nbsp;
    <a class="user-reset-select c-red{if !$field->get()} hidden{/if}" title="{t}отменить выбор{/t}">&#215;</a>
    &nbsp;
    <a data-url="{$field->getDialogUrl()}"
       data-crud-options='{ "updateThis": true }'
       class="btn btn-default btn-sm select-user" >{t}Выбрать{/t}</a>
</div>