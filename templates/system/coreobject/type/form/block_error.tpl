{if !$view_options || $view_options.error}
<div class="field-error top-corner" data-field="{$field->getFormName()}">
    {if $field->hasErrors()}
        <span class="text"><i class="cor"></i>
        {foreach from=$field->getErrors() item=error}
            {$error}<br>
        {/foreach}
        </span>
    {/if}
</div>
{/if}