$(function() {
    $('.performance-report .toggle-sql').click(function() {
        $(this).closest('tbody').toggleClass('show-sql');
    });

    $('.performance-report .toggle-stack-trace').click(function() {
        $(this).closest('.sql').toggleClass('show-stack-trace');
    });

    if ($.fn.plot) {
        $.plot('#performance-chart', global.performance_plot_data, {
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
                content: "%p.1%, %s", // show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false,
                cssClass: 'chart-tooltip'
            }
        });
    }
});