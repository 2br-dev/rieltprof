/**
 * Plugin, активирующий отображение новостей в боковой панели
 */
$.widget("rs.newsShow", {
    options: {
        elementTitle: '.side-news__title',
        elementItem: '.side-news__item',
        elementAllViewed: '.all-viewed',
        elementNewClass: 'new',
        elementDisabledClass: 'disabled',
        elementViewCircle: '.view-circle'
    },
    _create: function() {
        var _this = this;
        this.element.on('click', function() {
            _this.open();
        });
    },

    /**
     * Открывает SideBar со списком новостей
     */
    open: function() {
        if ($.rs.loading.inProgress) return;

        var _this = this;
        this.panel = $('<div>').sidePanel({
            position: 'right',
            ajaxQuery: {
                url: this.element.data('urls').newsList
            },
            onLoad: function(e, data) {
                $(data.element)
                    .on('click', _this.options.elementItem, function(e) {
                        return _this._markAsViewed(e);
                    });
            },
            onShow: function(e, data) {
                $(data.panel)
                    .on('click', _this.options.elementAllViewed, function(e) {
                            return _this._markAllAsViewed(e, data.panel);
                    });
            }
        });
    },

    /**
     * Сообщает серверу о прочтении новости
     *
     * @private
     */
    _markAsViewed: function(e) {
        var item = $(e.target).closest(this.options.elementItem);
        if (item.hasClass(this.options.elementNewClass)) {
            $.ajaxQuery({
                url: this.element.data('urls').markAsViewed,
                data: {
                    id: $(e.target).closest(this.options.elementItem).data('id')
                },
                success: function (response) {
                    if (response.success) {
                        $.rsMeters('update', response.meters);
                    }
                }
            });
            this._setItemViewed( item );
        }
    },

    /**
     * Помечает все новости как просмотренные
     *
     * @param e
     * @private
     */
    _markAllAsViewed: function(e, panel) {
        if (!$(e.target).hasClass(this.options.elementDisabledClass)) {
            $.ajaxQuery({
                url: this.element.data('urls').markAllAsViewed,
                success: function (response) {
                    if (response.success) {
                        $.rsMeters('update', response.meters);
                    }
                }
            });

            this._setItemViewed( $(this.options.elementItem, panel) );
            $(this.options.elementAllViewed, panel).addClass(this.options.elementDisabledClass);
        }
    },

    _setItemViewed: function(item) {
        item.removeClass(this.options.elementNewClass)
            .find(this.options.elementViewCircle)
            .attr('data-original-title', lang.t('Прочитано'));
    }

});

$(function() {
    $('.rs-news-show').newsShow();
});