{$need_initialize = ($load_recursive) ? '' : 'need-initialize'}

{foreach $iterator as $node}
    <li class="tree-select_list-item {if $node->getChildsCount()}tree-branch tree-collapsed {$need_initialize}{else}tree-leaf{/if}" data-id="{$node->getId()}">
        <div class="tree-select_list-item_row tree-row">
            <i class="tree-select_list-item_sublist-toggle zmdi tree-branch-toggle"></i>
            <span class="tree-select_list-item_title">{$node->getName()}</span>
        </div>
        {if $node->getChildsCount()}
            <ul class="tree-select_list-item_sublist">
                {if $load_recursive}
                    {include file="%system%/coreobject/type/form/treelistbox_branch.tpl" iterator=$node->getChilds()}
                {/if}
            </ul>
        {/if}
    </li>
{/foreach}