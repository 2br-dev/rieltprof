{$progress_width = 150}
{foreach $report.data as $block}
    <tbody {if $always_show_sql}class="show-sql"{/if}>
    <tr>
        <td>
            {if $block.sql_queries}
                <a class="toggle-sql zmdi zmdi-plus-square f-15 m-r-5"></a>
            {else}
                <span class="zmdi zmdi-square-o f-15 m-r-5"></span>
            {/if}
            <span>{$block.title} {$block.subtitle}</span></td>
        <td class="text-nowrap">
            <span class="percent">{number_format($block.duration_sec, 5)}</span>
            <span class="percent-bar" style="width: {round($block.duration_sec / $report.info.total_time * $progress_width)}px"></span>
        </td>
        <td class="text-nowrap">
            <span class="percent">{number_format($block.duration_sql_sec, 5)}</span>
            <span class="percent-sql-bar" style="width: {round($block.duration_sql_sec / $report.info.total_sql_time * $progress_width)}px"></span>
        </td>
    </tr>
    {foreach $block.sql_queries as $query}
        <tr class="sql">
            <td>
                <div class="child">
                    {if $query.stack_trace}
                        <a class="toggle-stack-trace zmdi zmdi-plus-square f-15 m-r-5"></a>
                    {else}
                        <span class="zmdi zmdi-square-o f-15 m-r-5"></span>
                    {/if}
                    <span class="sql-query">{$query.query}</span>
                    {if $query.stack_trace}
                        <div class="stack-trace">
                            <p class="caption">{t}Стек вызовов функций (файл, строка):{/t} </p>
                            {foreach $query.stack_trace as $line}
                                {$line}<br>
                            {/foreach}
                        </div>
                    {/if}
                </div>
            </td>
            <td></td>
            <td class="text-nowrap">
                <span class="percent">{number_format($query.duration_sec, 5)}</span>
                <span class="percent-query-bar" style="width: {round($query.duration_sec / $block.duration_sql_sec * $progress_width)}px"></span>
            </td>
        </tr>
    {/foreach}
    </tbody>
{/foreach}