{addjs file="{$elem.tpl_module_folders.mod_js}selecttheme.js" basepath="root"}
{include file=$elem.__theme->getOriginalTemplate() field=$elem.__theme}
<a id="selectTheme" class="btn btn-default va-middle">{t}выбрать{/t}</a>

<script>
$.allReady(function() {
    $('input[name="theme"]').selectTheme({
        dialogUrl: '{adminUrl mod_controller="templates-selecttheme" do=false}',
        setThemeUrl: '{adminUrl mod_controller="templates-selecttheme" do=installTheme}'
    })
});
</script>