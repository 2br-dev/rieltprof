/**
 * Плагин инициализирует в административной панели работу формы редактирования правило для автозадач
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.autoTaskRule = function( method ) {
        var defaults = {
                ruleIfSelect: 'select[name="rule_if_class"]',
                taskContainer: '.task-container'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('autoTaskRule');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('autoTaskRule', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    data.form = $this.closest('form');

                    $(data.opt.ruleIfSelect, data.form).change(function() {

                        var selected = $(this).val();
                        var before = $(this).data('current');

                        var need_ask = $(data.opt.taskContainer, data.form).parent().is(':visible');

                        if (need_ask && !confirm(lang.t('Вы действительно желаете изменить условие создания задачи? Установленные задачи будут сброшены.'))) {
                            $(this).val(before);
                            return false;
                        }

                        $(this).data('current', $(this).val());

                        $.ajaxQuery({
                                url: $(data.opt.ruleIfSelect, data.form).data('load-form-url'),
                                data: {
                                    rule_if_class: selected
                                },
                                success: function(response) {
                                    $this.html(response.html).trigger('new-content');
                                    $(data.opt.taskContainer, data.form).trigger('reset');
                                }
                        });

                    });
                }
            };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );