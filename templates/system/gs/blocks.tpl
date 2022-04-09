{* Общий шаблон одного блока *}
{if $layouts.blocks[$level.section.id]}
    {foreach $layouts.blocks[$level.section.id] as $block}
        {if $level.section.inset_align != 'wide'}
            <div class="{$level.section->renderGridBlockClass($layouts.grid_system)}">
        {/if}
            {moduleinsert name=$block.module_controller _params_array=$block->getParams()}
        {if $level.section.inset_align != 'wide'}
            </div>
        {/if}
    {/foreach}
{/if}