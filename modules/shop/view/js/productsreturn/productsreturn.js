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

$.contentReady(function(){
    /**
     * Включение товара для возврата
     */
    $(".productsReturnCheckbox").on('change', function(){
        var tr = $(this).closest('tr');
        if ($(this).prop('checked')){
            $("input[type='hidden'], select", tr).prop('disabled', false);
        }else{
            $("input[type='hidden'], select", tr).prop('disabled', true);
        }
        calculateReturnTotal();
    });

    /**
     * Смена количества
     */
    $(".productsReturnAmount").on('change', function () {
        calculateReturnTotal();
    });
});
