{$list=$field->getList()}

<p id="sheeplaTemplateError" class="inlineError" {if !empty($list)}style="display:none"{/if}>
    {t}Для доступа к опциям укажите правильный ключ API администратора<br/>и создайте в панели sheepla шаблоны доставки.{/t}
</p>

<select id="sheeplaTemplates" name="{$field->getFormName()}" {$field->getAttr()} {if empty($list)}style="display:none"{/if}>
    {rshtml_options options=$list selected=$field->get()}
</select>
{include file="%system%/coreobject/type/form/block_error.tpl"}

