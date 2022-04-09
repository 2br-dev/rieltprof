/**
* Отображает диалог правильный ли город
*/
$(function() {
    function openSubscribeWindow() {
        $.openDialog({
          url : global.current_city_dialog_url
        }); 
    }
    
    setTimeout(openSubscribeWindow, 500); 
});