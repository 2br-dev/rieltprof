{capture name="section" assign="content"}
    {if $this_controller->getDebugGroup()}
        {$debug_modifier_class = "{if $level.section.element_type == 'col'}col-container{/if}{if $level.section.element_type=='row'} section-row-content is-row {if empty($level.childs)}row-empty{/if}{/if}"}
    {/if}

    {if !$level.section.invisible}<div class="{$level.section->renderElementClass($layouts.grid_system, $debug_modifier_class)}" {if $this_controller->getDebugGroup()}data-section-id="{$level.section.id}" data-sort-url="{$router->getAdminUrl('ajaxMoveSection', null, 'templates-blockctrl')}"{/if}>{/if}
        {if !empty($level.childs)}
            {include file="%system%/gs/bootstrap4/sections.tpl" item=$level.childs assign=wrapped_content}
        {else}
            {include file="%system%/gs/bootstrap4/blocks.tpl" assign=wrapped_content}
        {/if}

        {if $level.section.inset_template}
            {include file=$level.section.inset_template wrapped_content=$wrapped_content assign=wrapped_content}
        {/if}

        {if $this_controller->getDebugGroup() && $level.section.element_type == 'col' && !$level.section.invisible}
            {* Добавляем разметку для перетаскивания секций в режиме отладки *}
            {include file='%system%/debug/section_wrap.tpl' wrapped_content=$wrapped_content assign=wrapped_content}
        {/if}

        {$wrapped_content}

    {if !$level.section.invisible}</div>{/if}
{/capture}
{if $this_controller->getDebugGroup() && $level.section.element_type == 'row'}
    {* Добавляем разметку для перетаскивания строк в режиме отладки *}
    {include file='%system%/debug/row_wrap.tpl' wrapped_content=$content}
{else}
    {$content}
{/if}