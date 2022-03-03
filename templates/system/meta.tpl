{foreach from=$meta_vars item=tagparam}
<meta {foreach from=$tagparam key=key item=value}{$key}="{$value|replace:'"':'&quot;'}" {/foreach}>
{/foreach}