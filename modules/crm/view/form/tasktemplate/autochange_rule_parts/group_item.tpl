{foreach $groups as $group_key => $group}
    <div class="group" data-uniq="{$group_key}">
        <a class="group-remove">&times;</a>
        <div class="group-items">
            {include file="%crm%/form/tasktemplate/autochange_rule_parts/or_item.tpl"
                group_items=$group.items
                group_key=$group_key
                rule_uniq=$rule_uniq}
        </div>
        <div class="one-group-tools">
            <a class="btn btn-sm btn-alt btn-primary add-autochange-oritem">{t}добавить условие ИЛИ{/t}</a>
        </div>
    </div>
{/foreach}