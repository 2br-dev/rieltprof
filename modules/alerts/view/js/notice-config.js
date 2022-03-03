$(function() {
    $('.edit-tpl').click(function() {
        var context = $(this).closest('tr');
        var title = $(this).closest('tr').find('.title').html(),
            email_inp = $('.tpl-email', context),
            sms_inp = $('.tpl-sms', context),
            desktop_inp = $('.tpl-desktop', context);
            addemail_inp = $('.add-recipients', context);
        
        $('#tpl-email').prop('disabled', email_inp.is(':disabled')).val(email_inp.val());
        $('#tpl-sms').prop('disabled', sms_inp.is(':disabled')).val(sms_inp.val());
        $('#tpl-desktop').prop('disabled', desktop_inp.is(':disabled')).val(desktop_inp.val());
        $('#add-recipients').val(addemail_inp.val());
        
        $('#notice-tpl-dialog').dialog({
            modal: true,
            width:700,
            resizable: false,
            title: lang.t('Редактирование шаблона уведомления &laquo;%title&raquo;', { title: title}),
            buttons: [
            {
                text: lang.t('Сбросить'),
                class:'btn btn-default',
                click: function() {
                    $('#tpl-email').val(email_inp.data('default'));
                    $('#tpl-sms').val(sms_inp.data('default'));
                    $('#tpl-desktop').val(desktop_inp.data('default'));
                }
            },
            {
                text: lang.t('Сохранить'),
                class:'btn btn-success',
                click: function() {
                    email_inp.val($('#tpl-email').val());
                    sms_inp.val($('#tpl-sms').val());
                    desktop_inp.val($('#tpl-desktop').val());
                    addemail_inp.val($('#add-recipients').val());
                    
                    $('#notice-tpl-dialog').dialog('close');
                }
            }]
        });
    });

    $('#tpl-email, #tpl-sms, #tpl-desktop').selectTemplate({
        dialogUrl: $('#notice-tpl-dialog').data('dialog-url'),
        handler: '.selectTemplate'
    })
});