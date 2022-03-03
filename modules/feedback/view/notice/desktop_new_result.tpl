<h1>Сообщение №{$data->result_item->id} от {$data->result_item->dateof|dateformat}</h1>

<p>Статус: {$data->result_item->__status->textView()}</p>
<p>IP пользователя: {$data->result_item->ip}</p>

<h2>Данные формы</h2>

{$values=$data->result_item->tableDataUnserialized()}
{if !empty($values)}
    <table class="table table-striped">
        <tr>
            <th>
                {t}Поле{/t}
            </th>
            <th>
                {t}Значение{/t}
            </th>
        </tr>
        {foreach from=$values item=data}
            {$field=$data.field}
            {$value=$data.value}
            {if $data.field.show_type != 'captcha'}
                <tr>
                    <td>
                       {$field.title}
                    </td>
                    <td>
                       {if $data.field.show_type=='file'}
                          {if empty($value)}
                             {t}Файл не загружен{/t}
                          {else}
                             <a href="{$value.real_file_name_absolute}">{t}Ссылка на файл{/t}</a>
                          {/if}
                       {else}
                          {$value}
                       {/if}
                    </td>
                </tr>
            {/if}
        {/foreach}
    </table>
{/if}