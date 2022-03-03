{* Отображение списка значений в виде списка чекбоксов *}
{foreach from=$field->getList() item=item key=key}
    <label class="radio-item"><input name="{$field->getFormName()}" type="radio" value="{$key}" {if in_array($key, (array)$field->get())}checked="checked"{/if}> {$item}</label>
    {if !$field->isRadioListInline()}<br>{/if}
{/foreach}
{include file="%system%/coreobject/type/form/block_error.tpl"}
