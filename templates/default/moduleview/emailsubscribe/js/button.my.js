/**
* Подписка на рассылку
*/
$(function() {
    $("body").on('submit', '#signUpUpdate form', function(){
        var $_this = $("#signUpUpdate");
        var data   = $(this).serialize();
        $.ajax({
            type : 'POST',
            url : $(this).attr('action'),
            data : data,
            dataType : 'json',
            success : function(response){
                $_this.replaceWith(response.html);
            }
        });
        return false;
    });
});