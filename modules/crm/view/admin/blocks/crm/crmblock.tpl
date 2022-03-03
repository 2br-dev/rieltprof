<div id="crmBlockWrapper" class="bordered">
    <h3>CRM</h3>
    <div class="tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
            {if $param.tabs.deal}
                <li class="active">
                    {$tab_deal_content={moduleinsert var="deal_data" name="Crm\Controller\Admin\Block\DealBlock" link_id=$param.tabs.deal.link_id link_type=$param.tabs.deal.link_type}}
                    <a data-target="#crm-block-deal" data-toggle="tab" role="tab">{t}Сделки{/t} <span class="counter crm-deal {if $deal_data.paginator->total}bg-red{/if}">{$deal_data.paginator->total}</span></a>
                </li>
            {/if}
            {if $param.tabs.interaction}
                <li>
                    {$tab_interaction={moduleinsert var="interaction_data" name="Crm\Controller\Admin\Block\InteractionBlock" link_id=$param.tabs.interaction.link_id link_type=$param.tabs.interaction.link_type}}
                    <a data-target="#crm-block-interaction" data-toggle="tab" role="tab">{t}Взаимодействия{/t} <span class="counter crm-interaction {if $interaction_data.paginator->total}bg-red{/if}">{$interaction_data.paginator->total}</span></a>
                </li>
            {/if}
            {if $param.tabs.userInteraction}
                <li>
                    {$tab_userInteraction={moduleinsert var="user_interaction_data" name="Crm\Controller\Admin\Block\InteractionBlock" link_id=$param.tabs.userInteraction.link_id link_type=$param.tabs.userInteraction.link_type}}
                    <a data-target="#crm-block-user-interaction" data-toggle="tab" role="tab">{t}Взаимодействия с пользователем{/t} <span class="counter crm-interaction-user {if $user_interaction_data.paginator->total}bg-red{/if}">{$user_interaction_data.paginator->total}</span></a>
                </li>
            {/if}
            {if $param.tabs.task}
                <li>
                    {$tab_task={moduleinsert var="task_data" name="Crm\Controller\Admin\Block\TaskBlock" link_id=$param.tabs.task.link_id link_type=$param.tabs.task.link_type}}
                    <a data-target="#crm-block-task" data-toggle="tab" role="tab">{t}Задачи{/t} <span class="counter crm-task {if $task_data.paginator->total}bg-red{/if}">{$task_data.paginator->total}</span></a>
                </li>
            {/if}
        </ul>
        <div class="tab-content">
            {if $param.tabs.deal}
                <div class="tab-pane active" role="tabpanel" id="crm-block-deal">
                    {$tab_deal_content}
                </div>
            {/if}
            {if $param.tabs.interaction}
                <div class="tab-pane" role="tabpanel" id="crm-block-interaction">
                    {$tab_interaction}
                </div>
            {/if}
            {if $param.tabs.userInteraction}
                <div class="tab-pane" role="tabpanel" id="crm-block-user-interaction">
                    {$tab_userInteraction}
                </div>
            {/if}
            {if $param.tabs.task}
                <div class="tab-pane" role="tabpanel" id="crm-block-task">
                    {$tab_task}
                </div>
            {/if}
        </div>
    </div>
</div>