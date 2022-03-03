{$attr=$field->getAttrArray()}
<input name="{$field->getFormName()}" value="{$field->get()}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} {if !$attr.type}type="text"{/if}/>
{include file="%system%/coreobject/type/form/block_error.tpl"}