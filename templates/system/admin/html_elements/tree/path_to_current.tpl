{foreach $tree->getPathToFirst() as $item}
    {if !$item@first}<i class="zmdi zmdi-chevron-right"></i>{/if}
    {$cell=$tree->getMainColumn($item)}
    {if isset($cell->property.href) && !$item@last}<a href="{$cell->getHref()}" class="item call-update">{else}<span class="item">{/if}
    {include file=$cell->getBodyTemplate() cell=$cell}
    {if isset($cell->property.href) && !$item@last}</a>{else}</span>{/if}
{foreachelse}
    <span class="item">{$tree->options.unselectedTitle|default:"{t}Не выбрано{/t}"}</span>
{/foreach}