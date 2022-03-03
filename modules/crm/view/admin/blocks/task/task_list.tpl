{addjs file="%crm%/jquery.rs.blockcrm.js"}
<div class="crm-block-task" data-refresh-url="{$this_controller->makeUrl()}"

     data-remove-url="{adminUrl do=false
                                taskdo="remove"
                                link_type=$link_type
                                link_id=$link_id
                                mod_controller="crm-block-taskblock"}" >

    <div class="tools-top">
        <a class="btn btn-success add-task va-m-c" data-url="{adminUrl do=add
                                                                            link_type=$link_type
                                                                            link_id=$link_id
                                                                            mod_controller="crm-taskctrl"}">
            <i class="zmdi zmdi-plus m-r-5 f-18"></i>
            <span>{t}Добавить задачу{/t}</span>
        </a>
    </div>

    <div class="table-mobile-wrapper">
        <table class="rs-table values-list localform">
            <thead>
            <tr>
                <th class="chk" style="width:26px">
                    <div class="chkhead-block">
                        <input type="checkbox" data-name="task[]" class="chk_head select-page" title="{t}Отметить элементы на этой странице{/t}">
                        <div class="onover">
                            <input type="checkbox" class="select-all" value="on" name="selectAll" title="{t}Отметить элементы на всех страницах{/t}">
                        </div>
                    </div>
                </th>

                {$columns=[
                    'task_num'            => [ 'sort' => true, 'name' => "{t}Номер{/t}"],
                    'title'               => [ 'sort' => true, 'name' => "{t}Суть{/t}" ],
                    'date_of_create'      => [ 'sort' => true, 'name' => "{t}Создано{/t}" ],
                    'status_id'           => [ 'sort' => true, 'name' => "{t}Статус{/t}"],
                    'date_of_planned_end' => [ 'sort' => true, 'name' => "{t}План завершения{/t}"],
                    'creator_user_id'     => [ 'sort' => true, 'name' => "{t}Создатель{/t}"],
                    'implementer_user_id' => [ 'sort' => true, 'name' => "{t}Исполнитель{/t}"]
                ]}
                {foreach $columns as $key => $column}
                    <th>
                        {if $column.sort}
                            <a data-url="{$this_controller->makeUrl(['sort' => {$key}, 'nsort' => $default_n_sort[$key]])}" class="refresh sortable {if $cur_sort == $key}{$cur_n_sort}{/if}">{$column.name}</a>
                        {else}
                            {$column.name}
                        {/if}
                    </th>
                {/foreach}

                <th class="actions"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $tasks as $task}
                <tr data-id="{$task.id}">
                    <td class="chk"><input type="checkbox" name="task[]" value="{$task.id}"></td>
                    <td><a class="task-edit" data-url="{adminUrl do=edit id={$task.id} mod_controller="crm-taskctrl"}">{$task.task_num}</a></td>
                    <td>{$task.title}</td>
                    <td>{$task.date_of_create|dateformat:"@date @time:@sec"}</td>
                    <td>
                        {$status=$task->getStatus()}
                        <span class="orderStatusColor" style="background-color:{$status.color}"></span>&nbsp;{$status.title}
                    </td>
                    <td>
                        <span class="c-{$task->getPlannedEndStatus()}" title="{$task->getPlannedEndStatusTitle()}">{$task.date_of_planned_end}</span>
                    </td>
                    <td>
                        {$user=$task->getCreatorUser()}
                        {if $user->id > 0}
                            {if $current_user.id == $user->id}{t}Вы, {/t}{/if}
                            {$user->getFio()} ({$user->id})
                        {else}
                            {t}Не назначен{/t}
                        {/if}
                    </td>
                    <td>
                        {$user=$task->getImplementerUser()}
                        {if $user->id > 0}
                            {if $current_user.id == $user->id}{t}Вы, {/t}{/if}
                            {$user->getFio()} ({$user->id})
                        {else}
                            {t}Не назначен{/t}
                        {/if}
                    </td>
                    <td class="actions">
                        <div class="inline-tools">
                            <a data-url="{adminUrl do=edit id={$task.id} mod_controller="crm-taskctrl"}" class="tool task-edit" title="{t}Редактировать{/t}"><i class="zmdi zmdi-edit"></i></a>
                            <a class="tool task-del" title="{t}удалить{/t}"><i class="zmdi zmdi-delete c-red"></i></a>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="9">{t}Пока нет ни одной задачи{/t}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>

    <div class="tools-bottom">
        <div class="paginator virtual-form" data-action="{$this_controller->makeUrl(['task_page' => null, 'task_page_size' => null])}">
            {$paginator->getView(['is_virtual' => true])}
        </div>
    </div>

    <div class="group-toolbar">
        <span class="checked-offers">{t}Отмеченные<br> значения{/t}:</span>
        <a class="btn btn-danger delete">{t}Удалить{/t}</a>
    </div>
</div>

<script type=" text/javascript">
    $.allReady(function() {
        $('.crm-block-task').blockCrm({
            addLink: '.add-task',
            editButton: '.task-edit',
            delButton: '.task-del',
            checkboxName: 'task',
            counterElement: '.counter.crm-task'
        });
    });
</script>