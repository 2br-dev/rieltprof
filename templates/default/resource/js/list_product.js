/**
* Скрипт инициализирует необходимые функции на странице со списком товаров
*/
$(window).load(function() {
    $('body').on('mouseover', '.productList .photoView', function() {
        if (!$(this).data('gallery')) {
    
            $('.gallery [data-change-preview]', this).mouseenter(function() {
                $(this).addClass('act').siblings().removeClass('act');
                $(this).closest('.photoView').find('.middlePreview').attr('src', $(this).data('changePreview') );
                return false;
            });            

            $('.productList .photoView').mouseleave(function() {
                $('.gallery [data-change-preview]:first', this).trigger('mouseenter');
            });            
            
            $('.scrollable .gallery', this).jcarousel({
                vertical: true
            });
            $(window).unbind('resize.jcarousel');            
            
            $('.scrollable .control', this).on({
                'inactive.jcarouselcontrol': function() {
                    $(this).addClass('disabled');
                },
                'active.jcarouselcontrol': function() {
                    $(this).removeClass('disabled');
                }
            });            

            $('.scrollable .control.up', this).jcarouselControl({
                target: '-=3'
            });
            $('.scrollable .control.down', this).jcarouselControl({
                target: '+=3'
            });
            $(this).data('gallery', true);
        }
    });
});