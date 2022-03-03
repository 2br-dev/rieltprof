{* Виджет: Динамика продаж *}
{addcss file="{$mod_css}sellchart.css?v=3" basepath="root"}
{addjs file="flot/jquery.flot.min.js" basepath="common"}
{addjs file="flot/jquery.flot.time.js" basepath="common" waitbefore=true}
{addjs file="flot/jquery.flot.resize.min.js" basepath="common"}
{addjs file="{$mod_js}jquery.sellchart.js?v=3" basepath="root"}

<div class="sell-widget" id="sellWidget">
    <div class="widget-filters">
        {* Фильтр по периоду *}
        <div class="dropdown">
            <a id="last-order-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $range=='year'}{t}по годам{/t}{else}{t}последний месяц{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="last-order-switcher">
                <li {if $range=='year'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="year" sellchart_orders="{$orders}" sellchart_show_type="{$show_type}"}" class="call-update">{t}По годам{/t}</a></li>
                <li {if $range=='month'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="month" sellchart_orders="{$orders}" sellchart_show_type="{$show_type}"}" class="call-update">{t}Последний месяц{/t}</a></li>
            </ul>
        </div>

        {if $range=='year'}
            {* Фильтр по годам *}
            <div class="dropdown">
                <a id="last-order-filter" data-toggle="dropdown" class="widget-dropdown-handle">{t}фильтр{/t} <i class="zmdi zmdi-chevron-down"></i></a>
                <ul class="dropdown-menu year-filter" aria-labelledby="last-order-filter">
                    {foreach $years as $year}
                    <li class="year-filter-item"><label><input type="checkbox" value="{$year}" checked> {t year=$year}%year г.{/t}</label></li>
                    {/foreach}
                </ul>
            </div>
        {/if}

        {* Фильтр по типу заказов *}
        <div class="dropdown">
            <a id="last-order-status" data-toggle="dropdown" class="widget-dropdown-handle">{if $orders=='success'}{t}завершенные заказы{/t}{else}{t}все заказы{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="last-order-status">
                <li {if $orders=='success'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="{$range}" sellchart_orders="success" sellchart_show_type="{$show_type}"}" class="call-update">{t}Завершенные заказы{/t}</a></li>
                <li {if $orders=='all'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="{$range}" sellchart_orders="all" sellchart_show_type="{$show_type}"}" class="call-update">{t}Все заказы{/t}</a></li>
            </ul>
        </div>

        {* Что отображать на графике? *}
        <div class="dropdown">
            <a id="last-order-type" data-toggle="dropdown" class="widget-dropdown-handle">{if $show_type == 'num'}{t}количество{/t}{else}{t}сумма{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="last-order-type">
                <li {if $show_type == 'num'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="{$range}" sellchart_orders="{$orders}" sellchart_show_type="num"}" class="call-update">{t}Количество{/t}</a></li>
                <li {if $show_type == 'summ'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="shop-widget-sellchart" sellchart_range="{$range}" sellchart_orders="{$orders}" sellchart_show_type="summ"}" class="call-update">{t}Сумма{/t}</a></li>
            </ul>
        </div>
    </div>

    {if $dynamics_arr}
        <div class="placeholder" style="height:300px;" data-inline-data='{$chart_data}'></div>
    {else}
        <div class="empty-widget">
            {t}Нет ни одного заказа{/t}
        </div>
    {/if}
</div>

<script type="text/javascript">
    $.allReady(function() {
        $('#sellWidget').rsSellChart();
    });
</script>