(function($){
    $.fn.selectTemplate = function( method ) {  
        var defaults = {
            useRelative: '#use-relative',
            parent: '*',
            handler: '.selectTemplate'
        }, 
        args = arguments;

        return this.each(function() {
            var $this = $(this), 
                data = $this.data('selectTemplate');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('selectTemplate', data);
                    data.options = $.extend({}, defaults, initoptions);
                   
                   $(data.options.handler, $this.parents(data.options.parent).get(0)).click(showDialog);
                },
            }
            
            //private
            var showDialog = function() {
                if ($this.is(':disabled')) return false;
                if ($this.val() != '') {
                    $('#templateManager').dialog('destroy');
                }
                $.rs.openDialog({
                    dialogId: 'templateManager',
                    url: data.options.dialogUrl,
                    ajaxOptions: {
                        data: {
                            start_tpl: $this.val()
                        }
                    },
                    dialogOptions: {
                        width:1010,
                        height:550,
                        dialogClass: 'templateManager',
                    },
                    afterOpen: function($dialog) {
                        function bindSelect() {
                            $('.canselect', $dialog)
                            .unbind('.selectTemplate')
                            .bind('click.selectTemplate', selectFile);
                        }
                        $dialog.bind('new-content', bindSelect);
                        bindSelect();
                    }
                });
            },
            
            selectFile = function(e) {
                var useRelative = $(data.options.useRelative).is(':checked');
                var path = $(this).closest('.item').data('path');
                if (useRelative) {
                    path = path.replace(/^theme:([\w]+)\/(.*)$/, '%THEME%/$2');
                }
                $this.focus().val(path);
                closeDialog();
                return false;
            },
            
            closeDialog = function() {
                $('#templateManager').dialog('close');
            };
            
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }    
})(jQuery);    