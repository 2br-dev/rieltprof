{addjs file="{$mod_js}jquery.csvexport.js" basepath="root"}
<div class="export-csv">
    <form class="crud-form" method="POST" action="{adminUrl schema=$schema}" data-dialog-options='{ "width":"1000", "height":"600" }'>
        <table class="preset-table">
            <tr>
                <td>
                    <label>{t}Возможные колонки для экспорта{/t}</label>
                    <select multiple class="source"></select>
                </td>
                <td class="middle">
                    <button type="button" class="btn btn-default add">
                        <span class="visible-xs">&darr;</span>
                        <span class="hidden-xs">&rarr;</span>
                    </button>
                    <button type="button" class="btn btn-default remove">
                        <span class="visible-xs">&uarr;</span>
                        <span class="hidden-xs">&larr;</span>
                    </button>
                </td>
                <td>
                    <label>{t}Выбранные колонки для экспорта{/t}</label>
                    <select name="columns[]" multiple class="destination selectAllBeforeSubmit">
                        {foreach from=$columns key=id item=column}
                        <option value="{$id}">{$column.title}</option>
                        {/foreach}
                    </select>
                </td>
                <td class="middle">
                    <input type="button" class="btn btn-default up" value="&uarr;">
                    <input type="button" class="btn btn-default down" value="&darr;">
                </td>
            </tr>
        </table>
    </form>
    <div class="presets">
        {t}Шаблон{/t}
        <select class="maps">
            <option value="0">-- {t}Не выбрано{/t} --</option>
            {foreach from=$maps item=item}
                {include file="csv/export_csv_option.tpl" map=$item}
            {/foreach}
        </select>
        &nbsp;&nbsp;
        <a class="removeMap va-m-c" data-url="{adminUrl do="deleteMap"}">
            <i class="zmdi zmdi-delete f-20 c-red"></i>
            <span class="hidden-xs">{t}Удалить{/t}</span>
        </a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a class="saveMap va-m-c" data-url="{adminUrl do="saveMap" type="export" schema=$schema}">
            <i class="zmdi zmdi-save f-20"></i>
            <span class="hidden-xs">{t}Сохранить шаблон{/t}</span>
        </a>
    </div>
</div>

<script>
    $.allReady(function() {
        $('.export-csv', this).csvExport();
    });
</script>