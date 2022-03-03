{addcss file="flatadmin/app.css" basepath="common"}
{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addcss file="flatadmin/debug.css" basepath="common"}
{addjs file="jquery.min.js"}
{addjs file="bootstrap/bootstrap.min.js" name="bootstrap" basepath="common"}
{addjs file="%main%/debug/performance.js"}

<div class="admin-style">

    <div class="text-center m-b-30">
        <h2 class="module-debug-title">{t title="{$controller_data.title|default:$controller_data.class}"}Информация по контроллеру `%title`{/t}</h2>
        {if $controller_data.title}<p>{$controller_data.class}</p>{/if}
    </div>

    <div role="tabpanel">
        <ul class="tab-nav" role="tablist">
            <li class="active"><a href="#vars" aria-controls="vars" role="tab" data-toggle="tab" aria-expanded="true">{t}Переменные{/t}</a></li>
            {if $timing_is_enable}
            <li><a href="#performance" aria-controls="performance" role="tab" data-toggle="tab" aria-expanded="false">{t}Производительность{/t}</a></li>
            {/if}
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel" id="vars">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{t}Имя переменной{/t}</th>
                        <th>{t}Тип{/t}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $var_list as $item}
                        <tr>
                            <td class="var-name">{$item.key}</td>
                            <td class="var-type">{$item.type}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            {if $timing_is_enable}
                <div class="tab-pane performance-report" role="tabpanel" id="performance">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{t}Тип события{/t}</th>
                            <th><span class="sort desc">{t}Время выполнения, сек{/t} <i class="zmdi"></i></span></th>
                            <th>{t}Время SQL запросов, сек{/t}</th>
                        </tr>
                        </thead>
                        {include file="%system%/debug/performance_table_data.tpl" always_show_sql=true}
                    </table>
                </div>
            {/if}
        </div>
    </div>
</div>