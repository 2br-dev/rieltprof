<div class="tel-line">
    <div class="tel-row">
        {$count = $client->getLastOneClick(false)}
        {if $count}
            {$filter = [
            "user_phone" => $client.phone
            ]}
        {else} {$filter = null} {/if}

        <a href="{adminUrl do=false mod_controller="catalog-oneclickctrl" f=$filter}">{t}Покупок в 1-клик{/t}: {if $count > 0}{$count}{else}{t}нет{/t}{/if}</a>
        {if $count}
            <div class="tel-dot"></div>
            <div>
                <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
            </div>
        {/if}
    </div>
    {if $count}
        <div class="tel-more-block">
            {foreach $client->getLastOneClick() as $oneclick}
                <a href="{adminUrl do=edit id=$oneclick.id mod_controller="catalog-oneclickctrl"}" class="crud-edit">№{$oneclick.id} от {$oneclick.dateof|dateformat:"@date"}</a>
            {/foreach}
        </div>
    {/if}
</div>