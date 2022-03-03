{addjs file="%main%/widgetengine.js"}
{addcss file="%main%/widgetstyle.css?v=2"}

<div class="viewport{if !$total} empty{/if}{if $total>1} cansort{/if}" id="widgets-block" data-widget-urls='{ "widgetList": "{adminUrl do="GetWidgetList"}", "addWidget":"{adminUrl do="ajaxAddWidget"}", "removeWidget":"{adminUrl do="ajaxRemoveWidget"}", "moveWidget": "{adminUrl do="ajaxMoveWidget"}" }'>
    <div id="noWidgetText">

        <p class="text">{t}Настройте<br><span class="small">свой рабочий стол</span>{/t}</p>
        <div class="welcome-disk">
            <a class="addwidget"><img src="{$mod_img}/nowidgets.png"></a>
        </div>

    </div>
    <div id="widget-zone">
        <div class="widget-column" data-column="1">
            {foreach $widgets as $widget}
                {moduleinsert name=$widget->getFullClass() widget=$widget}
            {/foreach}
        </div>
        <div class="widget-column" data-column="2"></div>
        <div class="widget-column" data-column="3"></div>
    </div>
    <a class="btn btn-default btn-lg btn-alt widget-change-position">
        <span class="change"><i class="zmdi zmdi-arrows"></i> {t}Изменить порядок виджетов{/t}</span>
        <span class="save"><i class="zmdi zmdi-save"></i> {t}Сохранить порядок виджетов{/t}</span>
    </a>
</div>