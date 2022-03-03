/* Скрипт инициализирует страницу просмотра параметров подписки на мобильное приложение */
$(function() {
    $('.mobile-site-app.view .phoneContent').mCustomScrollbar({
        theme: 'minimal-dark',
        autoHideScrollbar:true,
        scrollInertia: 0,
        mouseWheel:{ preventDefault: true }
    });
});