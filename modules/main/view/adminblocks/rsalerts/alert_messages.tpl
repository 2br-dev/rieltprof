{if $messages}
    <div class="list-group rs-alerts-section">
        {foreach $messages as $message}
            <a class="list-group-item {$message.status}" {if $message.href}href="{$message.href}"{/if} {if $message.target}target="{$message.target}"{/if}>
                {if $message.close}
                <span class="rs-alert-close close zmdi zmdi-close {$message.close.class}" data-url="{$message.close.url}"></span>
                {/if}
                <span class="message">{$message.message}</span>
                {if $message.description}
                    <span class="description">{$message.description}</span>
                {/if}
            </a>
        {/foreach}
    </div>
{else}
    <div class="rs-side-panel__empty">
        {t}Нет уведомлений{/t}
    </div>
{/if}