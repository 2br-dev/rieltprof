{foreach from=$level.section->getBlocks() item=block}
    <div class="block wide {if $block.public}on{/if} {*
        *}{if $level.section.inset_align == 'left'} alignleft{/if} {*
        *}{if $level.section.inset_align == 'right'}alignright{/if}" {*
        *}data-block-id="{$block.id}" data-sort-id="{$block.sortn}">
        <span class="drag-block-handler"></span>
        <div class="title">
            <div class="title-wide">
                <span class="help-icon" title="{$block->getBlockInfo('description')}">?</span>
                <span class="name">{$block->getBlockInfo('title')}</span>
            </div>
            <div class="title-small">
                <span class="help-icon" title="<strong>{$block->getBlockInfo('title')}</strong><br>{$block->getBlockInfo('description')}">?</span>
            </div>
        </div>
        
        <div class="dropdown smart-dropdown container-tools">
            <a class="rs-dropdown-handler" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>
            <ul class="dropdown-menu">
                <li>
                    <a class="iswitch itool" title="{t}Включить/Выключить{/t}">
                        <i class="zmdi zmdi-power"></i>
                        <span class="title">{t}Включить/Выключить{/t}</span>
                    </a>
                </li>
                <li>
                    <a href="{adminUrl do="editModule" id=$block.id}" class="isettings itool crud-edit" title="{t}Настройка блока{/t}">
                        <i class="zmdi zmdi-settings"></i>
                        <span class="title">{t}Настройка блока{/t}</span>
                    </a>
                </li>
                <li>
                    <a href="{adminUrl do="delModule" id=$block.id}" class="iremove itool crud-remove-one" title="{t}Удалить блок{/t}">
                        <i class="zmdi zmdi-delete"></i>
                        <span class="title">{t}Удалить блок{/t}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
{/foreach}