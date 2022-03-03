{$first=$button->getFirstItem()}

{if count($button->getAllItems())>1}
    <div {$button->getAttrLine()}>
        {if isset($first.attr.href)}
            <a class="split-link {$button->getItemClass($first)}" {$button->getItemAttrLine($first)}>{$first.title}</a>
            <a class="split-caret l-border {$button->getItemClass($first, 'toggle')}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></a>
        {else}
            <a class="split-group {$button->getItemClass($first, 'toggle')}" {$button->getItemAttrLine($first)} data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{$first.title} <span class="caret"></span></a>
        {/if}
        {if count($button->getDropItems())}
            <ul class="dropdown-menu dropdown-menu-right">
            {foreach $button->getDropItems() as $item}
                <li>
                    <a class="{$button->getItemClass($item, 'listitem')}" {$button->getItemAttrLine($item)}>{$item.title}</a>
                </li>
            {/foreach}
            </ul>
        {/if}
    </div>
{else}
    <a class="{$button->getItemClass($first)}" {$button->getItemAttrLine($first)}>{$first.title}</a>
{/if}