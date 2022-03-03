{if ModuleManager::staticModuleExists('shop')}
    <div class="tel-line">
        <div class="tel-row">
            {$count = $client->getLastReservation(false)}
            {if $count}
                {$filter = [
                "phone" => $client.phone
                ]}
            {else} {$filter = null} {/if}

            <a href="{adminUrl do=false mod_controller="shop-reservationctrl" f=$filter}">{t}Предзаказов{/t}: {if $count > 0}{$count}{else}{t}нет{/t}{/if}</a>
            {if $count}
                <div class="tel-dot"></div>
                <div>
                    <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
                </div>
            {/if}
        </div>
        {if $count}
            <div class="tel-more-block">
                {foreach $client->getLastReservation() as $oneclick}
                    <a href="{adminUrl do=edit id=$task.id mod_controller="crm-taskctrl"}" class="crud-edit">№{$oneclick.id} от {$oneclick.dateof|dateformat:"@date"}</a>
                {/foreach}
            </div>
        {/if}
    </div>
{/if}