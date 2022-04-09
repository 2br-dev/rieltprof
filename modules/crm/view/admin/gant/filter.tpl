{addcss file="%crm%/gant.css"}
{addjs file="jquery.rs.ormobject.js" basepath="common"}
{addjs file="%crm%/gant.js"}
<form action="{adminUrl}" class="viewport form-call-update stat-date-range" method="GET">
    <input type="hidden" name="preset" value="{$current_preset}">
    <input type="hidden" name="view_type" value="{$current_view_type}">

    <div class="g-top-line">
        <div class="gant-filter-line">
            <div class="dropdown m-r-5">
                <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$current_date_range.label} <i class="zmdi zmdi-chevron-down"></i></a>
                <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
                    {foreach $date_presets as $key => $preset}
                        <li{if $date_from == $key} class="act"{/if}>
                            <a href="{urlmake date_from=$key date_to=null}" class="call-update">{$preset.label}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>

            <div class="dropdown">
                <a id="dateSelector-{$controller_id}" data-toggle="dropdown" class="widget-dropdown-handle">{$current_date_range.from|dateformat:"@date"} &ndash; {$current_date_range.to|dateformat:"@date"} <i class="zmdi zmdi-chevron-down"><!----></i></a>
                <div class="dropdown-menu p-20" aria-labelledby="dateSelector-{$controller_id}">
                    <p>{t}Начало диапазона{/t}</p>
                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"><!----></i></span>
                        <div class="dtp-container">
                            <input class="form-control date-time-picker from" type="text" name="date_from" value="{$current_date_range.from}" datefilter style="z-index:5">
                        </div>
                    </div>

                    <p>{t}Конец диапазона{/t}</p>
                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"><!----></i></span>
                        <div class="dtp-container">
                            <input class="form-control date-time-picker to" type="text" name="date_to" value="{$current_date_range.to}" datefilter style="z-index:5">
                        </div>
                    </div>

                    <input type="submit" value="Применить" class="btn btn-primary">
                </div>
            </div>

            <div class="dropdown">
                <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$presets[$current_preset]} <i class="zmdi zmdi-chevron-down"></i></a>
                <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
                    {foreach $presets as $id => $title}
                        <li{if $current_preset == $id} class="act"{/if}>
                            <a href="{urlmake preset=$id}" class="call-update">{$title}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>

            <div class="dropdown">
                <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$view_types[$current_view_type]} <i class="zmdi zmdi-chevron-down"></i></a>
                <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
                    {foreach $view_types as $id => $title}
                        <li{if $current_view_type == $id} class="act"{/if}>
                            <a href="{urlmake view_type=$id}" class="call-update">{$title}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</form>
<style>
    :root {
        --gant-min-cell-width:40px;
        --gant-days: {count($chart_data.days)};
    }
</style>