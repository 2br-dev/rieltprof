{* Шаблон содержимого контейнера для BootStrap *}
{strip}
{foreach $item as $level}
    {include file="%system%/gs/{$layouts.grid_system}/section.tpl" level=$level assign=wrapped_content is_first=$level@first is_last=$level@last}

    {if $level.section.outside_template}
        {include file=$level.section.outside_template wrapped_content=$wrapped_content}
    {else}
        {$wrapped_content}
    {/if}
    
    {if $level.section.is_clearfix_after}<div class="{$level.section->renderClearfixClass($layouts.grid_system)}"></div>{/if}
{/foreach}
{/strip}