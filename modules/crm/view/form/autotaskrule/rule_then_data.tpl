{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="%crm%/jquery.rs.taskrule.js"}
<div class="task-rule-block" data-urls='{ "add": "{adminUrl do="AddTaskRule" mod_controller="Crm-AutoTaskRuleCtrl"}" }'>
    <div class="tools-top">
        <a class="btn btn-success add-autotask">{t}Добавить задачу{/t}</a>
    </div>

    {$tasks = $elem->getTasks()}
    <div class="table-mobile-wrapper" {if !$tasks}style="display:none;"{/if}>
        <table class="rs-table task-container ">
            <thead>
                <tr>
                    <th width="20" style="width:20px;"></th>
                    <th>№</th>
                    <th>{t}Суть задачи{/t}</th>
                    <th>{t}Исполнитель{/t}</th>
                    <th>{t}Срок{/t}</th>
                    <th>{t}Автостатус{/t}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach $tasks as $task_tpl}
                    {include file="%crm%/form/autotaskrule/rule_then_data_item.tpl" task_tpl=$task_tpl index=$task_tpl@iteration}
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<script>
    $.contentReady(function(){
        $('.task-rule-block').taskRule();
    });
</script>