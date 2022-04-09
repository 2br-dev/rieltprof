<div class="block-section-wrapper {if empty($level.childs) && empty($wrapped_content)}section-empty{/if}" data-section-id="{$level.section.id}" data-sortn="{$level.section.sortn}">
    <span class="drag-section-handler drag-all-block-handler">{* Блок перетаскивания секции *}</span>
    <div id="drag-placeholder-section-top" class="drag-placeholder-top">{* Линия для показа при перетаскивании *}</div>
    <div id="drag-placeholder-section-bottom" class="drag-placeholder-bottom">{* Линия для показа при перетаскивании *}</div>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'position' => 'before'], 'templates-blockctrl')}" title="{t}Добавить секцию перед этой{/t}" class="section-add-section-to-position debug-add-to-position top crud-add"></a>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'position' => 'after'], 'templates-blockctrl')}" title="{t}Добавить секцию после этой{/t}" class="section-add-section-to-position debug-add-to-position bottom crud-add"></a>
    <div class="section-tools debug-tools">
        <div class="dragblock">&nbsp;</div>
        <a href="{$router->getAdminUrl('addModule', ['section_id' => $level.section.id], 'templates-blockctrl')}" data-crud-options='{ "dialogId": "blockListDialog", "beforeCallback": "addConstructorModuleSectionId", "sectionId": "{$level.section.id}", "position": "last" }' title="{t}Добавление блока{/t}" class="debug-icon crud-add debug-icon-create" target="_blank"></a>
        {if empty($level.childs) && empty($wrapped_content)}
            <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'parent_id' => $level.section.id, 'element_type' => 'row'], 'templates-blockctrl')}" title="{t}Добавление строки внутрь{/t}" class="debug-icon crud-add debug-icon-createfill" target="_blank"></a>
        {/if}
        <a href="{$router->getAdminUrl('editSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}Настройки секции{/t}" class="debug-icon crud-add debug-icon-blockoptions" target="_blank"></a>
        <a class="debug-icon debug-icon-delete crud-remove-one" href="{$router->getAdminUrl('delSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}удалить секцию{/t}"></a>
    </div>
    {$debug_mode=$this_controller->app->getDebugMode()}
    {$wrapped_content=trim($wrapped_content)}
    <div class="{if empty($wrapped_content)}{if $debug_mode=='blocks'}section-content is-empty{else}section-row-content row-empty{/if}{else}section-content{/if}" data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}">
{*    <div class="section-content {if empty($wrapped_content)}container-rows-content is-empty{/if}" data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}">*}
        {$wrapped_content}
    </div>
</div>