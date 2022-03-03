{* Отображение списка значений в виде списка чекбоксов *}
{foreach from=$field->getList() item=item key=key}
    <div>
        <label><input name="{$field->getFormName()}" type="checkbox" value="{$key}" {if in_array($key, (array)$field->get())}checked="checked"{/if}> {$item}</label>
    </div>
{/foreach}
{include file="%system%/coreobject/type/form/block_error.tpl"}
