{addjs file="{$mod_js}jquery.csvexport.js" basepath="root"}
<div class="import-csv-params">
    <form class="crud-form" method="POST" action="{adminUrl do="processImport"}" data-dialog-options='{ "width":"800", "height":"600" }'>
        <input type="hidden" name="filename" value="{$filename}">
        <input type="hidden" name="schema" value="{$schema}">
        <input type="hidden" name="referer" value="{$referer}">
        <input type="hidden" name="import_start" value="1">
        <table class="import-table">
            <thead>
                <tr>
                    <td class="number">№</td>
                    <td class="title">{t}Колонка в CSV файле{/t}</td>
                    <td class="filed">{t}Поле в ReadyScript{/t}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$csv_columns.csv key=n item=item}
                <tr>
                    <td class="number">{$n+1}</td>
                    <td class="title">{$item}</td>
                    <td class="field">
                        <select name="columns[{$n}_{$item|escape}]" data-name="{$n}_{$item|escape}" class="destination">
                            <option value="">-- {t}не выбрано{/t} --</option>
                            {foreach from=$columns key=key item=column}
                            <option value="{$key}" {if $csv_columns.schema[$n]==$key}selected{/if}>{$column.title}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                {/foreach}
            </tbody>
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
        <a class="saveMap va-m-c" data-url="{adminUrl do="saveMap" type="import" schema=$schema}">
            <i class="zmdi zmdi-save f-20"></i>
            <span class="hidden-xs">{t}Сохранить шаблон{/t}</span>
        </a>
    </div>
</div>
<script>
    $.allReady(function() {
        $('.import-csv-params', this).csvImport();
    });
</script>