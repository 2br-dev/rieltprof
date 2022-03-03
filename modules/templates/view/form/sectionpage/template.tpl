{addjs file="{$elem.tpl_module_folders.mod_js}tplmanager.js" basepath="root"}
{addjs file="{$elem.tpl_module_folders.mod_js}selecttemplate.js" basepath="root"}
{include file=$elem.__template->getOriginalTemplate() field=$elem.__template}
<a id="selectTemplate" class="selectTemplate" title="{t}Выберите шаблон из списка{/t}"></a>
<span class="help-icon" title="{t}Указанный шаблон будет использован вместо блоков. Возможность разметить страницу блоками в этом случае будет отключена. Указывайте произвольный шаблон в случае, если макет невозможно сверстать с помощью gs960.css или bootstrap{/t}">?</span>
<script>
$.allReady(function() {
    $('input[name="template"]').selectTemplate({
        dialogUrl: '{adminUrl mod_controller="templates-selecttemplate" do=false}'
    })
});
</script>