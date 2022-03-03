{addjs file="%templates%/tplmanager.js" basepath="root"}
{addjs file="%templates%/selecttemplate.js" basepath="root"}

<div class="input-group">
    <input name="{$field->getFormName()}" value="{$field->get()}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} /><!--
    --><span class="input-group-addon"><a class="zmdi zmdi-collection-text selectTemplate" title="{t}Выбрать шаблон{/t}"></a></span>
</div>

{include file="%system%/coreobject/type/form/block_error.tpl"}

<script>
    $.allReady(function() {
        $('input[name="{$field->getFormName()}"]').selectTemplate({
            dialogUrl: '{adminUrl mod_controller="templates-selecttemplate" do=false only_themes=$field->getOnlyThemes()}',
            handler: '.selectTemplate'
        })
    });
</script>