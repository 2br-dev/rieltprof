$(function(){
    // Сохраянем URL ифрейма при переходах
    var iframe = $("iframe#frame");

    // Обработчик сообщения, посылаемого из IFrame
    window.addEventListener("message", function(ev){
        // Если Iframe сообщил о смене адреса
        if(ev.data.url){
            // Сохраняем полученный URL после решетки
            document.location.hash = ev.data.url;
        }
    }, false);

    // Восстанавливаем URL ифрейма из hash (при обновлении страницы)
    if(document.location.hash){
        var url_with_proxy = iframe.data('proxyUrl');
        url_with_proxy += '?url='+encodeURIComponent(document.location.hash.substr(1));
        iframe.attr('src', url_with_proxy);
    }

    iframe.load(function() {
        $('#mp-loader').hide();
    });
});