/*
* Плагин отвечает за работку фильтров в административной панели на сайте ReadyScript
*
* @author ReadyScript lab.
*/
(function($) {

    $.widget('rs.rsFilter', {
        options: {
            handler: '.openfilter',
            filterTitle: '.filter-title'
        },

        _create: function() {
            var self = this;
            this.element
                    .on('click', this.options.handler, function() {
                        self.open();
                        return false;
                    });
        },

        open:function() {
            var placementEl = this.element.closest('[data-filter-placement]');
            var position = placementEl.length ? placementEl.data('filterPlacement') : 'left';
            var form = $('.filter-form', this.element);
            var formPlaceholder = $('<div class="filter-form-placeholder">').insertAfter(form);

            form.sidePanel({
                title: this.element.find(this.options.filterTitle).text(),
                htmlHead: '<a class="rs-side-panel__head-link call-update" href="'+ form.data('cleanUrl') +'"><i class="zmdi zmdi-undo"></i>&nbsp;<span class="hidden-xs">'+lang.t('Очистить фильтр')+'</span></a>',
                position:position,
                onShow: function(e, data) {
                    data.element.on('submit', function() {
                        data.element.sidePanel('close');
                    });

                    data.panel.on('click', '.rs-side-panel__head-link', function() {

                        data.element.sidePanel('close');
                    });
                },
                onClose: function(e, data) {
                    //Возвращаем фильтр на место
                    formPlaceholder.replaceWith(data.element);
                    data.element.sidePanel('destroy');
                }
            });
        }
    });

    $.contentReady(function() {
        $('.filter').rsFilter();
    });

})(jQuery);