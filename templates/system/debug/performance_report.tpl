{addcss file="flatadmin/app.css" basepath="common"}
{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addcss file="flatadmin/debug.css" basepath="common"}

<div class="container performance-report">
    <div class="row">
        {if $report}
            {addjs file="jquery.min.js"}

            {addjs file="flot/excanvas.js" basepath="common" before="<!--[if lte IE 8]>" after="<![endif]-->"}
            {addjs file="flot/jquery.flot.min.js" basepath="common"}
            {addjs file="flot/jquery.flot.tooltip.min.js" basepath="common"}
            {addjs file="flot/jquery.flot.resize.js" basepath="common" waitbefore=true}
            {addjs file="flot/jquery.flot.pie.js" basepath="common" waitbefore=true}
            {addjs file="%main%/debug/performance.js"}

            <div class="wrapper">
                <div class="report-header">
                    <div class="numbers">
                        <h2>{t}Отчет о производительности{/t}</h2>
                        <p>{t url=$report.info.absolute_url}URL: %url{/t}</p>
                        <p>{t date="{$report.info.date|dateformat:"@date @time:@sec"}"}Дата формирования отчета: %date{/t}</p>
                        <p>{t time=round($report.info.total_time, 5)}Общее время (сек): %time{/t}</p>
                        <p>{t time=round($report.info.total_sql_time, 5)}Общее время SQL запросов (сек): %time{/t}</p>
                        <p>{t total=$report.info.total_sql_queries}Всего SQL запросов: %total{/t}</p>
                    </div>
                    <div class="chart hidden-xs">
                        <div id="performance-chart"></div>
                    </div>
                </div>
            </div>

            <h3 class="wrapper">{t}Детализация{/t}</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th><a class="sort {if $sort == 'default'}{$nsort}{/if}" href="{urlmake sort="default" nsort=$default_n_sort.default}" title="{t}Сортировать в порядке возникновения событий{/t}">{t}Тип события{/t} <i class="zmdi"></i></a></th>
                        <th><a class="sort {if $sort == 'time'}{$nsort}{/if}" href="{urlmake sort="time" nsort=$default_n_sort.time}" title="{t}Сортировать по времени выполнения{/t}">{t}Время выполнения, сек{/t} <i class="zmdi"></i></a></th>
                        <th><a class="sort {if $sort == 'sql_time'}{$nsort}{/if}" href="{urlmake sort="sql_time" nsort=$default_n_sort.sql_time}" title="{t}Сортировать по времени SQL запросов{/t}">{t}Время SQL запросов, сек{/t} <i class="zmdi"></i></a></th>
                    </tr>
                </thead>
                {include file="%system%/debug/performance_table_data.tpl"}
            </table>
        {else}
            <div class="text-center">
                <h1>{t}Отчет для данной страницы устарел{/t}</h1>
                <p>{t}Откройте заново профилируемую страницу и затем перейдите к отчету{/t}</p>
            </div>
        {/if}
        </div>
    </div>
</div>