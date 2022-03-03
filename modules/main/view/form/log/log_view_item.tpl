{addcss file='%main%/logview.css'}
{addcss file="flatadmin/app.css" basepath="common"}
{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addjs file='%main%/rs.logview.js'}
{addjs file="bootstrap/bootstrap.min.js" basepath="common"}

{foreach $app->getCss() as $css}
    {$css.params.before}<link {if $css.params.type !== false}type="{$css.params.type|default:"text/css"}"{/if} href="{$css.file}" {if $css.params.media!==false}media="{$css.params.media|default:"all"}"{/if} rel="{$css.params.rel|default:"stylesheet"}"{if $css.params.as} as="{$css.params.as}"{/if}{if $css.params.crossorigin} crossorigin="{$css.params.crossorigin}"{/if}>{$css.params.after}
{/foreach}
<script>
    var global = {$app->getJsonJsVars()};
</script>
{foreach $app->getJs() as $js}
    {$js.params.before}<script type="{$js.params.type|default:"text/javascript"}" src="{$js.file}"{if $js.params.async} async{/if}{if $js.params.defer} defer{/if}></script>{$js.params.after}
{/foreach}

{$level_list = $log->getLogLevelList()}

<div class="log-view admin-style rs-log-view" data-url-clear-log="{adminurl do='clearLog' log_class=$log->getIdentifier() site_id=$log->getSiteId()}">
    <div class="log-view-head">
        <h1>{t}Лог-файл{/t} "{$log->getTitle()}"</h1>
    </div>
    <form method="get" href="{$url->selfUri()}" class="log-view-filter">
        <input type="hidden" name="do" value="view">
        <input type="hidden" name="log_class" value="{$log_class}">
        <input type="hidden" name="site_id" value="{$site_id}">
        <input type="hidden" name="page" value="1">
        <input type="hidden" name="page_size" value="{$page_size}">
        <div>
            <div class="log-view-filter-text">
                <div class="log-view-input-hint">{t}Поиск по тексту{/t}</div>
                <input type="text" name="text" value="{$text}">
            </div>
            <div class="log-view-filter-date">
                <div class="rs-log-view-datetime">
                    <div>
                        <div class="log-view-input-hint">{t}Дата от{/t}</div>
                        <input type="date" name="date_from" value="{$date_from}" class="rs-log-view-input-date">
                    </div>
                    <div>
                        <div class="log-view-input-hint">{t}Время от{/t}</div>
                        <input type="time" name="time_from" value="{$time_from}" class="rs-log-view-input-time">
                    </div>
                </div>
                <div class="rs-log-view-datetime">
                    <div>
                        <div class="log-view-input-hint">{t}Дата до{/t}</div>
                        <input type="date" name="date_to" value="{$date_to}" class="rs-log-view-input-date">
                    </div>
                    <div>
                        <div class="log-view-input-hint">{t}Время до{/t}</div>
                        <input type="time" name="time_to" value="{$time_to}" class="rs-log-view-input-time">
                    </div>
                </div>
            </div>
        </div>
        <div class="log-view-filter-levels">
            <div class="log-view-input-hint">{t}Уровни логирования{/t}</div>
            {foreach $level_list as $level => $title}
                <label>
                    <input type="checkbox" name="levels[]" value="{$level}" {if empty($levels) || in_array($level, $levels)}checked{/if}>
                    <span>{$title}</span>
                </label>
            {/foreach}
        </div>
        <div class="log-view-filter-buttons">
            <input type="submit" value="{t}Применить{/t}" class="btn btn-primary">

            {if $date_from || $time_from || $date_to || $time_to || $levels || $text}
                <a href="{adminurl log_class=$log_class site_id=$site_id}" class="btn btn-primary btn-alt">
                    {t}Сбросить<span class="hidden-mobile"> фильтр</span>{/t}
                </a>
            {/if}

            <a type="submit" class="btn btn-danger btn-alt rs-log-view-clear-log">
                {t}Очистить<span class="hidden-mobile"> лог</span>{/t}
            </a>
        </div>
    </form>
    <div class="log-view-list">
        <table>
            {$empty_list = true}
            {foreach $reader->readRecord() as $record}
                {$empty_list = false}
                <tr class="log-view-line">
                    <td class="log-view-line-date">{$record.date} {$record.time}</td>
                    <td class="log-view-line-level">
                        {if isset($level_list[$record.level])}
                            {$level_list[$record.level]}
                        {else}
                            {$record.level}
                        {/if}
                    </td>
                    <td class="log-view-line-text">
                        <pre>{$record.text}</pre>
                    </td>
                </tr>
            {/foreach}
            {if $empty_list}
                <tr class="log-view-line log-view-empty">
                    <td>
                        {if $reader->getRecordsCount() == 0}
                            {t}Лог-файл пуст{/t}
                        {else}
                            {t}Записей не найдено{/t}
                        {/if}
                    </td>
                </tr>
            {/if}
        </table>
    </div>
    <div class="log-view-paginator-top">
        {$controller->getItemViewPaginatorHtml($page, $page_size, $reader->getFilteredRecordsCount())}
    </div>
    <div class="log-view-paginator">
        {$controller->getItemViewPaginatorHtml($page, $page_size, $reader->getFilteredRecordsCount())}
    </div>
</div>