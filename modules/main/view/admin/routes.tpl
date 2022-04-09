<div class="columns">
    <div class="common-column">

        <div class="beforetable-line no-height routes-filter">
            <form method="GET" class="form-call-update form-inline">
                <div class="form-group">
                    <label class="line-label">{t}Хост{/t}</label>
                    <input type="text" name="host" value="{$host|escape}">
                    &nbsp;&nbsp;
                </div>
                <div class="form-group">
                    <label class="line-label">URI</label> <span class="help-icon" title="{t}Например: /products/ или /products/computers/{/t}">?</span>
                    <input type="text" name="uri" value="{$uri|escape}" style="width:300px;">
                </div>
                &nbsp;
                <button type="submit" class="btn btn-primary">
                    <i class="zmdi zmdi-check visible-sm"></i>
                    <span class="hidden-sm">{t}Проверить на соответствие{/t}</span>
                </button>
            </form>
        </div>

        {if $uri !== false}
            <div class="notice-box notice-warning p-15 m-t-5">
                {if $selected}{t route=$selected}Соответствует маршрут <strong>%route</strong>{/t}{else}{t}Ни один маршрут не соответствует заданном URI{/t}{/if}
            </div>
        {/if}

        {if isset($elements.table)}
            <div class="table-mobile-wrapper">
                <form method="POST" enctype="multipart/form-data" action="{urlmake}" class="crud-list-form">
                    {foreach from=$elements.hiddenFields key=key item=item}
                        <input type="hidden" name="{$key}" value="{$item}">
                    {/foreach}
                    {$elements.table->getView()}
                </form>
            </div>
        {/if}

        {if isset($elements.paginator)}
            {$elements.paginator->getView()}
        {/if}

    </div>
</div> <!-- .columns -->