{if $fitem->getTitle()}<label {$fitem->getTitleAttrString()}>{$fitem->getTitle()}</label><br>{/if}
{* Если есть префильтры *}
{foreach $fitem->getPrefilters() as $item}
    {$item->getView()}
{/foreach}
{* Конец: Если есть префильтры *}    
{include file=$fitem->tpl}