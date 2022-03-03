<div class="grid_{$level.section.width}{*
    *}{if $level.section.prefix} prefix_{$level.section.prefix}{/if}{*
    *}{if $level.section.suffix} suffix_{$level.section.suffix}{/if}{*
    *}{if $level.section.pull} pull_{$level.section.pull}{/if}{*
    *}{if $level.section.push} push_{$level.section.push}{/if}{*
    *}{if $level.section.parent_id>0}{if $is_first} alpha{/if}{*
    *}{if $is_last} omega{/if}{/if} {if $this_controller->getDebugGroup()}
    col-container
    {/if}{*
    *}{if $level.section.css_class} {$level.section.css_class}{/if}" {if $this_controller->getDebugGroup()}data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}"{/if}>
    {if !empty($level.childs)}
        {include file="%system%/gs/{$layouts.grid_system}/sections.tpl" item=$level.childs assign=wrapped_content}
    {else}
        {include file="%system%/gs/blocks.tpl" assign=wrapped_content}
    {/if}

    {$debug_mode=$this_controller->app->getDebugMode()}
    {$wrapped_content=trim($wrapped_content)}
    {if $this_controller->getDebugGroup() && $level.section.element_type=='col'}
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
                    <a href="{$router->getAdminUrl('addSection', ['parent_id' => $level.section.id], 'templates-blockctrl')}" title="{t}Добавление секции внутрь этой{/t}" class="debug-icon crud-add debug-icon-createfill" target="_blank"></a>
                {/if}
                <a href="{$router->getAdminUrl('editSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}Настройки секции{/t}" class="debug-icon crud-add debug-icon-blockoptions" target="_blank"></a>
                <a class="debug-icon debug-icon-delete crud-remove-one" href="{$router->getAdminUrl('delSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}удалить секцию{/t}"></a>
            </div>
    {/if}

    {if $level.section.inset_template}
        {include file=$level.section.inset_template wrapped_content=$wrapped_content}
    {else}
        {if $this_controller->getDebugGroup() && $level.section.element_type=='col'}
            <div class="{if empty($wrapped_content)}{if $debug_mode=='blocks'}section-content is-empty{else}section-row-content row-empty{/if}{else}section-content{/if}" data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}">
        {/if}
        {$wrapped_content}
        {if $this_controller->getDebugGroup() && $level.section.element_type=='col'}
            </div>
        {/if}
    {/if}

    {if $this_controller->getDebugGroup() && $level.section.element_type=='col'}
        </div>
    {/if}
</div>