/**
 * Скрипт инициализирует работу страницы диаграммы Ганта
 */
$( function() {
    let head, leftPart, leftHead;
    let load = function() {
        head = $('.right-part .g-head');
        leftPart = $('.gant .left-part');
        leftHead = $('.left-part .g-head');

        /**
         * Инициализация селектора дат
         */
        $('.g-top-line input[datefilter]', this).dateselector();
    };

    load();
    $.contentReady(load);

    $('body')
        .on('mouseenter',  '.g-chart-wrapper',function(event) {
            $(this).addClass('active');
            let rowId = $(this).data('rowId');
            $(' > [data-row-id="' + rowId + '"]', leftPart).addClass('highlight');

        })
        .on('mouseleave', '.g-chart-wrapper', function(event) {
            $(this).removeClass('active');
            let rowId = $(this).data('rowId');
            $(' > [data-row-id="' + rowId + '"]', leftPart).removeClass('highlight');
        })
        .on('mousemove',  '.g-chart-wrapper a',function(event) {
            $("i", this)
                .css({top: event.offsetY, left: event.offsetX })
                .tooltip('show');
        })
        .on('mouseleave', '.g-chart-wrapper a', function(event) {
            $("i", this).tooltip('hide');
        });

    $(window).on('scroll', function(event) {
            let headOffset = leftHead.offset().top - 62;
            if (scrollY > headOffset) {
                head.css('top', (scrollY - headOffset) + 'px');
            } else {
                head.css('top', 0);
            }
        });
});