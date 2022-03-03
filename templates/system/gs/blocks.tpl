{* Общий шаблон одного блока *}
{if $layouts.blocks[$level.section.id]}
    {foreach from=$layouts.blocks[$level.section.id] item=block}
    {if $level.section.inset_align != 'wide'}
    <div class="gridblock{if $level.section.inset_align == 'left'} alignleft{/if}{if $level.section.inset_align == 'right'} alignright{/if}">
    {/if}
        {moduleinsert name=$block.module_controller _params_array=$block->getParams()}
    {if $level.section.inset_align != 'wide'}
    </div>
    {/if}
    {/foreach}
{/if}