{* Одна ветка меню *}
{foreach $menu_level as $node}
    {$item = $node->getObject()}
    <li class="{if $node->getChildsCount()}node{/if}{if $item->isAct()} act{/if}{if $item@first} first{/if}" {$item->getDebugAttributes()}>
        <a href="{$item->getHref()}" {if $item.target_blank}target="_blank"{/if}>{$item.title}</a>
        {if $node->getChildsCount()}
            <ul>
                {include file="blocks/menu/branch.tpl" menu_level=$node->getChilds()}
            </ul>
        {/if}
    </li>
{/foreach}