//Инициализируем работу меню в админ. панели
$(window).load(function() {
    //Добавляем scrollbar'ы в меню
    $('.side-scroll').mCustomScrollbar({
        theme: 'minimal',
        scrollInertia: 0,
        mouseWheel:{ preventDefault: true }
    });

    $('.sm-body').mCustomScrollbar({
        theme: 'minimal-dark',
        autoHideScrollbar:true,
        scrollInertia: 0,
        mouseWheel:{ preventDefault: true }
    });

    $('body')
        .on($.rs.clickEventName, '#menu-trigger', function(e) {
            $(this).toggleClass('toggled');
            $('#sidebar').toggleClass('toggled');
            e.preventDefault();
        })
        .on($.rs.clickEventName, '.sm .sm-node > a', function(e) {
            $(this).parent().toggleClass('open');
            e.preventDefault();
        })
        .on($.rs.clickEventName, '.menu-close', function(e) {
            var self = this;
            $(this).closest('.sm-node').removeClass('open');
            $(this).closest('#sidebar').removeClass('sm-opened');
        });


        $('.side-menu > .sm-node > a')
            .on($.rs.clickEventName, function(e) {
                var parent = $(this).closest('.sm-node');
                var sidebar = $(this).closest('#sidebar');

                if (parent.is('.open')) {
                    parent.removeClass('open');
                    sidebar.removeClass('sm-opened');
                } else {
                    sidebar.find('.side-menu > .sm-node').removeClass('open');
                    parent.addClass('open');
                    sidebar.addClass('sm-opened');
                }
                e.preventDefault();
            })
            .on('dblclick', function() {
                if ($(this).data('url')) {
                    location.href = $(this).data('url');
                }
            });

        $('.side-menu-overlay').on($.rs.clickEventName, function() {
            $('.side-menu .sm-node').removeClass('open');
            $(this).closest('#sidebar').removeClass('sm-opened');
        });
});