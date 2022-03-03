$.contentReady(function() {
    //Подсвечивает поля, для выбранного устройства
    var device = $('.device-selector li.act').data('device'); 
    $('.bootstrap-multi-values td.'+device).addClass('act');
});