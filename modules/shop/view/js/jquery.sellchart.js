/**
* Plugin, активирующий виджет Динамика продаж
*/
$.widget('rs.rsSellChart', {
    options: {
        yearFilter: '.year-filter',
        yearCheckbox: '.year-filter input',
        yearCheckboxLabel: '.year-filter label',
        placeholder: '.placeholder',
        yearPlotOptions: {
            xaxis: {
                minTickSize: [1, "month"],
            }
        },
        monthPlotOptions: {
            xaxis: {
                minTickSize: [1, "day"],
            }
        },
        plotOptions: {
            xaxis: {
                mode: 'time',
                monthNames: lang.t("янв,фев,мар,апр,май,июн,июл,авг,сен,окт,ноя,дек").split(',')
            },
            yaxis: {
                tickDecimals:0
            },
            lines: { show: true },
            points: { show: true },
            legend: {
                show: true,
                noColumns: 1, // number of colums in legend table
                margin: 5, // distance from grid edge to default legend container within plot
                backgroundColor: '#fff', // null means auto-detect
                backgroundOpacity: 0.85 // set to 0 to avoid background
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                borderColor: '#e5e5e5'
            },
            hooks: {
                processRawData: function(plot, series, data, datapoints) {
                    var seriesData = [];
                    $.each(data, function(key, val) {
                        seriesData.push([val.x, val.y]);
                    });

                    series.originalData = $.extend({}, data);
                    series.data = seriesData;
                }

            }
        }
    },

    _create: function() {
        var _this = this;
        this.chart = $(this.options.placeholder, this.element);

        this.element
            .on('change', this.options.yearCheckbox, function() {
                _this.build();
            })
            .on('click', this.options.yearFilter, function(e) {e.stopPropagation();});

        this.build();
        this.chart.on("plothover", function(event, pos, item) {
            _this._plotHover(event, pos, item);
        });
    },

    build: function() {
        var _this = this,
            dataset = [],
            yearList = $(this.options.yearCheckbox + ':checked', this.element);

        if (yearList.length) {
            yearList.each(function() {
                var key = $(this).val();
                if (key && _this.chart.data('inlineData').points[key])
                    dataset.push(_this.chart.data('inlineData').points[key]);
            });
        } else {
            dataset = this.chart.data('inlineData').points;
        }

        if (dataset.length > 0) {
            $.plot(this.chart, dataset, $.extend(true, this.options.plotOptions, this.options[this.chart.data('inlineData').range+'PlotOptions']));
        }
    },

    _plotHover: function(event, pos, item) {
        if (item) {
            if (this.previousPoint != item.dataIndex) {
                this.previousPoint = item.dataIndex;
                var
                    pointData = item.series.originalData[item.dataIndex],
                    dateStr = this[('_' + this.chart.data('inlineData').range + 'Format')].call(this, pointData);

                var tooltipText = lang.t('Заказов ')+dateStr+': <strong>'+pointData.count+'</strong> <br\> ' + lang.t('На сумму') + ': <strong>'+this.numberFormat(pointData.total_cost,2,',',' ')+' '+this.chart.data('inlineData').currency+'</strong>';
                this._showTooltip(item.pageX, item.pageY, tooltipText);
            }
        }
        else {
            $("#sellChartTooltip").remove();
            this.previousPoint = null;
        }
    },

    _showTooltip: function(x, y, contents) {
        $("#sellChartTooltip").remove();
        $('<div id="sellChartTooltip" class="chart-tooltip"/>').html(contents).css( {
            top: y + 10,
            left: x + 10
        }).appendTo("body").fadeIn(200);
    },

    _yearFormat: function(pointData) {
        var
            months = lang.t("январе,феврале,марте,апреле,мае,июне,июле,августе,сентябре,октябре,ноябре,декабре").split(','),
            pointDate = new Date(pointData.pointDate);

        return lang.t('в %date', {date: months[pointDate.getMonth()] + ' ' + pointDate.getFullYear()});
    },

    _monthFormat: function(pointData) {
        var
            months = lang.t("января,февраля,марта,апреля, мая,июня,июля,августа,сентября,октября,ноября,декабря").split(','),
            pointDate = new Date(pointData.x);

        return pointDate.getDate()+' '+months[pointDate.getMonth()]+' '+pointDate.getFullYear();
    },

    numberFormat: function(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };

            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
                .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }
});