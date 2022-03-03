/**
 * Плагин отвечает за запрос статуса выписывания чека
 */

var qwaitTransaction;
var qwaitReceipt;
(function( $ ){
    $.fn.onlinePayRequestStatus = function( method ) {
        var defaults = {
                onlinePayStatusParams: '#rs-status-params',
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

                    if ($(data.options.onlinePayStatusParams).hasClass('new')) {
                        qwaitTransaction = setTimeout(requestStatus, data.options.timeout);
                    }
                    if ($(data.options.onlinePayStatusParams).data('urlCheckReceipt')) {
                        qwaitReceipt = setTimeout(requestReceiptStatus, data.options.timeout);
                    }

                }
            };


            //private
            var requestStatus = function() {
                $.ajax({
                    type: 'GET',
                    url: $(data.options.onlinePayStatusParams).data('urlCheckTransaction'),
                    dataType: 'json',
                    success: function (response) {
                        clearTimeout(qwaitTransaction);
                        if (response.success) { //Если статус успешно получен
                            if (response.status == 'success' || response.status == 'fail' || response.status == 'hold') {
                                $(data.options.onlinePayStatusParams).removeClass('new').addClass(response.status);
                            } else {
                                qwaitTransaction = setTimeout(requestStatus, data.options.timeout);
                            }
                        } else {
                            qwaitTransaction = setTimeout(requestStatus, data.options.timeout);
                        }
                    }
                });
            };

            var requestReceiptStatus = function() {
                $.ajax({
                    type: 'GET',
                    url: $(data.options.onlinePayStatusParams).data('urlCheckReceipt'),
                    dataType: 'json',
                    success: function (response){
                        clearTimeout(qwaitReceipt);
                        if (response.success) { //Если статус успешно получен
                            $(data.options.waitReceiptSuccessImg).show();
                            $(data.options.waitReceiptLoading).hide();
                            if (!response.error) {
                                $(data.options.waitReceiptStatus).hide();
                            }
                            if (response.error) {  //Если произошла ошибка
                                $(data.options.waitReceiptStatus).show();
                                $(data.options.waitReceiptStatus).addClass('error');
                                $(data.options.waitReceiptStatus).html(" " + response.error);
                            }
                        } else {
                            qwaitReceipt = setTimeout(requestReceiptStatus, data.options.timeout);
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
    $('body').onlinePayRequestStatus();
});