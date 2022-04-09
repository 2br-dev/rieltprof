/**
 * Скрипт активирует контрол - выбор значения по картинке
 */
$.contentReady(function() {
    $('.admin-style .image-select > .items').each(function() {
        var owl = $(this).owlCarousel({
            center: true,
            items:1,
            margin:10,
            nav:true,
            navText:['<i class="zmdi zmdi-chevron-left"></i>', '<i class="zmdi zmdi-chevron-right"></i>'],
            dots: true,
            startPosition: $('.item[data-active]', this).index()
        });

        owl.on('changed.owl.carousel', function(event) {
            let value = $('.item:eq('+event.item.index+')',  event.target).data('key');
            $(event.target).closest('.image-select').find('input[type="hidden"]').val(value);
        });
    });
});