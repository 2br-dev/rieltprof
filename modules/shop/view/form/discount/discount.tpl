<input name="{$field->getFormName()}" value="{$field->get()|escape}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} type="text"/>
{include file=$elem.__discount_type->getRenderTemplate() field=$elem.__discount_type}
{include file="%system%/coreobject/type/form/block_error.tpl"}