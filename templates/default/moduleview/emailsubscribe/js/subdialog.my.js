/**
* Отображает диалог подписки на новости
*/
$(function() {
   var emailSubscribeWindow; //Хранилище для функции окна подписки

    /**
     * Открывает окно подписки
     */
   function openSubscribeWindow() {
        clearTimeout(emailSubscribeWindow);
        //Смотрим не открыто ли другое окно уже
        if (!$(".mfp-content").length && !$('#cboxLoadedContent').length){
            $.openDialog({
                url: global.emailsubscribe_dialog_url
            });
        }else { //Подождём ещё немного
            emailSubscribeWindow = setTimeout(openSubscribeWindow, global.emailsubscribe_dialog_open_delay * 1000);
        }
   }
   
   if (global.emailsubscribe_dialog_open_delay){ //Если опция настроена и включена
       emailSubscribeWindow = setTimeout(openSubscribeWindow, global.emailsubscribe_dialog_open_delay * 1000);
   }
});