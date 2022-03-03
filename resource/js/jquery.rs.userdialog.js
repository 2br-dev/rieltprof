/**
 * Плагин инициализирует в административной панели работу поля для выбора/создания пользователя
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.userDialog = function( method ) {
        var defaults = {
                idField: '.user-id-field',
                nameField: '.user-name-field',
                resetButton: '.user-reset-select',
                selectUser: '.select-user',

                dialogForm: '#userAddForm',
                dialogUserType: '.user-type',
                dialogRegOrSelectInput: 'input[name="is_reg_user"]',
                dialogRegTab: '.reg-tab',
                dialogCompanyBlock: '.company',
                dialogTabPrefix: '#partner-'

            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('userDialog');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('userDialog', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    
                    $this.on('click', data.opt.selectUser, function() {

                        if ($.rs.loading.inProgress)
                            return false;

                        $.rs.openDialog({
                            url: $(this).data('url'),
                            afterOpen: function(dialog) {

                                bindDialogEvents(dialog);

                            }
                        });
                        
                    }).on('click', data.opt.resetButton, function() {
                        $(this).addClass('hidden');
                        $this.find(data.opt.nameField).html(lang.t('Не выбрано'));
                        $this.find(data.opt.idField).val('');
                    });
                }
            };

            //private
            var bindDialogEvents = function(dialog) {

                var form = $(data.opt.dialogForm, dialog);
                //Обновляем данные в форме, если пользователь успешно выбран/создан
                form
                    .on('crudSaveSuccess', function(event, response) {
                            $this.find(data.opt.idField).val(response.user_id);
                            $this.find(data.opt.nameField).html(
                                $('<a target="blank">')
                                    .text(response.user_fio)
                                    .attr('href', response.user_link)
                            );
                            $this.find(data.opt.resetButton).removeClass('hidden');
                    })
                    // Смена типа пользователя
                    .on('change', data.opt.dialogUserType, function(){
                        var val = $(this).val();
                        $(data.opt.dialogCompanyBlock, form).toggleClass('hidden', val == '0');
                    })
                    //Смена физ.лицо/юр.лицо
                    .on('change', data.opt.dialogRegOrSelectInput, function() {
                        $(data.opt.dialogRegTab, form).hide();
                        $(data.opt.dialogTabPrefix + $(this).attr('id'), form).show();
                    })
                    .on('click', '.show-password', function() {
                        var input = $(this).siblings('input');
                        var is_type_password = input.attr('type') == 'password';
                        $(this).toggleClass('zmdi-eye-off', is_type_password).toggleClass('zmdi-eye', !is_type_password);
                        input.attr('type', is_type_password ? 'text' : 'password');
                    });
            };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    $.contentReady(function() {
        $('.orm-type-user-dialog', this).userDialog();
    });

})( jQuery );