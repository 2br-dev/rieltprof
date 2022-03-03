/**
* Плагин отвечает за запрос статуса выписывания чека
*/

var qwait;
(function( $ ){
    $.fn.receiptSuccessRequestStatus = function( method ) {
        var defaults = {
            waitReceiptSuccessImg : '#rs-waitReceiptSuccessImg', //Селектор картинки с успешным сообщением
            waitReceiptLoading : '#rs-waitReceiptLoading', //Селектор картинки загрузки
            waitReceiptStatus : '#rs-waitReceiptStatus', //Селектор текста статуса ожидания
            timeout: 2000
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('receiptSuccessRequestStatus');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('receiptSuccessRequestStatus', data);
                    data.options = $.extend({}, defaults, initoptions);
                    
                    qwait = setTimeout(requestStatus, data.options.timeout);
                }
            };
            
            
            //private 
            //Комплектации
            /**
            * Смена комплектации
            * 
            */
            var requestStatus = function() { 
                $.ajax({
                    type: 'GET',
                    url: global.receipt_check_url,
                    dataType: 'json',
                    success: function (response){
                        clearTimeout(qwait);
                        if (response.success){ //Если статус успешно получен
                            $(data.options.waitReceiptSuccessImg).show();
                            $(data.options.waitReceiptLoading).hide();
                            if (!response.error){
                               $(data.options.waitReceiptStatus).hide(); 
                            }    
                            if (response.error){  //Если произошла ошибка
                                $(data.options.waitReceiptStatus).show();
                                $(data.options.waitReceiptStatus).addClass('error');
                                $(data.options.waitReceiptStatus).html(" " + response.error);
                            }    
                        }else{
                            qwait = setTimeout(requestStatus, data.options.timeout);
                        }         
                    }
                });
            };

            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

})( jQuery );


$(function() {
    $('body').receiptSuccessRequestStatus();
});