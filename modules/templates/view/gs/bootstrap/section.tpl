{$devices=['lg' => '_lg', 'md' => '', 'sm' => '_sm', 'xs' => '_xs']}
{foreach from=$item item=level name="sections"}
    <div class="area {if $level.section.element_type == 'row'}row{else}{*
        *}{include file="%templates%/gs/bootstrap/attribute.tpl" field="width"}{*
        *}{include file="%templates%/gs/bootstrap/attribute.tpl" field="prefix" name="offset-"}{*
        *}{include file="%templates%/gs/bootstrap/attribute.tpl" field="pull" name="pull-"}{*
        *}{include file="%templates%/gs/bootstrap/attribute.tpl" field="push" name="push-"}{/if}{*
        *} {$level.section->getAnyVisibleClass()}" data-section-id="{$level.section.id}" data-sort-id="{$level.section.sortn}">
        <div class="commontools">            
            {if $level.section.element_type == 'row'}{t}Строка{/t}{else}
            <span class="section-width" data-xs-width="{$level.section.width_xs}"
                                        data-sm-width="{$level.section.width_sm}"
                                        data-md-width="{$level.section.width}"
                                        data-lg-width="{$level.section.width_lg}">&nbsp;</span>
            {/if}
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
        *}{if empty($level.childs) && $level.section.element_type != 'row'} sort-blocks{/if}" {*
        *}{if empty($level.childs)}data-section-id="{$level.section.id}"{/if} {*
        *} data-sort-id="{$level.section.sortn}">
            {if !empty($level.childs)}
                {include file="%templates%/gs/bootstrap/section.tpl" item=$level.childs}
            {else}
                {include file="%templates%/gs/blocks.tpl" level=$level}
            {/if}
        </div>
    </div>
    {if $level.section.is_clearfix_after}<div class="clearfix"></div>{/if}
{/foreach}