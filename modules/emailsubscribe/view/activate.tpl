<div class="wrapperSubscribeWindow" data-dialog-options='{ "width": "500" }'>
    {if $success}
        <div class="formSuccessText">
            {$success}
        </div>
    {else}
        {foreach $errors as $error}
            <p class="error">{$error}</p>
        {/foreach}
    {/if}
</div>