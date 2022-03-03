/**
* JQuery plug-in для активации древовидных списков
*/
(function($){
    $.fn.rstree = function(options)
    {
        var make = function()
        {
            options = $.extend({
                useajax: true,
                inputContainer: '.input-container',
                divSelected:'.selected-container',
                selectedContainer: '.selected-container .group-block',
                pathContainer: '.current-path .breadcrumb',
                hideGroupCheckbox: 0,
                hideProductCheckbox: 0,
                urls: {
                    getChild: '',
                }
            }, options);

            var plugin_context = this;
            var context = options.context;

            var divSelected = $(options.divSelected, context);
            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var loadingChilds = false;

            var onSelectItem = function()
            {
                $('li a.act', plugin_context).removeClass('act');
                $(this).addClass('act');
                options.selectItem( $(this).parents('li:first'), plugin_context);
                changeCurrentPath();
            };

            var toggle = function(e)
            {
                var offset = $(e.target).offset();
                if (e.clientY > offset.top + 19) return false;

                if (e.target.tagName == 'LI')
                {
                    var jquery_el = $(e.target);
                    var el_class = jquery_el.attr('class');

                    if (el_class.indexOf('plus') > -1) {
                        loadChild(e.target, function()
                        {
                            //Отображаем подпункты
                            var newClass = jquery_el.attr('class').replace('plus','minus');
                            jquery_el.attr('class', newClass);
                            $('ul:first', e.target).show();
                        });

                    } else if (el_class.indexOf('minus') > -1) {
                        //Скрываем подпункты
                        var newClass = jquery_el.attr('class').replace('minus','plus');
                        jquery_el.attr('class', newClass);
                        $('ul:first', e.target).hide();
                    }

                    return false;
                }
            };

            var loadChild = function(li, callback)
            {
                if (loadingChilds) return false;
                if ($('>ul', li).length>0) {
                    callback();
                } else if (options.useajax) {
                    var $img = $('img', li);
                    var $img_before = $img.attr('src');
                    $img.attr('src', '/resource/img/adminstyle/small-loader.gif');
                    loadingChilds = true;

                    $.ajaxQuery({
                        loadingProgress: false,
                        url: options.urls.getChild,
                        data: {
                            id: $(li).attr('qid'),
                            hideGroupCheckbox:options.hideGroupCheckbox,
                            hideProductCheckbox: options.hideProductCheckbox,
                        },
                        success: function(response) {
                            $img.attr('src', $img_before);
                            $(li).append(response.html);
                            if ($('>input:checkbox', li).get(0).checked) {
                                $('ul input:checkbox', li)
                                    .attr('checked', 'checked')
                                    .attr('disabled','disabled');
                            }

                            bindEvents();
                            callback();
                            loadingChilds = false;
                        }

                    })
                }
            };

            var checkboxChange = function(e, trigged)
            {
                var val = this.value;
                var parentLi = $(this).parent('li');

                parents_str = ',';
                parentLi.parents('li[qid]').each(function() {
                    parents_str = parents_str + $(this).attr('qid')+',';
                });

                if (this.checked) {
                    $('ul input:checkbox', parentLi)
                        .attr('checked', 'checked')
                        .attr('disabled','disabled');

                    if (!trigged) { //Если это событие не вызвали мы сами же при открытии диалога
                        if ($(".dirs", inputContainer).length){
                            $(".dirs", inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }else{
                            $(inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }


                    var li_product = $('<li class="group">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="group_icon"></span>'+
                            '<span class="value"></span>'+
                        '</li>');

                    li_product.attr('val', val);
                    li_product.find('.value').text( $('a', $(this).parents('li:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));

                    selectedContainer.append(li_product);

                        //Удаляем выбранные ранне элементы, если отмечена более высокая по иерархии категория
                        $("input[data-catids*=',"+val+",']", inputContainer).each(function()
                        {
                            $("li[val='"+this.value+"']", divSelected).remove();
                            $(this).remove();
                        });
                    }

                } else {
                    $('ul input:checkbox', parentLi)
                        .removeAttr('checked')
                        .removeAttr('disabled');

                    if (!trigged) {
                        $("input[name='"+fieldName+"[group][]'][value="+val+"]", inputContainer).remove();
                        $("li[val="+val+"]", selectedContainer).remove();
                    }
                }
                //bindSelection();
                options.selectCheck(this, plugin_context);
                //$(context).trigger('new-content');
            };

            var watchSelectedInputs = function(callTrigger)
            {
                $('input:checkbox', plugin_context).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[group][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            if (callTrigger) $(this).trigger('change',[true]);
                        }
                    }
                });
            };

            var bindEvents = function()
            {
                $('li, li > *', plugin_context).unbind();
                $('li a', plugin_context).click(onSelectItem);
                $('li', plugin_context).click(toggle);
                $('li input:checkbox', plugin_context).change(checkboxChange);
                watchSelectedInputs(true);
            };

            var onDialogOpen = function()
            {
                $('input', plugin_context).removeAttr('checked').removeAttr('disabled');
                watchSelectedInputs(true);
            };

            var changeCurrentPath = function()
            {
                var stack = [];
                var item = $('li a.act', plugin_context);
                item.parents('.admin-category li[qid]').each(function() {
                    var title = $('> a', this).text();
                    stack.push(title);
                });

                var pathContainer = $(options.pathContainer).empty();

                stack.forEach(function(value, index) {
                    pathContainer.prepend( $('<span>').text(value).wrap('<li>').parent() );
                });
                pathContainer.prepend( '<li><i class="zmdi zmdi-folder left-toggle"></i></li>' );
                pathContainer.find('li:last').addClass('active');
            };

            bindEvents();
            changeCurrentPath();
            $(plugin_context).bind('dialogOpen', onDialogOpen);
        };

        return this.each(make);
    }
})(jQuery);





/**
* JQuery plug-in для выбора товаров.
*/
(function($){
    $.fn.selectProduct = function(options)
    {
        var make = function()
        {
            //Текущие настройки
            var current = {
                page:1,
                pageSize:20,
                catid:0,
                filter:{}
            };

            var context = this;

            options = $.extend({
                itemHtml: function(){
                    return $('<li class="product">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="product_icon"></span>'+
                            '<span class="product_image cell-image" data-preview-url=""><img src="" alt=""/></span>'+
                            '<span class="barcode"></span>'+
                            '<span class="value"></span>'+
                        '</li>');
                },
                startButton: '.select-button',
                divLoader: '.loader',
                divResult: '.selected-goods',
                divCategory: '.admin-category',
                divProducts: '.product-container',
                tableProducts: '.product-list',
                dialog: 'productDialog',
                openDialog: false,
                additionalItemHtml: '',

                userCost : '',
                divPaginator: '.paginator',
                pagLeft: '.pag_left',
                pagRight: '.pag_right',
                pagSubmit: '.pag_submit',
                pagPage: '.pag_page',
                pagPageSize: '.pag_pagesize',
                inputContainer: '.input-container',
                groupSelectedContainer:'.selected-container .group-block',
                selectedContainer: '.selected-container .product-block',
                urls: {
                    getChild: '',
                    getDialog: '',
                    getProducts: ''
                },
                selectButtonText: lang.t('Выбрать'),
                filterSet:'.set-filter',
                filterClear:'.clear-filter',
                showCostTypes: false,
                onResult: function(){},
                onCheckProduct: function() {}
            }, options);

            options.urls = $(context).data('urls');

            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var groupSelectedContainer = $(options.groupSelectedContainer, context);
            var initialized = false;
            var hideGroupCheckbox = (+$(this).hasClass('hide-group-cb')); //Скрывать checkbox у категорий
            var hideProductCheckbox = (+$(this).hasClass('hide-product-cb')); //Скрывать checkbox у категорий
            var showVirtualDirs = (+$(this).hasClass('show-virtual-dirs')); //Показывать виртальные категории
            var openDialogEvent;

            //Назначаем сортировку на элементы c товарами и категориям
            $(".product-block", $(context)).sortable({
                placeholder: "portlet-placeholder",
                //Когда останавливаемся, то сортируем список с идентификаторами
                update: function( event, ui ){
                    //Посмотрим позицию текущего элемента
                    var item_id = $(ui.item).attr('val');
                    var prev    = $(ui.item).prev(); //Определим кто перед нами
                    if (prev.length){ //Если перед нами кто-то есть, то переместимся к нему
                        $("[value='" + item_id + "']", inputContainer).insertAfter($("input[value='" + prev.attr('val') + "']", inputContainer));
                    }else{ //Если мы первые
                        $("[value='" + item_id + "']", inputContainer).insertBefore($("input:eq(0)", inputContainer));
                    }
                }
            });
            $(".group-block", $(context)).sortable();

            var tree, dialog;

            var loadProducts = function(optVars)
            {
                $.ajaxQuery({
                    url: options.urls.getProducts,
                    data: $.extend(optVars, {
                        hideProductCheckbox: hideProductCheckbox
                    }),
                    success: function(response) {
                        dialog.removeClass('folders-open');
                        $(options.divProducts, dialog).html(response.html);
                        bindEvents();
                    }
                });
            };

            var showProductLoader = function()
            {
                var loader = $(options.divLoader, dialog);
                var container = $('.productblock', dialog);

                loader.height( container.height()+'px' );
                $('.overlay', loader).height( container.height()+'px' );
                loader.show();
            };

            var hideProductLoader = function()
            {
                $(options.divLoader).hide();

            };

            var onStartClick = function(e)
            {
                openDialogEvent = e;
                dialog = $('#'+options.dialog);

                if (!dialog.length) {
                    dialog = $('<div id="'+options.dialog+'" class="selectProduct"></div>');
                    var dialogParams = {
                        resize: onResize,
                        title: lang.t('Выберите товары или группы товаров'),
                        dialogClass:'select-product-dialog',
                        minWidth: 900,
                        width: 1000,
                        height: $(window).height()-130,
                        clickOut: false,
                        autoOpen:false,
                        modal:true,
                        resizable:false,
                        create: function() {
                            var wrapper = $('.admin-dialog-wrapper:first');
                            if (!wrapper.length) {
                                wrapper = $('<div class="admin-style admin-dialog-wrapper" />').appendTo('body');
                            }
                            $(this).closest('.ui-dialog').appendTo(wrapper);
                            $(this).data('dialogWrapper', wrapper);
                        },
                        open: function() {
                            $('.ui-widget-overlay:last').appendTo( $(this).data('dialogWrapper') );
                        },
                        buttons: {}
                    };

                    dialog.dialog(dialogParams);
                }

                var buttons = [];
                if (options.selectButtonText !== false) {
                    buttons.push({
                        text: options.selectButtonText,
                        click: onPressOk,
                        class: 'btn btn-success'
                    });
                }
                dialog.dialog('option', 'buttons', buttons);


                if (!initialized) {

                    var params = {
                        hideGroupCheckbox : hideGroupCheckbox,
                        hideProductCheckbox : hideProductCheckbox
                    };
                    if (showVirtualDirs){
                        params['showVirtualDirs'] = showVirtualDirs;
                    }

                    $.ajaxQuery({
                        url: options.urls.getDialog,
                        data: params,
                        success: function(response) {

                            dialog.html(response.html);
                            dialog.dialog('open');
                            bindFirst();
                            bindEvents();
                            onResize();

                            $(options.divCategory, dialog).rstree({
                                context: context,
                                urls: {
                                    getChild: options.urls.getChild
                                },
                                hideGroupCheckbox: hideGroupCheckbox,
                                hideProductCheckbox: hideProductCheckbox,
                                selectItem: function(li) {
                                    current.catid = li.attr('qid');
                                    current.page = 1;
                                    loadProducts(current);
                                },
                                selectCheck: function() {
                                    updateProductCheckbox();
                                },

                            });

                            // Вставка кастомных кнопок в buttonpane
                            if(options.showCostTypes){
                                $('.my-buttons-pane').remove();
                                var myPane = $('<div>').addClass('my-buttons-pane');
                                myPane.html($('.to-dialog-buttonpane'));
                                $('.ui-dialog-buttonpane').prepend(myPane);
                            }

                            initialized = true;
                        }
                    });

                } else {
                    dialog.dialog('open');
                    $(options.divCategory).trigger('dialogOpen');

                    $(options.tableProducts+' input:enabled').removeAttr('checked');
                    $(options.tableProducts+' .chk.checked').removeClass('checked');
                    watchSelectedInputs();
                }
            };

            var checkboxChange = function()
            {
                var val = $(this).val();

                if (this.checked) {
                    if ($(".products", inputContainer).length){
                        if ( !$(".products input[value='"+val+"']", inputContainer).length ) {
                            $(".products", inputContainer).append('<input type="hidden" name="'+fieldName+'[product][]" data-weight="'+$(this).data('weight')+'" data-catids="'+$(this).attr('catids')+'" value="'+val+'">');
                        }
                    }else{
                        if ( !$("input[value='"+val+"']", inputContainer).length ) {
                            $(inputContainer).append('<input type="hidden" name="' + fieldName + '[product][]" data-weight="' + $(this).data('weight') + '" data-catids="' + $(this).attr('catids') + '" value="' + val + '">');
                        }
                    }


                    var li_product = options.itemHtml();

                    li_product.attr('val', val);
                    li_product.find('.barcode').html($(this).data('barcode'));
                    li_product.find('.product_image').data('preview-url', $(this).data('preview-url'));
                    li_product.find('.product_image img').attr('src', $(this).data('image'));
                    li_product.find('.value').text( $('.title', $(this).parents('tr:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));
                    li_product.find('.onlyone')
                        .attr('title', lang.t('Всегда в количестве одна штука'))
                        .attr('name', 'concomitant_arr[onlyone]['+val+']');

                    if (options.onCheckProduct) {
                        options.onCheckProduct.call(this, val, li_product, dialog);
                    }

                    var additionalItemHtml = options.additionalItemHtml.replace('%field_name%', fieldName).replace('%item_id%', val);
                    li_product.append(additionalItemHtml);

                    if (!$("li[val="+val+"]", selectedContainer).length > 0) {
                        selectedContainer.append(li_product).trigger('new-content');
                    }
                    $(this).closest('.chk').addClass('checked');
                } else {
                    $("input[name='"+fieldName+"[product][]'][value="+$(this).val()+"]", inputContainer).remove();
                    $("li[val="+val+"]", selectedContainer).remove();
                    $(this).closest('.chk').removeClass('checked');
                }
                bindSelection();
            };

            var bindEvents = function()
            {
                $(options.divProducts+' > *', dialog).unbind();

                if (options.onSelectProduct) {
                    $('tbody.product-list tr', dialog).click(function() {
                        options.onSelectProduct.call(this, {
                            openDialogEvent: openDialogEvent,
                            productTitle: $('.title', this).text(),
                            productBarcode: $('.barcode', this).text(),
                            productId: $(this).data('id'),
                            dialog: dialog
                        });
                    });
                }

                $(options.tableProducts+' input:checkbox', dialog).change(checkboxChange);

                //Пагинатор
                $(options.pagLeft+','+options.pagRight).click(function() {
                    current.page = $(this).attr('gopage');
                    loadProducts(current);
                });

                $(options.pagSubmit).click(function() {
                    var pag = $(this).closest(options.divPaginator);
                    current.page = $(options.pagPage, pag).val();
                    current.pageSize = $(options.pagPageSize, pag).val();
                    loadProducts(current);
                });

                selectAll();
                updateProductCheckbox();
                watchSelectedInputs();
                setDefaultCostType();
            };

            var setDefaultCostType = function()
            {
                if(options.userCost){
                    var selector_element = $('select[name=costtype]', dialog);
                    if(selector_element){
                        selector_element.val(options.userCost);
                    }
                }
            };

            var selectAll = function()
            {
                $('input[name="select-all"]', dialog).on('change', function(){
                    var product_inputs = $(options.tableProducts, dialog).find('input[type=checkbox][value]');
                    product_inputs.prop('checked', $(this).prop('checked'));
                    product_inputs.change();
                });
                $(options.tableProducts, dialog).find('input').on('change', function(){
                    if(!$(this).prop('checked')){
                        $('input[name="select-all"]', dialog).prop('checked', false);
                    }
                });
            };

            var updateProductCheckbox = function()
            {
                var checked_dirs = $(options.divCategory+' input:checked', dialog);

                if (checked_dirs.length>0) {
                    $(options.tableProducts+' input', dialog).each(function() {

                        var product_dirs = $(this).data('catids');
                        var checked = false;

                        for(var i=0; i<checked_dirs.length; i++) {
                            if (product_dirs.indexOf(','+$(checked_dirs[i]).val()+',') != -1) {
                                checked = true;
                            }
                        }
                        if (checked) {
                            this.checked = true;
                            this.disabled = true;
                            $(this).closest('.chk').addClass('.checked');
                        } else {
                            $(this).closest('.chk').removeClass('.checked');
                            if (this.disabled) {
                                this.checked = false;
                                this.disabled = false;
                            }
                        }
                    });
                } else {
                    $(options.divProducts+' tr input:disabled', dialog).removeAttr('checked').removeAttr('disabled');
                }
                bindSelection();
            };

            var watchSelectedInputs = function()
            {
                $(options.tableProducts+' input', dialog).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[product][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            $(this).closest('.chk').addClass('checked');
                        }
                    }
                });
            };

            var bindFirst = function()
            {
                //Фильтр
                $('.filter', dialog).submit(setFilter);

                $(options.filterClear, dialog).click(function() {
                    $('.field-id', dialog).val('');
                    $('.field-title', dialog).val('');
                    $('.field-barcode', dialog).val('');
                    $('.field-sku', dialog).val('');
                   current.filter = {};
                   loadProducts(current);
                });
            };

            var setFilter = function()
            {
                current.filter.id       = $('.field-id', dialog).val();
                current.filter.title   = $('.field-title', dialog).val();
                current.filter.barcode = $('.field-barcode', dialog).val();
                current.filter.sku = $('.field-sku', dialog).val();
                
                loadProducts(current);                
            };

            var onResize = function()
            {
                $('.column-left', dialog).height( dialog.height()+'px' );
                $('.column-right', dialog).height( dialog.height()+'px' );
            };

            var onDeleteSelectedProduct = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[product][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            var onDeleteSelectedGroup = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[group][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            /**
            * Поставить обработчики событий на список выбранных групп товаров
            */
            var bindSelection = function()
            {
                $('li.product a', selectedContainer).off('.selectproduct');
                $('li.group a', groupSelectedContainer).off('.selectproduct');

                $('li.product a', selectedContainer).on('click.selectproduct', onDeleteSelectedProduct);
                $('li.group a', groupSelectedContainer).on('click.selectproduct', onDeleteSelectedGroup);
            };

            /**
            * При нажатии кнопки выбрать (зафиксировать результат выбора)
            */
            var onPressOk = function()
            {
                options.onResult({
                    openDialogEvent: openDialogEvent
                });
                closeDialog();
            };

            var closeDialog = function()
            {
                dialog.dialog('close');
            };

            if (options.openDialog){
                onStartClick();
            }
            $(context).on('click', options.startButton, onStartClick);
            bindSelection(); //Если в форме по умолчанию присутствуют выбранные раннее элементы
        };

        return this.each(make);
    }
})(jQuery);

//Событие нужно для блока дизайнера
var productSelectInitedEvel = new CustomEvent('product-select.inited');
if (document.querySelector('body')){
    document.querySelector('body').dispatchEvent(productSelectInitedEvel);
}
