/**
 * Плагин инициализирует в административной панели работу вкладки "Автосмена статуса" в задаче
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.autoChangeRule = function( method ) {
        var defaults = {
                addRuleButton: '.add-autochange-rule',
                addGroupButton: '.add-autochange-group',
                addOrItemButton: '.add-autochange-oritem',
                rules: '.autochange-rules',
                rule: '.autochange-rule',
                groups: '.groups',
                group: '.group',
                groupItems: '.group-items',
                removeRule: '.group-item > .remove',
                removeGroup: '.group > .group-remove',
                removeOrItem: '.autochange-rule > .rule-remove'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('autoChangeRule');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('autoChangeRule', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    var enableCheckbox = $this.closest('form')
                                                .find('input[name="is_autochange_status"]');

                    $this
                        .on('click', data.opt.addRuleButton, addRule)
                        .on('click', data.opt.addGroupButton, addGroup)
                        .on('click', data.opt.addOrItemButton, addOrItem)
                        .on('click', data.opt.removeRule+','+data.opt.removeGroup+','+data.opt.removeOrItem, remove);

                    enableCheckbox.on('change', function() {
                        $this.closest('tr').toggle( $(this).is(':checked') );
                    }).change();
                }
            };

            //private
            var addRule = function() {
                $.ajaxQuery({
                    url: $this.data('urls').addRule,
                    success: function(response) {
                        $(data.opt.rules, $this).append(response.html);
                    }
                });
            },

            addGroup = function() {
                var rule = $(this).closest(data.opt.rule);

                $.ajaxQuery({
                    url: $this.data('urls').addGroupItem,
                    data: {
                        rule_uniq: rule.data('uniq')
                    },
                    success: function(response) {
                        rule.find(data.opt.groups).append(response.html);
                    }
                });
            },

            addOrItem = function() {
                var rule = $(this).closest(data.opt.rule);
                var group = $(this).closest(data.opt.group);

                $.ajaxQuery({
                    url: $this.data('urls').addOrItem,
                    data: {
                        rule_uniq: rule.data('uniq'),
                        group_uniq: rule.data('uniq')
                    },
                    success: function(response) {
                        group.find(data.opt.groupItems).append(response.html);
                    }
                });
            },

            remove = function() {
                $(this).parent().remove();
            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );