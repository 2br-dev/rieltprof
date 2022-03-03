{if isset($local_options.render_all_nodes)}
    {$render_all_nodes = $local_options.render_all_nodes}
{/if}
{if isset($local_options.render_opened_nodes)}
    {$render_opened_nodes = $local_options.render_opened_nodes}
{/if}
{if isset($local_options.forced_open_nodes)}
    {$forced_open_nodes = $local_options.forced_open_nodes}
{/if}

{foreach $list as $key => $item}
    {$object = $item->getObject()}
    {$is_disabled = (isset($tree->options.disabledField) && $object[$tree->options.disabledField] === $tree->options.disabledValue) ? 'disabled' : ''}
    {$is_current = (isset($tree->options.activeField) && $tree->options.activeValue == $object[$tree->options.activeField]) ? 'current' : ''}
    {$class_field = ($object[$tree->options.classField]) ? $object[$tree->options.classField] : ''}
    {$is_opened = $item->isOpened() || $forced_open_nodes}
    {$render_childs = $render_all_nodes || ($render_opened_nodes && $is_opened)}
    {$closed_class = ($is_opened) ? 'tree-expanded' : 'tree-collapsed'}
    {$is_branch = ($item->getChildsCount()) ? 'tree-branch' : ''}
    {$need_initialize = ($item->getChildsCount() && !$render_childs) ? 'need-initialize' : ''}
    {$is_root = ($object.is_root_element) ? 'root noDraggable' : ''}

    {$item_id = $object[$tree->options.sortIdField]}

    <li class="{$is_disabled} {$is_current} {$class_field} {$closed_class} {$is_branch} {$need_initialize} {$is_root}" {if $tree->isNoDraggable($object)}data-notmove="notmove"{/if} data-id="{$item_id}">
        <div class="item">
            <div class="chk" unselectable="on">
                {if !$tree->isNoCheckbox($object) && !$tree->options.noCheckbox}
                    <input type="checkbox" name="{$tree->getCheckboxName()}" value="{$object[$tree->options.activeField]}" {if $tree->isChecked($object[$tree->options.activeField])}checked{/if} {if $tree->isDisabledCheckbox($object)}disabled{/if}>
                {/if}
            </div>
            <div class="line">
                <div class="toggle">
                    <i class="zmdi"></i>
                </div>
                {if $tree->options.sortable}<div class="move{if $tree->isNoDraggable($object)} no-move{/if}"><i class="zmdi zmdi-unfold-more"></i></div>{/if}
                {if !$tree->isNoRedMarker($object)}
                    <div class="redmarker"></div>
                {/if}
                <div class="data">
                    <div class="textvalue">
                        {$cell=$tree->getMainColumn($object)}
                        {if isset($cell->property.href)}<a href="{$cell->getHref()}" {$cell->getLinkAttr()}>{/if}
                        {include file=$cell->getBodyTemplate() cell=$cell}
                        {if isset($cell->property.href)}</a>{/if}
                    </div>
                    {if !$tree->isNoOtherColumns($object)}
                        {if isset($object.treeTools)}
                            {$object.treeTools->setRow($object)|devnull}
                            {include file=$object.treeTools->getBodyTemplate() cell=$object.treeTools}
                        {else}
                            {if $tree->getTools()}
                                {include file=$tree->getTools()->getBodyTemplate() cell=$tree->getTools($object)}
                            {/if}
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
        {if $item->getChildsCount()}
            <ul class="childroot">
                {if $render_childs}
                    {include file="%system%/admin/html_elements/tree/tree_branch.tpl" list=$item.child level=$level+1}
                {/if}
            </ul>
        {/if}
    </li>
{/foreach}