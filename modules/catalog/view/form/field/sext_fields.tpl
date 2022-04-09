{assign var=values value=$elem->tableDataUnserialized('sext_fields')}

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
        {* Сведения о товаре *}
        {if !empty($values)}
            {foreach $values as $key => $data}
                <tr>
                    <td> 
                       {$data.title}
                    </td>
                    <td class="feedback_result_value">
                        <input type="hidden" name="clickfields[{$key}]" value="{if $data.type == 'bool'}{if $data.current_val == 'Да'}1{else}0{/if}{else}{$data.current_val}{/if}">
                       {$data.current_val}
                    </td>
                </tr>
            {/foreach}
        {/if}
    </table>
{else}
    {t}Не указано{/t}
{/if}