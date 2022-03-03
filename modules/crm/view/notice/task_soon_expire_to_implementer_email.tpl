{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <style type="text/css">
        .table {
            border-collapse:collapse;
            border:1px solid #aaa;
        }

        .table td {
            padding:3px;
            border:1px solid #aaa;
        }
    </style>

    {$task = $data->task}

    <h2>{t num=$task->task_num title=$task->title time=$data->remaining_time_str}Осталось %time до истечения срока выполнения задачи №%num (%title){/t}</h2>

    {include file="%crm%/notice/task_table.tpl" task=$task}
{/block}