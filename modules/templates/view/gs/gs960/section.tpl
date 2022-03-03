{foreach from=$item item=level name="sections"}
    <div class="grid_{$level.section.width} block-section-wrapper area {if $level.section.prefix} prefix_{$level.section.prefix}{/if}{if $level.section.suffix} suffix_{$level.section.suffix}{/if}{if $level.section.pull} pull_{$level.section.pull}{/if}{if $level.section.push} push_{$level.section.push}{/if}{if $level.section.parent_id>0}{if $smarty.foreach.sections.first} alpha{/if}{if $smarty.foreach.sections.last} omega{/if}{/if}" data-section-id="{$level.section.id}" data-inset-align="{$level.section.inset_align}" data-section-id="{$level.section.id}">
        <div class="commontools">            
            <span class="section-title">{t}Секция{/t}</span>{$level.section.width}
            <span class="drag-handler"></span>
            
            <div class="dropdown smart-dropdown section-tools">
                <a class="rs-dropdown-handler" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>
                <ul class="dropdown-menu">
                    {if $level.section->canInsertModule()}
                        <li>
                            <a class="iplusmodule itool crud-add" data-url="{adminUrl do=addModule section_id=$level.section.id}" title="{t}Добавить модуль{/t}" data-crud-options='{ "dialogId": "blockListDialog", "sectionId": "{$level.section.id}" }'>
                                <i class="zmdi zmdi-plus"></i>
                                <span class="title">{t}Добавить модуль{/t}</span>
                            </a>
                        </li>
                    {/if}
                    {if $level.section->canInsertSection()}
                        <li>
                            <a class="iplus itool crud-add" href="{adminUrl do=addSection parent_id=$level.section.id page_id=$currentPage.id}" title="{t}Добавить секцию{/t}">
                                <i class="zmdi zmdi-plus-square"></i>
                                <span class="title">{t}Добавить секцию{/t}</span>
                            </a>
                        </li>
                    {/if}
                    <li>
                        <a class="isettings itool crud-edit" href="{adminUrl do=editSection id=$level.section.id}" title="{t}Редактировать секцию{/t}">
                            <i class="zmdi zmdi-settings"></i>
                            <span class="title">{t}Редактировать секцию{/t}</span>
                        </a>
                    </li>
                    <li>
                        <a class="iremove itool crud-remove-one" href="{adminUrl do=delSection id=$level.section.id}" title="{t}Удалить{/t}">
                            <i class="zmdi zmdi-delete"></i>
                            <span class="title">{t}Удалить{/t}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>    
        {if $level.section.is_clearfix_after}<div class="clearfix-after"></div>{/if}
        <div class="workarea{if !empty($level.childs) || !$level.section->getBlocks()} sort-sections{/if} {*
            *}{if empty($level.childs)} sort-blocks{/if}" {*
            *}{if empty($level.childs)}data-section-id="{$level.section.id}"{/if} {*
            *}{if !empty($level.childs) || !$level.section->getBlocks()}data-row-id="{$level.section.id}"{/if} data-sort-id="{$level.section.sortn}">
            {if !empty($level.childs)}
                {include file="%templates%/gs/gs960/section.tpl" item=$level.childs}
            {else}
                {include file="%templates%/gs/blocks.tpl" level=$level}
            {/if}
        </div>
    </div>
    {if $level.section.is_clearfix_after}<div class="clearfix"></div>{/if}
{/foreach}