<form method="post" action="{adminurl do='interfaceDeliveryOrderAction' action='call_courier_create' order_id=$order.id delivery_order_id=$delivery_order.id}" class="crud-form">
    <table class="otable">
        <tr>
            <td class="otitle">{t}Дата ожидания курьера{/t}</td>
            <td>
                <input type="date" name="date">
                <div class="fieldhelp">
                    {t}Заявка, созданная на текущую дату после 15:00 по времени отправителя, может быть выполнена на следующий день{/t}
                </div>
            </td>
        </tr>
        <tr>
            <td class="otitle">{t}Время ожидания курьера{/t}</td>
            <td>
                <input type="time" name="time_from"> - <input type="time" name="time_to">
            </td>
        </tr>
        <tr>
            <td class="otitle">{t}Время обеденного перерыва{/t}</td>
            <td>
                <input type="time" name="lunch_time_from"> - <input type="time" name="lunch_time_to">
            </td>
        </tr>
        <tr>
            <td class="otitle">{t}Необходим прозвон отправителя{/t}</td>
            <td>
                <input type="checkbox" name="need_call" value="1">
            </td>
        </tr>
        <tr>
            <td class="otitle">{t}Комментарий{/t}</td>
            <td>
                <textarea name="comment"></textarea>
            </td>
        </tr>
    </table>
</form>
