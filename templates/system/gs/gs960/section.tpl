<div class="{$level.section->renderElementClass($layouts.grid_system)}{*
    *}{if $level.section.parent_id>0}{if $is_first} alpha{/if}{*
    *}{if $is_last} omega{/if}{/if} {if $this_controller->getDebugGroup()}
    col-container
    {/if}" {if $this_controller->getDebugGroup()}data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}"{/if}>
    {if !empty($level.childs)}
        {include file="%system%/gs/{$layouts.grid_system}/sections.tpl" item=$level.childs assign=wrapped_content}
    {else}
        {include file="%system%/gs/blocks.tpl" assign=wrapped_content}
    {/if}

    {if $level.section.inset_template}
        {include file=$level.section.inset_template wrapped_content=$wrapped_content assign=wrapped_content}
    {/if}

    {if $this_controller->getDebugGroup() && $level.section.element_type == 'col'}
        {* Добавляем разметку для перетаскивания секций в режиме отладки *}
        {include file='%system%/debug/section_wrap.tpl' wrapped_content=$wrapped_content assign=wrapped_content}
    {/if}

    {$wrapped_content}
</div>