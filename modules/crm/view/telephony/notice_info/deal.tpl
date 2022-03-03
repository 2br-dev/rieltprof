<div class="tel-line">

    <div class="tel-row">
        {$deal_count = $client->getLastDeals(false)}
        {if $deal_count}
            {if $client.id>0}
                {$filter = ["client_id" => $client.id]}
            {else}
                {$filter = ["client_name" => $client->phone]}
            {/if}
        {else} {$filter = null} {/if}

        <a href="{adminUrl do=false mod_controller="crm-dealctrl" f=$filter}">{t}Сделок{/t}: {if $deal_count > 0}{$deal_count}{else}{t}нет{/t}{/if}</a>
        <div class="tel-dot"></div>
        <div>
            {if $deal_count}
                <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
            {/if}
            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-dealctrl"}" class="btn btn-warning btn-rect btn-inline zmdi zmdi-plus crud-add" title="{t}Создать сделку{/t}"></a>
        </div>
    </div>

    {if $deal_count}
        <div class="tel-more-block">
            {foreach $client->getLastDeals() as $deal}
                <a href="{adminUrl do=edit id=$deal.id mod_controller="crm-dealctrl" from_call=$call_history.id}" class="crud-edit">{$deal.deal_num}</a>
            {/foreach}
        </div>
    {/if}
</div>