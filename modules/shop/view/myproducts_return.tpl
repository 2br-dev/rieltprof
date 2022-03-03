{addcss file="%shop%/returns.css"}
{addjs file="%shop%/returns.js"}

{if $order_list}
    <div class="page-responses form-style">
        <h2 class="h2"><span>{t}Мои возвраты{/t}</span></h2>
            <form action="{urlmake Act="add"}" method="GET">
                <div class="form-group textCenter">
                    <label class="label-sup">{t}Ваши заказы{/t}</label>
                    <select name="order_id">
                        {foreach $order_list as $order}
                            <option value="{$order.order_num}">{$order.order_num} от {$order.dateof|date_format:"d.m.Y"}</option>
                        {/foreach}
                    </select>
                    <input class="formSave" type="submit" value="{t}Создать возврат{/t}"/>
                </div>
            </form> 
    </div>

    {if $returns_list}
        <div class="page-responses form-style">
            <h2 class="h2"><span>{t}Ваши заявки{/t}</span></h2>
            <div class="form-group">
                    <table class="table returnsTable">
                        <tbody>
                        <tr class="returnhead">
                            <th>{t}Заявка{/t}</th>
                            <th class="mobileHide">{t}Даты{/t}</th>
                            <th class="mobileHide">{t}Сумма возврата{/t}</th>
                            <th>{t}Заявление{/t}</th>
                        </tr>
                        {foreach $returns_list as $return}
                            {$order=$return->getOrder()}
                            <tr>
                                <td class="status">
                                    №:
                                    {if $return.status == 'new'}<a href="{urlmake Act="edit" return_id=$return.return_num}">{$return.return_num}</a>{else}{$return.return_num}{/if}<br>
                                    {t}Статус{/t}:
                                    {$return.__status->textView()}<br/>
                                    {if $order.order_num}
                                        {t}Заказ №{/t}:
                                        {$order.order_num}
                                    {/if}
                                </td>
                                <td class="mobileHide">
                                    <p>{t}Оформление заявки{/t}:</p>
                                    <p>{$return.dateof|date_format:"d.m.Y"}</p>
                                    {if $return.date_exec}
                                        <p>{t}Выполнение заявки{/t}:</p>
                                        <p>{$return.date_exec|date_format:"d.m.Y"}</p>
                                    {/if}
                                </td>
                                <td class="summ mobileHide">{$return.cost_total|format_price} {$return.currency_stitle}</td>
                                <td><a href="{urlmake Act="print" return_id=$return.return_num}" target="_blank">{t}Распечатать{/t}</a></td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
            </div>
        </div>
    {/if}    

    <script type="text/javascript">
        $(document).ready(function() {
            $('[name="order_id"]').styler();
        });
    </script>    
{else}
    <div class="noData">
        {t}Нет заказов для возврата товаров{/t}
    </div>
{/if}