<select id="refundsSelectOrder" name="order_num" class="select-order" disabled style="display: none"></select>
<input id="refundsOrderEnter" name="order_num" class="order-enter" type="text" placeholder="{t}Введите номер заказа{/t}">

<script type="text/javascript">
    $.contentReady(function(){

        var refunds_select  = $("#refundsSelectOrder"); //Выпадающий список
        var refunds_context = refunds_select.closest('table'); //Текущий контекст

        /**
         * Срабатывает на смене пользователя
         * Возвращает массив заказов принадлежащих пользователю и вставляет в нужное место
         */
        $(refunds_context).on('change', '[type="hidden"][name="user_id"]', function() {
            $.rs.loading.show();
            $.ajax({
                url: '{$router->getAdminUrl('ajaxUsersOrder')}',
                method: 'POST',
                data: {
                    user_id : $(this).val() //id пользователя
                },
                dataType: 'json',
                success: function(data){
                    $.rs.loading.hide();
                    $.each(data['orders'], function (index , val) {
                        refunds_select.append($("<option value='" + val.order_num + "'>" + val.order_num + " от " + val.date + "</option>"));
                    });
                    $("#refundsOrderEnter").hide().prop('disabled', true);
                    refunds_select.show().prop('disabled', false);
                }
            });
        });

        /**
         * Если пользователя убрали из строки ввода
         */
        $(refunds_context).on('remove-object', '[data-name="user_id"]', function() {
            $("#refundsOrderEnter").show().prop('disabled', false);
            refunds_select.hide().prop('disabled', true);
        });
    });
</script>