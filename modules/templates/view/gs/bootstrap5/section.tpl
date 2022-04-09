{foreach $item as $level}
    <div class="area {if $level.section.element_type == 'row'}row{else}{*
        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="width" name="b5col"}{*
        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="prefix" name="b5offset"}{*
        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="order" name="b5order"}{*
        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="align-items" name="b5align-self"}{*
        *}{/if}{*
        *} {$level.section->getAnyVisibleClass()}" data-section-id="{$level.section.id}" data-sort-id="{$level.section.sortn}">
        <div class="commontools">            
            {if $level.section.element_type == 'row'}{t}Строка{/t}{else}
            <span class="section-width" data-xs-width="{$level.section.width_xs}"
                                        data-sm-width="{$level.section.width_sm}"
                                        data-md-width="{$level.section.width}"
                                        data-lg-width="{$level.section.width_lg}"
                                        data-xl-width="{$level.section.width_xl}"
                                        data-xxl-width="{$level.section.width_xxl}">&nbsp;</span>
            {/if}
            <span class="drag-handler"></span>

            <div class="dropdown smart-dropdown section-tools">
                <a class="rs-dropdown-handler" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"><!----></i></a>
                <ul class="dropdown-menu">
                    {if $level.section->canInsertModule()}
                        <li>
                            <a class="iplusmodule itool crud-add" data-url="{adminUrl do=addModule section_id=$level.section.id}" title="{t}Добавить модуль{/t}" data-crud-options='{ "dialogId": "blockListDialog", "sectionId": "{$level.section.id}" }'>
                                <i class="zmdi zmdi-plus"></i>
                                <span class="title">{t}Добавить модуль{/t}</span>
                            </a>
                        </li>
                    {/if}
                    {if $level.section->canInsertSection() && $level.section.element_type == 'col'}
                        <li>
                            <a class="iplus itool crud-add" href="{adminUrl do=addSection parent_id=$level.section.id page_id=$currentPage.id element_type="row"}" title="{t}Добавить строку{/t}">
                                <i class="zmdi zmdi-plus-square"></i>
                                <span class="title">{t}Добавить строку{/t}</span>
                            </a>
                        </li>
                    {/if}
                    {if $level.section->canInsertSection() && $level.section.element_type == 'row'}
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
                *}{if empty($level.childs) && $level.section.element_type != 'row'} sort-blocks{/if} {*
                *}{if $level.section.element_type == 'row'}{*
                        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="child_width" name="b5row-cols"}{*
                        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="gutter_x" name="b5gx"}{*
                        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="gutter_y" name="b5gy"}{*
                        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="align_items" name="b5align_items"}{*
                        *}{include file="%templates%/gs/bootstrap5/attribute.tpl" field="inset_align" name="b5justify-content"} {*
                 *}{else}{*
                *}{/if}" {*
                *}{if !empty($level.childs) || !$level.section->getBlocks()}data-section-id="{$level.section.id}"{/if} {*
                *}{if empty($level.childs) && $level.section.element_type != 'row'}data-row-id="{$level.section.id}"{/if} data-sort-id="{$level.section.sortn}">
            {if !empty($level.childs)}
                {include file="%templates%/gs/bootstrap5/section.tpl" item=$level.childs}
            {else}
                {include file="%templates%/gs/blocks.tpl" level=$level}
            {/if}
        </div>
    </div>
    {if $level.section.is_clearfix_after}<div class="b5w-100"></div>{/if}
{/foreach}