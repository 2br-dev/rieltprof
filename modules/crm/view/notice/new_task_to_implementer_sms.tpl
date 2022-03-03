{t title=$data->task->title}Новая задача `%title`.{/t}
{if $data->task->date_of_planned_end}{t date="{$data->task->date_of_planned_end|dateformat:"@date @time"}"}Выполнить до %date{/t}{/if}