{if $button->getSplitButton()}
    <div class="btn-group rs-split-button">
        {include file="%system%/admin/html_elements/toolbar/button/button.tpl"}
        {include file="%system%/admin/html_elements/toolbar/button/button.tpl" button=$button->getSplitButton()}
    </div>
{else}
    {include file="%system%/admin/html_elements/toolbar/button/button.tpl"}
{/if}