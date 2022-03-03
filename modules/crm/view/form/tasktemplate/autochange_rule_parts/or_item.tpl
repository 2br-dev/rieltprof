{foreach $group_items as $n => $group_item}
    <div class="group-item" data-uniq="{$n}">
        <span class="or-text">{t}или{/t}</span>

        {t}задача{/t} <input type="text" name="autochange_status_rule_arr[{$rule_uniq}][groups][{$group_key}][items][{$n}][task_index]" value="{$group_item.task_index}" size="3">
        {t}перешла в статус{/t}
        <select name="autochange_status_rule_arr[{$rule_uniq}][groups][{$group_key}][items][{$n}][task_status]">
            {html_options options=$statuses selected=$group_item.task_status}
        </select>
        <a class="c-red f-18 m-r-5 zmdi zmdi-delete btn btn-default remove"></a>
    </div>
{/foreach}