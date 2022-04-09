{* Шаблон содержимого контейнера для bootstrap *}
{strip}
{foreach $item as $level}
    {include file="%system%/gs/bootstrap4/section.tpl" level=$level assign=content}

    {if $level.section.outside_template}
        {include file=$level.section.outside_template wrapped_content=$content assign=content}
    {/if}

    {$content}

    {if $level.section.is_clearfix_after}<div class="{$level.section->renderClearfixClass($layouts.grid_system)}"></div>{/if}
{/foreach}
{/strip}