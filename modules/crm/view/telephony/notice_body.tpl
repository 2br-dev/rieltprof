<div class="updatable" data-update-replace="true" data-no-update-hash data-url="{adminUrl do=refreshCallWindow mod_controller="crm-callactions" call_id=$call_history.call_id}">
    <a data-url="{adminUrl do="closeCallWindow" mod_controller="crm-callactions" call_id="{$call_history.call_id}"}" class="close zmdi zmdi-close" title="{t}Закрыть{/t}"></a>
    <a class="tel-view-toggler zmdi zmdi-chevron-right" title="{t}Свернуть{/t}"></a>

    <div class="tel-body">
        <h4 class="tel-caption {if $call_history.call_status != 'CALLING'}tel-caption-small{/if}">{$call_history.__call_flow->textView()} {t}звонок{/t}</h4>
        {if $call_history.call_status == 'ANSWER'}
            <p class="tel-sub-caption">{t}Идет разговор{/t}</p>
        {elseif $call_history.call_status == 'HANGUP'}
            <p class="tel-sub-caption">{t}Звонок завершен{/t}</p>
        {/if}
        <div class="tel-user-info">
            <div class="icon">
                <span class="icon-circle">
                    <i class="zmdi zmdi-{$call_history->getCallStatusIconClass()} rubberBand animated infinite"></i>
                </span>
            </div>
            <div class="user-data">
                {$caller = $call_history->getCallerUser()}
                <div class="user">{$caller->getFio()}</div>
                <div class="phone">{$caller.phone}</div>
            </div>
        </div>
        {$groups = $caller->getUserGroups(false)}
        <div class="tel-groups">
            {foreach $groups as $group}{if !$group@first}, {/if}{$group.name}{/foreach}
        </div>
        {$client = $call_history->getOtherUser()}
        {$client['call_history'] = $call_history}
        <div class="tel-key-value tel-top-border">
            {hook name="crm-telephony-notice_body:key-value" title="Уведомление о звонке:информация о пользователе"}
            {* Заказы *}
            {include file="%crm%/telephony/notice_info/order.tpl"}

            {* Сделки *}
            {include file="%crm%/telephony/notice_info/deal.tpl"}

            {* Взаимодействия *}
            {include file="%crm%/telephony/notice_info/interaction.tpl"}

            {* Задачи *}
            {include file="%crm%/telephony/notice_info/task.tpl"}

            {* Покупок в 1 клик *}
            {include file="%crm%/telephony/notice_info/oneclick.tpl"}

            {* Предзаказы *}
            {include file="%crm%/telephony/notice_info/reservation.tpl"}

            {* Поддержка *}
            {include file="%crm%/telephony/notice_info/support.tpl"}
            {/hook}
        </div>
        <div class="tel-error hidden"></div>
    </div>
    {$actions = $call_history->getCallActions()}
    {if $actions}
        <div class="tel-footer-actions">
            {foreach $actions as $action}
                <a {foreach $action.attr as $key => $value}{$key}="{$value}" {/foreach}>{$action.text}</a>
            {/foreach}
        </div>
    {/if}
</div>