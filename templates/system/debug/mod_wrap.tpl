<div class="module-wrapper {if $show_constructor_controls}can-drag{/if}" {*
     *}{if $show_constructor_controls}data-sort-url="{$router->getAdminUrl('ajaxMoveBlock', ['block_id' => $block_id, 'ajax' => 1], 'templates-blockctrl')}"{/if} {*
     *}data-block-id="{$block_id}" data-sortn="{$sortn}" {$group->getDebugAttributes()}>
    {if $show_constructor_controls}
        <div id="drag-placeholder-top" class="drag-placeholder-top">{* Линия для показа при перетаскивании *}</div>
        <div id="drag-placeholder-bottom" class="drag-placeholder-bottom">{* Линия для показа при перетаскивании *}</div>
        <a href="{$router->getAdminUrl('addModule', ['block_id' => $block_id, 'position' => 'before'], 'templates-blockctrl')}" data-crud-options='{ "dialogId": "blockListDialog", "beforeCallback": "addConstructorModuleSectionId", "blockId": "{$block_id}", "position": "before" }' title="{t}Добавить блок перед этим{/t}" class="module-add-block-to-position debug-add-to-position top crud-add"></a>
        <a href="{$router->getAdminUrl('addModule', ['block_id' => $block_id, 'position' => 'before'], 'templates-blockctrl')}" data-crud-options='{ "dialogId": "blockListDialog", "beforeCallback": "addConstructorModuleSectionId", "blockId": "{$block_id}", "position": "after" }' title="{t}Добавить блок после этого{/t}" class="module-add-block-to-position debug-add-to-position bottom crud-add"></a>
        <span class="drag-all-block-handler">{* Блок перетаскивания *}</span>
        <div class="drag-overflow">
    {/if}
        {if !$is_main_content_block}
            <div class="module-tools debug-tools">
                <div class="dragblock">&nbsp;</div>
                {foreach from=$group->getTools() item=tool}
                    {$tool->getView()}
                {/foreach}
            </div>
        {/if}
        <div class="module-content">
            {$result_html}
        </div>
    {if $show_constructor_controls}
        </div>
    {/if}
</div>