{if $visible_alerts->canShow()}
<div class="alert alert-warning viewport m-b-20 c-black visible-alerts-block">
    <a class="pull-right close" style="line-height:100%" data-cookie-name="{$cookie_param_name}" data-cookie-value="{$messages_hash}_{$timestamp}" title="{t}<nobr>Скрыть на 14 дней</nobr>{/t}">&times;</a>

    {foreach $visible_alerts->getMessages() as $message_data}
        {$message_data.message}
        {if $message_data.href}
            <a class="u-link" href="{$message_data.href}" {if $message_data.target}target="{$message_data.target}"{/if}>{$message_data.link_title}</a>
        {/if}<br>
    {/foreach}
</div>
{/if}