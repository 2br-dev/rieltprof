/**
 * Плагин инициализирует в административной панели работу поля установки связи с другими объектами
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.linkManager = function( method ) {
        var defaults = {
                addLink: '.open-link-manager',
                linkContainer: '.link-container',
                linkItem: 'li',
                linkItemRemove: '.remove'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('linkManager');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('linkManager', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.opt.container = $(data.opt.linkContainer, $this);

                    $this.on('click', data.opt.addLink, function() {

                        if ($.rs.loading.inProgress)
                            return false;

                        $.rs.openDialog({
                            url: $(this).data('url'),
                            afterOpen: function(dialog) {

                                $('.tabs.link-manager', dialog).on('shown.bs.tab', function(e) {

                                    var tabs = $(e.target).closest('.tabs');
                                    $('.tab-pane form').removeClass('crud-form');
                                    $('.tab-pane.active form').addClass('crud-form');

                                });

                                dialog.on('crudSaveSuccess', function(event, response) {

                                    var input_name = $(data.opt.container).data('formName') + '['+response.link_type+'][]';

                                    if (canInsert(input_name, response.link_id)) {
                                        var input = $('<input type="hidden">')
                                            .attr('name', input_name)
                                            .val(response.link_id);

                                        var li = $('<li>').append(input).append(response.link_view);
                                        $(data.opt.container).append(li).trigger('new-content');
                                    }
                                });
                            }
                        });

                        })
                        .on('click', data.opt.linkItemRemove, function() {
                            $(this).closest(data.opt.linkItem).remove();
                        });
                }
            };

            //private
            var canInsert = function(input_name, input_value) {
                if ($('input[name="'+input_name+'"][value="'+input_value+'"]', $(data.opt.container)).length) {
                    $.messenger('show', {
                        theme: 'error',
                        text: lang.t('Связь с выбранным объектом уже существует')
                    });
                    return false;
                }
                return true;
            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

    $.contentReady(function() {
        $('.orm-type-link-manager', this).linkManager();
    });

})( jQuery );