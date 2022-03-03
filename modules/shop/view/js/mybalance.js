$(function() {
    $('.cost_field').on('keyup', function () {
        var base_cost = parseFloat($(this).val());
        if(!base_cost){
            base_cost = 0;
        }
        var ratio = $(".hidden_curr").data('ratio');
        var liter = $(".hidden_curr").data('liter');
        var new_cost = (base_cost / ratio).toFixed(2);
        $(".label_curr").html(new_cost.toString() + " " + liter);
    });
});
