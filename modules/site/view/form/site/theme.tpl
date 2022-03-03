{if empty($elem.id)}
    {addjs file="{$elem.tpl_module_folders.mod_js}selecttheme.js" basepath="root"}
    <div style="white-space:nowrap" id="theme_block">
        <input name="{$elem.__theme->getFormName()}" value="{$elem.__theme->get()|escape}" {if $elem.__theme->getMaxLength()>0}maxlength="{$elem.__theme->getMaxLength()}"{/if} {$elem.__theme->getAttr()} />&nbsp;<a id="selectTheme" class="button va-middle">{t}выбрать{/t}</a>
        {include file="%system%/coreobject/type/form/block_error.tpl" field=$elem.__theme}
    </div>
    <script>
    $.allReady(function() {
        $('#theme_block input[name="theme"]').selectTheme({
            dialogUrl: '{adminUrl mod_controller="templates-selecttheme" do=false}',
            setThemeUrl: '{adminUrl mod_controller="templates-selecttheme" do=installTheme}',
            justSelect: true
        })
    });
    </script>    
{else}
    {t}уже задана{/t}
{/if}