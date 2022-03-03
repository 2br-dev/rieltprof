/**
 * Скрипт обеспечивает работу страницы авторизации в административной панели
 *
 * @author ReadyScript lab.
 */
(function($) {

    $(function() {
        var errorMessage = $('.error-message');
        var successMessage = $('.success-message');
        var loading = $('.rs-loading');

        $('#auth .to-recover').click(function() {
            $('#auth').fadeOut();
            $('#recover').fadeIn();
            errorMessage.empty();
            successMessage.empty();
            return false;
        });

        $('#recover .back-to-auth').click(function() {
            $('#auth').fadeIn();
            $('#recover').fadeOut();
            errorMessage.empty();
            successMessage.empty();
            return false;
        });


        $('#auth, #recover').submit(function() {
            var $form = $(this);
            loading.show();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serializeArray(),
                success: function(response) {
                    errorMessage.empty();
                    $form.removeClass('form-has-error');

                    if (response.success) {
                        if (response.successText) {
                            successMessage.text(response.successText);
                        }

                        if ($form.attr('id') == 'auth') {
                            loading.show();
                            location.reload();
                            return;
                        }
                    } else {
                        errorMessage.text(response.error);
                        $form.addClass('form-has-error');
                    }
                    loading.hide();
                },
                error: function() {
                    loading.hide();
                }
            });

            return false;
        });
    });

})(jQuery);