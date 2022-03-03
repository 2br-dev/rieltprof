{* Шаблон содержимого контейнера для bootstrap *}
{strip}
{foreach from=$item item=level name="sections"}     
    {include file="%system%/gs/{$layouts.grid_system}/section.tpl" level=$level assign=wrapped_content} 

    {if $level.section.outside_template}
        {include file=$level.section.outside_template wrapped_content=$wrapped_content}
    {else}
        {$wrapped_content}
    {/if}
    
    {if $level.section.is_clearfix_after}<div class="w-100 {$level.section.clearfix_after_css}"></div>{/if}
{/foreach}
{/strip}