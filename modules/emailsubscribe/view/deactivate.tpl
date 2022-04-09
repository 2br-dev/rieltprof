<div class="text-center section">
    {if $success}
        <div class="mb-4">
            <img width="64" height="64" src="{$THEME_IMG}/decorative/success.svg" alt="">
        </div>
        {$success}
    {else}
        <div class="mb-4">
            <img width="64" height="64" src="{$THEME_IMG}/decorative/danger.svg" alt="">
        </div>
        {$errors|join:", "}
    {/if}
</div>