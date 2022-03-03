{* Шаблон содержимого контейнера для BootStrap *}
{strip}
{foreach from=$item item=level name="sections"}
    {include file="%system%/gs/{$layouts.grid_system}/section.tpl" level=$level assign=wrapped_content is_first=$smarty.foreach.sections.first is_last=$smarty.foreach.sections.last} 

    {if $level.section.outside_template}
        {include file=$level.section.outside_template wrapped_content=$wrapped_content}
    {else}
        {$wrapped_content}
    {/if}
    
    {if $level.section.is_clearfix_after}<div class="clearfix {$level.section.clearfix_after_css}"></div>{/if}
{/foreach}
{/strip}