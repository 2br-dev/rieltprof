{* Общий шаблон одного блока *}
{if $layouts.blocks[$level.section.id]}
    {foreach $layouts.blocks[$level.section.id] as $block}
        {moduleinsert name=$block.module_controller _params_array=$block->getParams()}
    {/foreach}
{/if}