{addcss file="%shop%/returns.css"}
{addjs file="%shop%/returns.js"}
<div class="page-responses form-style">
    <h2 class="h2">{t}Мои возвраты{/t}</h2>

    {if !empty($order_list)}
        <form action="{urlmake Act="add"}" method="GET">
            <div class="form-group">
                <label class="label-sup">{t}Ваши заказы{/t}</label>
                <select name="order_id">
                    {foreach $order_list as $order}
                        <option value="{$order.order_num}">{$order.order_num} от {$order.dateof|date_format:"d.m.Y"}</option>
                    {/foreach}
                </select>
                <input type="submit" value="{t}Создать возврат{/t}"/>
            </div>
        </form>
    {else}
        <h6>Нет заявок</h6>
    {/if}
</div>

{if !empty($order_list)}
    <div class="page-responses form-style">
        <h2 class="h2">{t}Ваши заявки{/t}</h2>
        <div class="form-group">
            {if !empty($returns_list)}
                <table class="table">
                    <tbody>
                    <tr>
                        <th>{t}Заявка{/t}</th>
                        <th class="hidden-xs">{t}Даты{/t}</th>
                        <th class="hidden-xs">{t}Сумма возврата{/t}</th>
                        <th>{t}Заявление{/t}</th>
                    </tr>
                    {foreach $returns_list as $return}
                        {$order=$return->getOrder()}
                        <tr>
                            <td>
                                №:
                                {if $return.status == 'new'}<a href="{urlmake Act="edit" return_id=$return.return_num}">{$return.return_num}</a>{else}{$return.return_num}{/if}<br>
                                {t}Статус{/t}:
                                {$return.__status->textView()}<br/>
                                {if $order.order_num}
                                    {t}Заказ №{/t}:
                                    {$order.order_num}
                                {/if}
                            </td>
                            <td class="hidden-xs">
                                <p>{t}Оформление заявки{/t}:</p>
                                <p>{$return.dateof|date_format:"d.m.Y"}</p>
                                {if $return.date_exec}
                                    <p>{t}Выполнение заявки{/t}:</p>
                                    <p>{$return.date_exec|date_format:"d.m.Y"}</p>
                                {/if}
                            </td>
                            <td class="hidden-xs">{$return.cost_total|format_price} {$return.currency_stitle}</td>
                            <td><a href="{urlmake Act="print" return_id=$return.return_num}" target="_blank">{t}Распечатать{/t}</a></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            {else}
                <p>{t}Нет заявок{/t}</p>
            {/if}
        </div>
    </div>
{/if}
