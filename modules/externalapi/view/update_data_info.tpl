{strip}
<ul>
{foreach $data_scheme as $key => $item}
    {if $key.0 != '@'}
        {if $item['@is_node']}
        {* Выводим информацию о дереве *}
        <li><b>[{$key}]</b>{if $item['@require']}, {t}обязательный{/t}{/if}
            {if $item['@title']}, {$item['@title']}{/if}
            {include file="%externalapi%/update_data_info.tpl" data_scheme=$item}
        </li>
        {else}
        {* Выводим информацию о листьях *}
        <li>
            <b>[{$key}]</b>{if $item['@type']}, <i>{$item['@type']}</i>{/if}
                {if $item['@arrayitemtype']} of {$item['@arrayitemtype']}{/if}
                {if $item['@require']}, {t}обязательный{/t}{/if}
                {if $item['@title']}, {$item['@title']}{/if}
                {if $item['@allowable_values']}<br>{t}возможные значения:{/t}
                    <ul>
                    {foreach $item['@allowable_values'] as $value}
                        <li>{$value}</li>
                    {/foreach}
                    </ul>
                {/if}
        </li>
        {/if}
    {/if}
{/foreach}
</ul>
{/strip}