<form method="GET" action="{$pcontrol->action}" class="paginator {if !$local_options.no_ajax}form-call-update{/if}">
    {foreach $pcontrol->hidden_fields as $key => $val}
        <input type="hidden" name="{$key}" value="{$val}">
    {/foreach}

    {$pcontrol->element->getView($local_options)}
</form>