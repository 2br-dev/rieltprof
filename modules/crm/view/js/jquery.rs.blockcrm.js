/**
 * Плагин инициализирует в административной панели работу блока управления взаимодействиями
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.blockCrm = function( method ) {
        var defaults = {
                addLink: '.add-interaction',
                counterElement: '.counter.crm-interaction',
                editButton: '.interaction-edit',
                delButton: '.interaction-del',
                checkboxName: 'interaction',
                refresh: '.refresh',
                multiRemoveValue: '.group-toolbar .delete',
                valuesList: '.values-list',
                virtualForm      : '.virtual-form',         //Класс виртуальной формы
                virtualSubmit    : '.virtual-form .virtual-submit, .virtual-form button[type="submit"]', //Класс элементов отправки виртуальной формы
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('blockCrm');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('blockCrm', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    var uniqName = $this.attr('class') + '-checked';

                    $this
                        .on('click', data.opt.addLink, function() {
                            editItem($(this).data('url'));
                        })
                        .on('click', data.opt.editButton, function() {
                            editItem($(this).data('url'));
                        })
                        .on('click', data.opt.refresh, function() {
                            refreshList($(this).data('url'));
                        })
                        .on('click', data.opt.virtualSubmit, submitVirtualForm)
                        //Перехватываем нажатие Enter в форме фильтрации
                        .on('keydown', data.opt.virtualForm + ' input', function(e) {
                            if (e.keyCode == 13) {
                                e.preventDefault();
                                e.stopPropagation();
                                submitVirtualForm.call(this, e);
                            }
                        })
                        .on('click', data.opt.virtualForm + ' a[data-href]', function(e) {
                            refreshList($(this).data('href'));
                            e.preventDefault();
                        })
                        .on('click', data.opt.multiRemoveValue, function(e) {
                            var ids = getSelectedOffers();
                            removeItems(ids);
                        })
                        .on('click', data.opt.delButton, function() {
                            var id = $(this).closest("[data-id]").data('id');
                            removeItems([{name: data.opt.checkboxName, value: id}]);
                        })
                        .on('change', 'input[type="checkbox"]', function() {
                            setTimeout(function() {
                                //Если есть отмеченные элементы, то посылаем событие - Запретить действия над товаром, иначе - разрешить
                                if ($('input[type="checkbox"]:checked', $this).length) {
                                    $this.trigger('disableBottomToolbar', uniqName);
                                } else {
                                    $this.trigger('enableBottomToolbar', uniqName);
                                }
                            }, 100);
                        });

                    $this.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                        $this.trigger('enableBottomToolbar', uniqName);
                    });
                }
            };

            //private
            var
            /**
             * Возвращает отмеченные строки
             */
            getSelectedOffers = function()
            {
                var items = [];
                $(data.opt.valuesList + ' input[name][type="checkbox"]:checked', $this).each(function() {
                    items.push({
                        name: $(this).attr('name'),
                        value: $(this).val()
                    });
                });
                return items;
            },

            refreshList = function(url, post_params) {
                if (!url) {
                    url = $this.data('refreshUrl');
                }

                if (!post_params) {
                    post_params = [];
                }

                $.ajaxQuery({
                    url: url,
                    data: post_params,
                    method: 'post',
                    success: function(response) {
                        var newData = $(response.html);
                        var newCounter = newData.find('.total_value').text();
                        $this.replaceWith(newData).trigger('new-content');
                        $(data.opt.counterElement).text(newCounter);
                        $this.trigger('enableBottomToolbar', uniqName);
                    }
                });
            },

            editItem = function(url) {

                if ($.rs.loading.inProgress)
                    return false;

                $.rs.openDialog({
                    url: url,
                    dialogOptions: {
                        width:'75%',
                        height:0.75 * $(window).height()
                    },
                    afterOpen: function(dialog) {
                        dialog.on('crudSaveSuccess', function(event, response) {
                            refreshList();
                        });
                    }
                });
            },

            removeItems = function(ids) {
                var items = ids;
                var count = $(data.opt.valuesList + ' .select-all:checked', $this).length ? $('.total_value', $this).text() : items.length;

                if (!items.length || !confirm(lang.t('Вы действительно хотите удалить выбранные элементы(%count)?', {count:count}))) {
                    return false;
                }

                $.ajaxQuery({
                    url: $this.data('removeUrl'),
                    method:'POST',
                    data: items,
                    success: function(response) {
                        if (response.success) refreshList();
                    }
                });
            },

            /**
             * Постит данные из виртуальной формы
             */
            submitVirtualForm = function(e)
            {
                var form = $(this).closest(data.opt.virtualForm);
                var real_form = $('<form enctype="multipart/form-data"/>');

                form.find('input, select, textarea').each(function() {
                    var element = $(this).clone(true);
                    if ($(this).attr('type') == 'file' && element.val() != $(this).val()) {
                        $(this).after(element);
                        element = $(this);
                    }

                    if (element.is('select,textarea')) {
                        element.val( $(this).val() ); //bugfix select clone
                    }
                    element.appendTo(real_form);
                });

                var params = real_form.serializeArray();
                refreshList(form.data('action'), params);
                e.preventDefault();
            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );