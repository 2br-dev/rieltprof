<div class="tel-line">
    <div class="tel-row">
        {$interaction_count = $client->getLastInteractions(false)}
        {if $interaction_count}
            {if $client.id > 0}
                {$filter = [
                "links" => [
                "crm-linktypeuser" => ["user_id" => $client.id],
                "crm-linktypecall" => ["call_history_id" => $call_history.id]
                ]]}
            {else}
                {$filter = [
                "links" => [
                "crm-linktypecall" => ["call_history_id" => $call_history.id]
                ]
                ]}
            {/if}
        {else} {$filter = null} {/if}
        <a href="{adminUrl do=false mod_controller="crm-interactionctrl" f=$filter}">{t}Взаимодействий{/t}: {if $interaction_count > 0}{$interaction_count}{else}{t}нет{/t}{/if}</a>
        <div class="tel-dot"></div>
        <div>
            {if $interaction_count}
                <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
            {/if}
            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-interactionctrl"}" class="btn btn-warning btn-rect btn-inline zmdi zmdi-plus crud-add" title="{t}Создать взаимодействие{/t}"></a>
        </div>
    </div>

    {if $interaction_count}
        <div class="tel-more-block">
            {foreach $client->getLastInteractions() as $interaction}
                <a href="{adminUrl do=edit id=$interaction.id mod_controller="crm-interactionctrl"}" class="crud-edit">{$interaction.date_of_create|dateformat:"@date @time"}</a>
            {/foreach}
        </div>
    {/if}
</div>