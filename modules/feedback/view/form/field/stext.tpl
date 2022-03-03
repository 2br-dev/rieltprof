{$values=$elem->tableDataUnserialized()}

{if !empty($values)} 
    <table class="feedback_result_form">
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
                    <td class="feedback_result_value">
                        {if $data.field.show_type=='file'}
                            {if empty($value)}
                                {t}Файл не загружен{/t}
                            {else}
                                <a href="{$value.real_file_name}">{t}Ссылка на файл{/t}</a>
                            {/if}
                        {elseif $data.field.show_type == 'list'}
                            {if is_array($value)}
                                {implode(', ', $value)}
                            {else}
                                {$value}
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