<div class="block-row-wrapper {$row_wrapper_class}" data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}">
    <div id="drag-placeholder-row-top" class="drag-placeholder-top">{* Линия для показа при перетаскивании *}</div>
    <div id="drag-placeholder-row-bottom" class="drag-placeholder-bottom">{* Линия для показа при перетаскивании *}</div>
    <span class="drag-row-handler drag-all-block-handler">{* Блок перетаскивания строки *}</span>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'parent_id' => $level.section.parent_id, 'element_type' => 'row', 'position' => 'before'], 'templates-blockctrl')}" title="{t}Добавить строку перед этой{/t}" class="row-add-row-to-position debug-add-to-position top crud-add"></a>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'parent_id' => $level.section.parent_id, 'element_type' => 'row', 'position' => 'after'], 'templates-blockctrl')}" title="{t}Добавить строку после этой{/t}" class="row-add-row-to-position debug-add-to-position bottom crud-add"></a>
    <div class="row-tools debug-tools">
        <div class="dragblock">&nbsp;</div>
        <a href="{$router->getAdminUrl('addSection', ['parent_id' => $level.section.id], 'templates-blockctrl')}" title="{t}Добавление секции в строку{/t}" class="debug-icon crud-add debug-icon-create" target="_blank"></a>
        <a href="{$router->getAdminUrl('editSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}Настройки строки{/t}" class="debug-icon crud-add debug-icon-blockoptions" target="_blank"></a>
        <a class="debug-icon debug-icon-delete crud-remove-one" href="{$router->getAdminUrl('delSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}удалить строку{/t}"></a>
    </div>
    {$wrapped_content}
</div>