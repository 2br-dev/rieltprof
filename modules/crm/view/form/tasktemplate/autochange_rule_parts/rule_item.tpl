{foreach $rules as $uniq => $rule}
    <div class="autochange-rule" data-uniq="{$uniq}">
        <a class="rule-remove">&times;</a>
        <div class="set-status-text">
            {t}Установить статус:{/t}
            <select name="autochange_status_rule_arr[{$uniq}][set_status]">
                {html_options options=$statuses selected=$rule.set_status}
            </select>
            если:
        </div>

        <div class="groups">
            {include file="%crm%/form/tasktemplate/autochange_rule_parts/group_item.tpl" groups=$rule.groups rule_uniq="{$uniq}"}
        </div>

        <div class="autochange-group-tools">
            <a class="btn btn-sm btn-alt btn-primary add-autochange-group">{t}добавить группу условий{/t}</a>
        </div>
    </div>
{/foreach}