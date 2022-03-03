{if $order.user_id}
    {$order_status = $order->getStatus()}
    {if ($order_status.type == 'waitforpay' || $order_status.copy_type == 'waitforpay')}
        <a href="{adminUrl do='interfaceRecurringPaymentsAction' order_id=$order.id action='saved_methods_form'}" class="btn btn-primary btn-alt crud-edit crud-sm-dialog rs-order-check-сhanges">
            {t}Выбрать способ платежа{/t}
        </a>
    {/if}
    {if ($order_status.type == 'payment_method_selected' || $order_status.copy_type == 'payment_method_selected')}
        {$saved_payment_method = $order->getSavedPaymentMethod()}
        <table class="otable">
            <tr>
                <td class="otitle">{t}Выбранный способ платежа{/t}</td>
                <td>{$saved_payment_method->getType()} {$saved_payment_method.subtype} {$saved_payment_method.title}</td>
            </tr>
        </table>
        <a href="{adminUrl do='interfaceRecurringPaymentsAction' order_id=$order.id action='pay_with_saved_method'}" class="btn btn-success btn-alt crud-get rs-order-check-сhanges" data-confirm-text="{t}Вы действительно хотите оплатить заказ?{/t}">
            {t}Оплатить заказ выбранным способом{/t}
        </a>
        <a href="{adminUrl do='interfaceRecurringPaymentsAction' order_id=$order.id action='saved_methods_form'}" class="btn btn-primary btn-alt crud-edit crud-sm-dialog rs-order-check-сhanges">
            {t}Изменить способ платежа{/t}
        </a>
    {/if}
{else}
    <div class="text-danger">{t}Рекуррентная оплата возможна только для авторизованных пользователей{/t}</div>
{/if}
