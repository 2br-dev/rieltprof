var initOrderStatusesWidget = function(data) {

    (function( $ ){
        $.plot('#orderStatusesGraph', data, {
            series: {
                pie: {
                    innerRadius: 0.5,
                    show: true,
                }
            },
            legend: {
                show: false
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            legend: {
                container: '.flc-orderStatusesLegend',
                backgroundOpacity: 0.5,
                noColumns: 2,
                backgroundColor: "white",
                lineWidth: 0
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false,
                cssClass: 'chart-tooltip'
            }
        });
    })(jQuery);
};