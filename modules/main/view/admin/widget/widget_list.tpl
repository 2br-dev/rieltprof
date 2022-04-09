<div class="widget-collection-block{if !$list} empty{/if}">

    <div class="widget-collection-zone">
        <p>{t}Нажмите на виджет, чтобы добавить его на рабочий стол.{/t}
           <span class="hidden-xs">{t}Или перетащите необходимый виджет за значок <i class="zmdi zmdi-apps"></i> на рабочую область административной панели{/t}</span>
        </p>
        <div class="widget-collection" data-column="source">
        {foreach $list as $wid => $item}
                <div class="item" data-wclass="{$item.short_class}">
                    <div class="item-head">
                        <a class="add"><i class="zmdi zmdi-plus"></i></a>
                        <span class="move-handle"><i class="zmdi zmdi-apps"></i></span>
                        <div class="title">{$item.title}</div>
                    </div>
                    <div class="item-description">{$item.description}</div>
                    <div class="item-module">{t module=$item.module}Модуль: %module{/t}</div>
                </div>
        {/foreach}
        </div>
    </div>
    <div class="rs-side-panel__empty">
        {t}Нет виджетов, которые можно добавить на рабочий стол{/t}
    </div>

</div>