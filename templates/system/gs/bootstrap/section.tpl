{if $this_controller->getDebugGroup() && $level.section.element_type=='row'}
<div class="block-row-wrapper bootstrap3" data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}">
    <span class="drag-row-handler drag-all-block-handler">{* Блок перетаскивания строки *}</span>
    <div id="drag-placeholder-row-top" class="drag-placeholder-top">{* Линия для показа при перетаскивании *}</div>
    <div id="drag-placeholder-row-bottom" class="drag-placeholder-bottom">{* Линия для показа при перетаскивании *}</div>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'parent_id' => $level.section.parent_id, 'element_type' => 'row', 'position' => 'before'], 'templates-blockctrl')}" title="{t}Добавить строку перед этой{/t}" class="row-add-row-to-position debug-add-to-position top crud-add"></a>
    <a href="{$router->getAdminUrl('addSection', ['section_id' => $level.section.id, 'parent_id' => $level.section.parent_id, 'element_type' => 'row', 'position' => 'after'], 'templates-blockctrl')}" title="{t}Добавить строку после этой{/t}" class="row-add-row-to-position debug-add-to-position bottom crud-add"></a>
    <div class="row-tools debug-tools">
        <div class="dragblock">&nbsp;</div>
        <a href="{$router->getAdminUrl('addSection', ['parent_id' => $level.section.id], 'templates-blockctrl')}" title="{t}Добавление секции в строку{/t}" class="debug-icon crud-add debug-icon-create" target="_blank"></a>
        <a href="{$router->getAdminUrl('editSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}Настройки строки{/t}" class="debug-icon crud-add debug-icon-blockoptions" target="_blank"></a>
        <a class="debug-icon debug-icon-delete crud-remove-one" href="{$router->getAdminUrl('delSection', ['id' => $level.section.id], 'templates-blockctrl')}" title="{t}удалить строку{/t}"></a>
    </div>
{/if}
<div class="{if $level.section.element_type == 'row'}row {if $level.section.css_class}{$level.section.css_class}{/if} {else}{*
    *}{include file="%system%/gs/bootstrap/attribute.tpl" field="width"}{*
    *}{include file="%system%/gs/bootstrap/attribute.tpl" field="prefix" name="offset-"}{*
    *}{include file="%system%/gs/bootstrap/attribute.tpl" field="pull" name="pull-"}{*
    *}{include file="%system%/gs/bootstrap/attribute.tpl" field="push" name="push-"}{/if} {*
    *}{if $level.section.css_class}{$level.section.css_class}{/if} {*
    *}{if $this_controller->getDebugGroup()}{*
    *}{if $level.section.element_type == 'col'}col-container bootstrap3{/if}{if $level.section.element_type=='row'} section-row-content is-row {if empty($level.childs)}row-empty{/if}{/if}{*
    *}{/if}" {if $this_controller->getDebugGroup()}data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}"{/if}>{strip}

    {if !empty($level.childs)}
        {include file="%system%/gs/bootstrap/sections.tpl" item=$level.childs assign=wrapped_content}
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

    {/strip}
</div>
{if $this_controller->getDebugGroup() && $level.section.element_type=='row'}
</div>
{/if}