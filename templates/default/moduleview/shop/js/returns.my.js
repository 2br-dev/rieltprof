/**
 * Подсчитывает итог возврата
 *
 */
function calculateReturnTotal() {
    var total = 0;
    $(".productsReturnCheckbox:checked").each(function () {
        var price  = $(this).data('price');
        var amount = $("#amount" + $(this).data('uniq')).val();
        total += (price*amount);
    });
    $("#returnTotal").html(total);
}

$(document).ready(function(){
    /**
     * Включение товара для возврата
     */
    $(".productsReturnCheckbox").on('change', function(){
        var tr = $(this).closest('tr');
        if ($(this).prop('checked')){
            $("input[type='hidden'], select", tr).prop('disabled', false);
            $(".jqselect", tr).removeClass('disabled');
        }else{
            $("input[type='hidden'], select", tr).prop('disabled', true);
            $(".jqselect", tr).addClass('disabled');
        }
        calculateReturnTotal();
    });

    /**
     * Смена количества
     */
    $(".productsReturnAmount").on('change', function () {
        calculateReturnTotal();
    });

    /**
     * Отправка формы
     */
    $(".returnsSubmit").on('click', function(){
        $(this).closest('form').submit();
        return false;
    });
});
