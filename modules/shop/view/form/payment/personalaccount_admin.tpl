{if $order.totalcost>0 && !$order.is_payed}
    <p>
        <a data-href="{adminUrl do=orderQuery type="payment" order_id=$order.id operation="orderpay"}" id="pay-from-personal-account" class="f-12 u-link">
            {t}Оплатить заказ с лицевого счета{/t}
        </a>
    </p>

    <script>
        $(function() {
            $('#pay-from-personal-account').click(function() {
                if ($('#orderForm').data('hasChanges')) {
                    $.messenger(lang.t('В составе заказа произошли изменения. Для оплаты необходимо сохранить заказ.'), { theme:'error' });
                } else {
                    if (confirm(lang.t('Вы действительно желаете оплатить заказ с лицевого счета пользователя'))) {
                        $.ajaxQuery({
                            url: $(this).data('href'),
                            success: function(response) {
                                if (response.success) {
                                    $('input[name="is_payed"]').prop('checked', true);
                                }

                                $.orderEdit('refresh');
                            }
                        });
                    } else {
                        return false;
                    }
                }
            });
        });
    </script>
{/if}