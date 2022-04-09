{if $dirlist->count()}
    {foreach $dirlist as $node} {* Первый уровень *}
        {$dir = $node->getObject()}
        <li>
            <a class="offcanvas__list-item" href="{$dir->getUrl()}" {$dir->getDebugAttributes()}>{$dir.name}</a>
            {if $node->getChildsCount()}
                <ul class="offcanvas__subnav offcanvas__list">
                    {foreach $node->getChilds() as $sub_node} {* Второй уровень *}
                        {$sub_dir = $sub_node->getObject()}
                        <li><a class="offcanvas__list-item" href="{$sub_dir->getUrl()}" {$sub_dir->getDebugAttributes()}>{$sub_dir.name}</a>
                            {if $sub_node->getChildsCount()}
                                <ul class="offcanvas__subnav offcanvas__list">
                                    {foreach $sub_node->getChilds() as $sub_node2} {* Третий уровень *}
                                        {$sub_dir2 = $sub_node2->getObject()}
                                        <li><a class="offcanvas__list-item" href="{$sub_dir2->getUrl()}" {$sub_dir2->getDebugAttributes()}>{$sub_dir2.name}</a>
                                    {/foreach}
                                </ul>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </li>
    {/foreach}
{/if}