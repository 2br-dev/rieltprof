{addcss file="common/minipicker/jquery.minicolors.css" basepath="common"}
{addjs file="jquery.browser/jquery.browser.js" basepath="common"}
{addjs file="minipicker/jquery.minicolors.min.js" basepath="common"}

<input name="{$field->getFormName()}" value="{$field->get()}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} />