/* Скрипт, необходимый для корректной работы страницы регистрации и профиля */

$(function() {
    //Инициализируем отображение полей при установке флажка "Сменить пароль"
    $('body').on('change', '#pass-accept', function() {
        $('.form-fields_change-pass', $(this).closest('form')).toggleClass('hidden', !this.checked);
    })

    //Инициализируем отображение соответствующих полей при смене типа профиля
    $('body').on('change', '#is_company_no, #is_company_yes', function() {
        $('.form-fields_company', $(this).closest('form')).toggleClass('hidden', $('#is_company_no').is(':checked'));
    });
});