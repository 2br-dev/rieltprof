{if $items->count()}
    {foreach $items as $node} {* Первый уровень *}
        {$menu = $node->getObject()}
        <li {if $node@first}class="offcanvas__list-separator"{/if}>
            <a class="offcanvas__list-item" href="{$menu->getHref()|default:"#"}" {if $menu.target_blank}target="_blank"{/if} {$menu->getDebugAttributes()}>{$menu.title}</a>
            {if $node->getChildsCount()}
                <ul class="offcanvas__subnav offcanvas__list">
                    {foreach $node->getChilds() as $sub_node} {* Второй уровень *}
                        {$sub_menu = $sub_node->getObject()}
                        <li><a class="offcanvas__list-item" href="{$sub_menu->getHref()|default:"#"}" {if $sub_menu.target_blank}target="_blank"{/if} {$sub_menu->getDebugAttributes()}>{$sub_menu.title}</a>
                            {if $sub_node->getChildsCount()}
                                <ul class="offcanvas__subnav offcanvas__list">
                                    {foreach $sub_node->getChilds() as $sub_node2} {* Третий уровень *}
                                    {$sub_menu2 = $sub_node2->getObject()}
                                    <li><a class="offcanvas__list-item" href="{$sub_menu2->getHref()|default:"#"}" {if $sub_menu2.target_blank}target="_blank"{/if} {$sub_menu2->getDebugAttributes()}>{$sub_menu2.title}</a>
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