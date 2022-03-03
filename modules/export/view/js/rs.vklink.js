/**
 * Плагин инициализирует сопоставление с категориями ВКонтакте категорий ReadyScript
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.vkLink = function( method ) {
        var defaults = {
                reloadVkCategory: '.vk-reload-category',
                selectLink: '.vk-link'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('vkLink');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('vkLink', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    $this
                        .on('click', data.opt.reloadVkCategory, reloadVkCategory)
                }
            };

            //private
            var
                reloadVkCategory = function() {
                    var context = $(this).closest('tr');
                    var currentValue = $(data.opt.selectLink, context).val();

                    $.ajaxQuery({
                        url: $(this).data('link'),
                        success: function(response) {
                            if (response.success) {
                                var newData = $(response.html);
                                newData.find(data.opt.selectLink).val(currentValue);
                                context.replaceWith(newData);
                            }
                        }
                    });

                };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

    $.contentReady(function() {
        $('.vk-link', this).vkLink();
    });

})( jQuery );