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
            {foreach from=$values item=data}
                <tr>
                    <td> 
                       {$data.title}
                    </td>
                    <td class="feedback_result_value"> 
                       {$data.current_val}
                    </td>
                </tr>
            {/foreach}
        {/if}
    </table>
{else}
    {t}Не указано{/t}
{/if}