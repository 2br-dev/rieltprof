/**
* Plugin, активирующий вкладку "комплектации" у товаров
* также плагин управляет назначением фото для комплектаций из вкладки Фото
*/
(function($){
    
$.fn.offer = function(method) {

    var defaults = {
        offerBlock       : '.offer-block',            //Класс для оборачивающего общего блока с компл. 
        offers           : '.offer-list',             //Класс для списка компл. 
        addOffer         : '.add-offer',              //Класс для добавления компл. 
        
        productPrice     : '[name^="excost"]',        //Цены товара
        mainOffer        : '.main-offer',               
        offerLine        : '.item',                   //Класс одной строки комплектаций
        offerEmptyLine   : '.empty-row',              //Класс пустой строки   
        newOffer         : '.new-item',               //Класс новой строки комплектации
        offerEditLine    : '.edit-form',              //Класс строки редактирования комплектации
        editOffer        : '.offer-edit, .offer-list td.clickable', //Ссылки на редактирование комплектации
        offerChangeWithMain : '.offer-change-with-main',   //Класс для функции "сделать комплектацию основной"
        
        offersImagesRow  : '.offer-images-line',       //Строка с фото у комплектаций
        offersImages     : '.offer-images-line a',
        productPhotos    : '.photo-list',            //Блок с фотографиями товара
        productOnePhoto  : '.photo-one',             //Одна фотография товара
        removeOffer      : '.delete',                //Класс для удаления компл. 
        barcode          : 'input[name="barcode"]',  //Поле Артикул
        sku              : 'input[name="sku"]',      //Поле Штрихкод
        
        offersPhotoDialog: {
            addOfferLink : '.add-offer-link',           //Кнопка добавить фото к комплектации
            addOffersLink: '.add-offers-link',          //Кнопка добавить несколько фото к комплектации
            applySelect  : '.apply-photo-offer-filter', //Кнопка Выбрать
            clearSelect  : '.clear-photo-offer-filter', //Кнопка Снять отметки
            offerZone    : '.photo-select',             //Элемент Select со списком связанных комплектаций
            save         : '.offer-photo-actions .save', //Кнопка Назначить
            reset        : '.offer-photo-actions .offer-photo-clear' //Ссылка снять отметки с комплектаций
        },
        
        //Параметры для многомерных комплектаций
        multiOfferWrapId : '#multioffer-wrap',       //Оборачивающий контейнер всего блока многомерных комплектаций
        useMultiOffer    : '#use-multioffer',        //Галка включающая многомерные компл. 
        crAutoOffers     : '#create-auto-offers',     //Галка "Создавать комплектации"
        multiOfferName   : 'multioffers[levels]',   //Атрибут name у уровня многомерной комплектации
        multiOfferPhoto  : 'multioffers[is_photo]', //Атрибут name галки с фото
        multiOfferWrap   : '.multioffer-wrap',       //Оборачивающий общий котейнер
        offersBody       : '.offers-body',           //Контейнер со всеми комплектациями
        addLevel         : '.add-level',             //Кнопка добавить уровень мн. компл. 
        deleteLevel      : '.delete-level',          //Кнопка удалить уровень комплектации
        createComplexs   : '.create-complexs',       //Кнопка создания многомерных комплектаций 
        hide             : 'cant-use',                  //Строка уровнем компл.
        rowMO            : '.line',                  //Строка уровнем компл.
        virtualForm      : '.virtual-form',         //Класс виртуальной формы
        virtualSubmit    : '.virtual-form .virtual-submit, .virtual-form button[type="submit"]', //Класс элементов отправки виртуальной формы
        tableInlineEdit  : '.table-inline-edit',    //Таблица, оборачивающая форму редактирования
        tableInlineEditCancel      : '.cancel',     //Кнопка "Отменить" в форме редактирования
        offerDeleteButton: '.offer-del',            //Кнопка "Удалить" одну комплектацию
        multiDeleteButton : '.group-toolbar .delete', //Кнопка "Удалить" выбранные комплектации
        multiEditButton   : '.group-toolbar .edit'    //Кнопка "Редактировать" выбранные комплектации
    },
    args = arguments;
    
    return this.each(function() {
        var $this = $(this),
            xhr,
            data = $this.data('offer');
        
        var methods = {
            /**
            * Инициализация, назначение действий
            * 
            * @param initoptions
            */
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('offer', data);
                data.options = $.extend({}, defaults, initoptions);

                $this
                    .on('click', data.options.virtualSubmit, submitVirtualForm)
                    //Перехватываем нажатие Enter в форме фильтрации
                    .on('keydown', data.options.virtualForm + ' input', function(e) {
                        if (e.keyCode == 13) {
                            e.preventDefault();
                            e.stopPropagation();
                            submitVirtualForm.call(this, e);
                        }
                    })
                    .on('click', data.options.addOffer, methods.addOffer)
                    .on('click', data.options.editOffer, function() {
                        var offer_id = $(this).closest(data.options.offerLine).data('id');
                        methods.editOffer(offer_id);
                    })
                    .on('click', data.options.tableInlineEdit+' '+data.options.tableInlineEditCancel, function() {
                        var offer_id = $(this).closest(data.options.offerEditLine).data('id')
                        methods.editOffer(offer_id);
                    })
                    .on('click', data.options.virtualForm + ' a[data-href]', function(e) {
                        methods.refresh($(this).data('href'));
                        e.preventDefault();
                    })
                    .on('click', data.options.offerChangeWithMain, function(e) {
                        var offer_id = $(this).closest('[data-id]').data('id'),
                            offer_title = $(this).closest('[data-id]').find('.title').text(),
                            post_data = [{name: 'offer_id', value: offer_id}];
                        
                        if (offer_id == 0){return;}
                                 
                        post_data = post_data.concat(getMainOfferData()); 
                        post_data = post_data.concat(getProductPriceData()); 
                        post_data = post_data.concat(getProductBarcode());
                        post_data = post_data.concat(getProductSku());
                                
                        if (confirm(lang.t('Вы действительно хотите сделать коплектацию "%title" основной?', {title: offer_title}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerChangeWithMain,
                                method: 'post',
                                data: post_data,
                                success: function(response) {   
                                    var form = $this.closest('.crud-form');
                                    $.each(response.excost, function(i, item){
                                        $('[name="excost['+i+'][cost_original_val]"]', form).val(item.cost_original_val);
                                        $('[name="excost['+i+'][cost_original_currency]"]', form).val(item.cost_original_currency);
                                    }); 
                                    $(data.options.barcode, form).val(response.barcode);
                                    $(data.options.sku, form).val(response.sku);
                                    if (response.success) methods.refresh(null, null, 'all-offers');
                                }
                            });
                        }
                    })
                    .on('click', data.options.offerDeleteButton, function(e) {
                        var offer_id = $(this).closest('[data-id]').data('id'),
                            offer_title = $(this).closest('[data-id]').find('.title').text();
                        
                        if (offer_id == 0) {
                            methods.editOffer(offer_id);
                            return;
                        }
                        
                        if (confirm(lang.t('Вы действительно хотите удалить комплектацию "%title"?', {title: offer_title}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerDelete,
                                data: {
                                    offers:[offer_id]
                                },
                                success: function(response) {
                                    if (response.success) methods.refresh();
                                }
                            });
                        }
                    })
                    .on('click', data.options.multiDeleteButton, function(e) {
                        var offers_id = getSelectedOffers();
                        var count = $(data.options.offers + ' .select-all:checked', $this).length ? $('.total_value', $this).text() : offers_id.length;
                        
                        if (offers_id.length && confirm(lang.t('Вы действительно хотите удалить выбранные комплектации(%count)', {count:count}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerDelete,
                                data:offers_id,
                                success: function(response) {
                                    if (response.success) methods.refresh();
                                }
                            });
                        }
                    })
                    .on('click', data.options.multiEditButton, function(e) {
                        var selected = $(data.options.offers + ' input[type="checkbox"]:checked', $this).length;
                        if (!selected) return;
                        
                        var offer_id = $(data.options.offers + ' input[type="checkbox"]:checked:last', $this).val();
                        methods.editOffer(offer_id, selected>1);
                    })
                    .on('change', data.options.offers + ' input[type="checkbox"]', function() {
                        //Если есть отмеченные элементы, то посылаем событие - Запретить действия над товаром, иначе - разрешить
                        if ($(data.options.offers + ' input[type="checkbox"]:checked', $this).length) {
                            $this.trigger('disableBottomToolbar', 'offer-checked');
                        } else {
                            $this.trigger('enableBottomToolbar', 'offer-checked');
                        }
                    })
                    .on('click', data.options.offersImages, function() {
                        var value = $(this).data('id');
                        
                        if ($(this).is('.act')) {
                            $(this).removeClass('act');
                            $(this).parent().find('input[value="'+value+'"]').remove();
                        } else {
                            var name = $(this).data('name');
                            $(this).addClass('act');
                            $(this).parent().append('<input type="hidden" name="'+name+'" value="'+value+'">');
                        }
                    });
                    
                    $this.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                        $this
                            .trigger('enableBottomToolbar', 'offer-checked')
                            .trigger('enableBottomToolbar', 'offer-edit');
                    });
                    
                    $this.closest('.crud-form')
                            .on('changePhoto', onChangePhoto)
                            .on('click', data.options.offersPhotoDialog.addOfferLink, function() {
                                methods.openPhotoLinkDialog([$(this).data('id')]);
                            })
                            .on('click', data.options.offersPhotoDialog.addOffersLink, function() {
                                var ids = [];
                                $(data.options.productPhotos + ' .chk input:checked').each(function() {
                                    ids.push($(this).val());
                                });
                                methods.openPhotoLinkDialog(ids);
                            });
                    
                    
                                         
                //Многомерные комплектации                   
                $(data.options.multiOfferWrap,$this)
                                    .on('click',data.options.addLevel, methods.addMultiOfferLevel)    //Добавить уровень комплектаций
                                    .on('click',data.options.deleteLevel, methods.delMultiOfferLevel) //Добавить уровень комплектаций
                                    .on('click',data.options.createComplexs, methods.createComplexs)  //Создание многомерных комплектаций
                                    .on('click',data.options.crAutoOffers, toggleCreateMOffers)       //Вкл./выкл. галки "Создавать комплектации"
                                    .on('change','select[name^="'+data.options.multiOfferName+'"]',onMultiOfferLevelChange); //Событие на изменение уровня многомерной комплектации
                                     
                $('body').on('on-tab-open',tabChange ); //Создание многомерных комплектаций
                $(data.options.useMultiOffer,$this).on('click', showMOffers);
                
                if (!$(data.options.useMultiOffer,$this).prop('checked')){
                   $(data.options.multiOfferWrap,$this).hide(); 
                }
                
                checkMODelete(); //Проверяет можно ли удалять многомерные комплектации
                
                //Проверим существуют ли характеристики списковые, и если нет, то скороем опцию многомерных комплектаций
                var props = getActualPropListsIds(); 
                if (props.length==0){
                   $(data.options.multiOfferWrapId,$this).addClass(data.options.hide); 
                   $(data.options.multiOfferWrap,$this).hide(); 
                }
                
            },
            
            postForm: function(form, post_params) {
                $.ajaxQuery({
                    url: form.data('action'),
                    data: post_params,
                    method: 'post',
                    success: function(response) {
                        //Если это пост виртуальной формы, то отображаем ошибки формы, если они есть
                        if (response.success) {
                            methods.refresh();
                        } else {
                            $('.crud-form-error', form).fillError(response.formdata.errors, form);
                        }
                    }
                });                
            },
            
            /**
            * Открывает диалог связи комплектации и фотографии
            */
            openPhotoLinkDialog: function(photos_id) {
                
                var applySelection = function(enable, dialog) {
                     var filter = {};
                        $('.params-row select', dialog).each(function() {
                            filter[$(this).data('name')] = $(this).val();
                        });
                        
                        $('.offer-photo-select option', dialog).each(function() {
                            var match = true;
                            var option_params = $(this).data('params');
                            if (option_params !== null) {
                                $.each(filter, function (param_key, param_val) {
                                    if (param_val != '') {
                                        if (typeof (option_params[param_key]) == undefined || option_params[param_key] != param_val) {
                                            match = false;
                                        }
                                    }
                                });
                                if (match)
                                    $(this).prop('selected', enable);
                            }
                        });
                },
                                
                linkPhotoWithOffers = function(dialog) {
                    //Закроем открытую вкладку комплектаций
                    methods.closeOfferEdit();
                    
                    var post_data = $('#offers-photo-form').serializeArray().concat(getMainOfferData());
                    
                    $.ajaxQuery({
                        url: $this.data('urls').offerLinkPhotoSave,
                        method:'post',
                        data: post_data,
                        success: function(response) {
                            //Отметим фотографии Основной комплектации
                            
                            if (response.success) {
                                $(data.options.mainOffer + ' ' + data.options.offersImagesRow + ' input', $this).remove();
                                $(data.options.mainOffer + ' ' + data.options.offersImages, $this)
                                    .removeClass('act')
                                    .each(function() {
                                    
                                    if (response['main_offer_photos'].indexOf($(this).data('id').toString()) != -1) {
                                        $(this).click();
                                    }
                                });
                            }
                        }
                    });
                    
                    dialog.dialog('close');
                };                
                
                var post_data = getMainOfferData();
                
                $(photos_id).each(function(key, photo_id) {
                    post_data.push({
                        name: "photos_id[]",
                        value: photo_id
                    });
                });
                
                $.rs.openDialog({
                    url: $this.data('urls').offerLinkPhoto,
                    dialogOptions: {
                        width:510,
                        height:700,
                        title: lang.t('Назначить фотографии комплектациям'),
                        dialogClass: 'photolink-dialog',
                    },
                    ajaxOptions: {
                        method: 'POST',
                        data: post_data
                    },
                    afterOpen: function(dialog) {
                        dialog
                            .on('click', data.options.offersPhotoDialog.reset, function() {
                                $(data.options.offersPhotoDialog.offerZone+' option', dialog).prop('selected', false);
                            })
                            .on('click', data.options.offersPhotoDialog.applySelect, function() {
                                applySelection(true, dialog);
                            })
                            .on('click', data.options.offersPhotoDialog.clearSelect, function() {
                                applySelection(false, dialog);
                            })
                            .on('click', data.options.offersPhotoDialog.save, function() {
                                linkPhotoWithOffers(dialog);
                            });
                        $this.trigger('disableBottomToolbar', 'offer-link');
                    },
                    close: function() {
                        $this.trigger('enableBottomToolbar', 'offer-link');
                    }
                });
                
            },
            
            /**
            * Обновляет список комплектаций
            */
            refresh: function(url, post_params, render_type) {
                
                if (!url) url = $(data.options.offers).data('refreshUrl');
                if (!post_params) post_params = [];
                if (!render_type) render_type = 'ext-offers';
                
                post_params.push({
                    name: 'offer_render_type',
                    value:render_type
                });
                
                $.ajaxQuery({
                    url: url,
                    data: post_params,
                    method: 'post',
                    //dataType: 'html',
                    success: function(response) {
                        if (response.success != false) {
                            $('#'+render_type, $this).html(response.html).trigger('new-content');
                            $this.trigger('enableBottomToolbar', 'offer-edit');
                            $this.trigger('enableBottomToolbar', 'offer-checked');
                        }
                    }
                });
                
            },            
            
            /**
            * Добавление пустой копмлектации товару
            * 
            * @var array props - массив характеристик, для добавления
            */
            addOffer: function(props) {
                //Добавляем новую строку в таблицу комплектаций
                var tr = $('<tr class="item new-item" data-id="0">\
                            <td class="chk"></td>\
                            <td class="drag drag-handle"></td>\
                            <td class="title">'+lang.t('Новая комплектация')+'</td>\
                            <td class="barcode"></td>\
                            <td class="amount"></td>\
                            <td class="price"></td>\
                            <td class="actions va-m-c">\
                                <span class="loader"></span>\
                                <a class="offer-del zmdi zmdi-close f-18 m-r-5"></a>\
                            </td>\
                        </tr>');
                
                $(data.options.offers).prepend(tr);
                $(data.options.offers + ' ' + data.options.offerEmptyLine).hide();
                methods.editOffer(0);
            },
            
            /**
            * Закрывает форму редактирования комплектации
            */
            closeOfferEdit: function() {
                var open_offer_id = $(data.options.offerLine + '.now-edit, ' + data.options.offerLine + '.now-multiedit', $this).first().data('id');
                if (open_offer_id) {
                    methods.editOffer(open_offer_id);
                }
            },
            
            /**
            * Редактирование комплектации 
            */
            editOffer: function(offer_id, is_multiedit) {
                //Закрываем форму, если она была открыта раннее.
                var edit_line = $(data.options.offerLine + '[data-id="'+offer_id+'"]', $this);
                var is_opened = edit_line.is('.now-edit') || edit_line.is('.now-multiedit');
                
                //Удаляем строку несозданной комплектации, если таковая была
                if (offer_id>0 || is_opened) $(data.options.offers + ' ' + data.options.newOffer, $this).remove();
                
                $(data.options.offerLine, $this).removeClass('now-multiedit now-edit load');
                $(data.options.offerEditLine, $this).remove();
                $(data.options.offers + ' > tbody > tr', $this).removeClass('nodrag nodrop');

                if (is_opened || !is_multiedit) {
                    $(data.options.offers + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                } 
                
                if (xhr) xhr.abort();
                
                if (is_opened) { //Закрытие режима редактирования
                    $this.trigger('enableBottomToolbar', 'offer-edit');
                    $(data.options.offers + ' ' + data.options.offerEmptyLine).show();
                    return;
                }
                
                $(data.options.offers + ' input[type="checkbox"]', $this).prop('disabled', true);
                $this.trigger('disableBottomToolbar', 'offer-edit');
                
                edit_line.addClass(is_multiedit ? 'now-multiedit' : 'now-edit load');
                
                var product_barcode = $this.closest('.crud-form').find(data.options.barcode).val();
                
                if (is_multiedit) {
                    var url = $this.data('urls').offerMultiEdit;
                    var post_data = getSelectedOffers();
                } else {
                    var url = $this.data('urls').offerEdit;
                    var post_data = {
                        offer_id: offer_id,
                        product_barcode: product_barcode
                    };
                }
                             
                xhr = $.ajaxQuery({
                    url: url,
                    data: post_data,
                    success: function(response) {
                        edit_line.removeClass('load');
                        var edit_wrap = $('<tr class="edit-form no-over">'+
                                            '<td colspan="7">'+
                                                '<div class="bordered"></div>'+
                                            '</td>'+
                                         '</tr>');
                             
                        edit_wrap.data('id', offer_id).find('.bordered').html(response.html);
                        edit_wrap.insertAfter(edit_line).trigger('new-content');
                        
                        //Отключаем сортировку
                        $(data.options.offers + ' > tbody > tr').addClass('nodrag nodrop');
                        
                        //Включаем обработчик блока цен
                        edit_wrap.on('click.offer', '.oneprice', function() {
                            $(this).siblings('.vtable').toggle(!this.checked);
                            $(this).siblings('.oneprice-data').toggle(this.checked);
                        })
                    }
                });
                
            
            },
            
            //Многомерные комплектации
            /**
            * Показывает только актуальные характристики
            * 
            */
            showOnlyActualProps: function () {
               //Обновляем список свойств
               //Загружаем значения в список
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function() {
                    fillPropToSelect(this);
               });
                
               //Сначала все характеристики скрыты 
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" option", $this).addClass('hide').prop('disabled',true);
               var props = getActualPropListsIds(); 
               
               //Теперь покажем характеристики у которых тип - список
               for(var i=0;i<props.length;i++){
                  $(data.options.multiOfferWrap+" "+data.options.rowMO+" option[value='"+props[i]+"']", $this).removeClass('hide').prop('disabled',false);
               }
               
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function() {
                   if ($('option:enabled:selected', $(this)).length == 0) { 
                       var count = $(data.options.multiOfferWrap + " " + data.options.rowMO, $this).length;
                       if (count == 1) {
                            $('option:enabled:first', $(this)).prop('selected', true);
                        } else {
                           //Если по какой-то причине выбрана неактивная характеристика, удаляем строку
                           //$(this).closest(data.options.rowMO).remove();
                        }
                   } 

                   //Скроем Группы в списке у которых все элементы скрыты
                   $("optgroup",$(this)).each(function(){
                       $(this).toggle( $('option:enabled', $(this)).length > 0 );
                   });
               });
               
            },
            
            /**
            * Создание многомерных комплектаций из добавленных
            * 
            */
            createComplexs: function() 
            {
               //Покажем актуальные характеристики 
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны 
               
               //Подготим данные для отправки на сервер
               var post_data = [{
                   name: 'product_barcode',
                   value: $this.closest('.crud-form').find(data.options.barcode).val()
               }];
               
               $(data.options.multiOfferWrap+' '+data.options.rowMO+' option:selected', $this).each(function() {
                   var product_form = $this.closest('.crud-form');
                   var multioffer_title = $(this).closest(data.options.rowMO).find('.key input').val();
                   
                   post_data.push({
                       name: 'prop['+$(this).val()+'][title]',
                       value: multioffer_title
                   });

                   let selected_value_selector = '.property-container .property-item[data-property-id="' + $(this).val() + '"] input.h-val:checked,' +
                                                '.property-container .property-item[data-property-id="' + $(this).val() + '"] .property-type-big-list_selected-item-checkbox';
                   
                   $(selected_value_selector, product_form).each(function() {
                       post_data.push({
                           name: $(this).attr('name'),
                           value:$(this).val()
                       });
                   });
               });
               
               //Обновляем сведения
               methods.refresh($this.data('urls').offerMakeFromMultioffer, post_data, 'all-offers');
               
               //Спрячем кнопку
               $(data.options.crAutoOffers,$this).prop('checked',false);
               toggleCreateMOffers();
               
               return false;
            },
            /**
            * Добавляет уровень многомерной комплектации
            * 
            */
            addMultiOfferLevel: function (){
                //Клонируем строку уровня 
               var offerLevel = $(tmpl('multioffer-line', {}));

               var row = $(offerLevel);
               $(data.options.multiOfferWrap+" "+data.options.offersBody, $this).append(row);
               fillPropToSelect(row.find('select'));
               
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны               
                //Получаем следующую характеристику                                  
               var rows = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this);
               var selected = $("option:enabled:selected", rows),
                   next_prop = selected.val(), 
                   next_title = selected.text();
                                  
               $("option:enabled", rows).each(function() {
                    if ( !$(this).is(':selected') ) {
                        next_prop = $(this).val();
                        next_title = $(this).text();
                        return false;
                    }
               });               
               
               row.find('select').val(next_prop);
               row.find('input').val(next_title);
               
               checkMODelete();               //Проверка на показ кнопки удалить  
               checkMOAdd();                  //Проверка на показ кнопки добавить
               remakeMONames();               //Переформирование имен в нужном порядке 
               return false;
            },
            
            /**
            * Удаляет уровень многомерной комплектации
            * 
            */
            delMultiOfferLevel: function (){
               var wrap = $(this).closest(data.options.rowMO);
               $(wrap).remove();
               $.messenger('hideAll');
               remakeMONames();
               checkMODelete();
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны
               checkMOAdd();
               return false;
            }
        }
        
        //private
        /**
        * Постит данные из виртуальной формы
        */
        var submitVirtualForm = function(e) 
        {
            var form = $(this).closest(data.options.virtualForm);
            var real_form = $('<form />');
            form.find('input, select, textarea').each(function() {
                var element = $(this).clone();
                if (element.is('select,textarea')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(real_form);
            });
            
            var params = real_form.serializeArray();
            
            if (form.data('hasValidation')) {
                methods.postForm(form, params);
            } else {
                methods.refresh(form.data('action'), params);
            }
            
            e.preventDefault();
        },        
        
        /**
        * Возвращает данные по ценам продукта
        * 
        */
        getProductPriceData = function() {
            var virtual_form = $('<form />');
            
            $(data.options.productPrice).each(function() {
                var element = $(this).clone();
                if (element.is('select')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(virtual_form);
            });
            
            return virtual_form.serializeArray();
        },
        
        /**
        * Возвращает данные по артикулу продукта
        */
        getProductBarcode = function() {
            var product_barcode = $this.closest('.crud-form').find(data.options.barcode).val();
            return [{name:'barcode', value:product_barcode}];
        },
        
        /**
        * Возвращает данные по штрихкоду продукта
        */
        getProductSku = function() {
            var product_sku = $this.closest('.crud-form').find(data.options.sku).val();
            return [{name:'sku', value:product_sku}];
        },
        
        /**
        * Возвращает данные основной комплектации
        * 
        */
        getMainOfferData = function() {
            var virtual_form = $('<form />');
        
            $(data.options.mainOffer, $this).find('input, select').each(function() {
                var element = $(this).clone();
                if (element.is('select')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(virtual_form);
            });
            
            return virtual_form.serializeArray();
        },  
        
        /**
        * Вызывается при добавлении, удалении фото, сортировке фото товара
        */
        onChangePhoto = function(e, photo_block) 
        {
            $(data.options.offersImagesRow, $this).each(function() {
                var main_offer_selected_photos = [];
                var main_offer_photo_line = $(this);
                $('.act', main_offer_photo_line).each(function() {
                    main_offer_selected_photos.push($(this).data('id'));
                });
                main_offer_photo_line.empty();
                
                //Обновим фотографии у основной комплектации
                $(photo_block).find(data.options.productPhotos + ' ' + data.options.productOnePhoto).each(function() {
                    var mini_photo = $('<a>')
                                        .attr('data-name', 'offers[main][photos_arr][]')
                                        .attr('data-id', $(this).data('id'))
                                        .append( $('<img />').attr('src', $(this).find('.image').data('smallImage') ));
                                        
                    main_offer_photo_line.append(mini_photo);

                    if (main_offer_selected_photos.indexOf($(this).data('id')) != -1) {
                        mini_photo.click();
                    }                
                });            
            });
        },
        
        /**
        * Возвращает отмеченные комплектации
        */
        getSelectedOffers = function() 
        {
            var items = [];
            $(data.options.offers + ' input[name][type="checkbox"]:checked').each(function() {
                items.push({
                     name: $(this).attr('name'),
                     value: $(this).val()
                });
            });
            return items;
        },
        
        /**
        * Наполняет SELECT значениями свойств
        */
        fillPropToSelect = function(select) 
        {
            select = $(select);            
            if ( $('.p-proplist > *').length > 1 ) {
                var before_val = select.val();
                select.empty().html( $('.p-proplist > *').clone() ).find('[value="new"]').remove();
                select.val(before_val);
            }
        },
         
        //Многомерные комплектации
        /**
        * Переключает состояния кнопки создать
        */
        toggleCreateMOffers = function (){
            $(data.options.createComplexs,$this).toggle();
        },
        /**
        * Событие при изменении выбора в селекторе уровня многомерных компл.
        * 
        */
        onMultiOfferLevelChange = function (){
            $(this).data('prop-id',$(this).val()); //Выставляем доп информацию
        }, 
        /**
        * Переключение между вкладками
        */
        tabChange = function(){  
            var props     = getActualPropListsIds(); 
            var props_cnt = props.length;
            
            if (props_cnt==0){
                $(data.options.multiOfferWrap,$this).hide();
                $(data.options.useMultiOffer,$this).prop('checked',false);
            }else{
                //Проверим существование, соотвествует ли количество 
                //строк с уровнями, включённым списковым характеристикам 
                var rows = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this);
                var rows_cnt = rows.length;

                if (rows_cnt > props_cnt){ //Если больше чем надо, то удалим строки с уровнями
                    rows.each(function() {
                        var property_id = $(this).find('option:selected').val();
                        if (props.indexOf(parseInt(property_id)) == -1) $(this).remove();
                    });
                }
            }
            methods.showOnlyActualProps(); 
            checkMOAdd();  
            checkMODelete();
            
            if (!$(data.options.useMultiOffer,$this).prop('checked')){
               $(data.options.multiOfferWrap,$this).hide(); 
            }

            if (props.length==0){
                $(data.options.multiOfferWrapId,$this).addClass(data.options.hide); 
                $(data.options.useMultiOffer,$this).prop('checked',false); 
            }else{
                $(data.options.multiOfferWrapId,$this).removeClass(data.options.hide); 
            }                                 
        },
        /**
        * Получает в виде массива актуальные id списковых характеристик, у которых есть отмечанные галочки
        * беря их из вкладки характеристики
        */
        getActualPropListsIds = function(){
           var props = new Array();
           //Соберём сведения по характеристикам с галочками
           $(".property-container .property-item").each(function(){
               if ($('.item-val input[type="checkbox"]:checked, .property-type-big-list_selected-item',$(this)).length>0){
                  var i = props.length; 
                  
                  props[i] = $(this).data('property-id'); 
               } 
           }); 
           
           return props;
        },
        /**
        * Переформировывает атрибут name у уровней комплектаций
        * Проставляет name по по порядку
        */
        remakeMONames = function(){
           var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).length; 

           //Галка "с фото" у комплектаций
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='radio']",$this).each(function(i){
               $(this).val(i+1);
           });
           //Название комплектации
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='text']",$this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][title]');
           });
           //Выпадающий список у многомерных комплектаций
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][prop]');
           });
           
           $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).removeClass('error');
        },
        /**
        * Проверяет можно ли добавлять уровень комплектации
        */
        checkMOAdd = function(){
            var props     = getActualPropListsIds(); 
            var cnt       = props.length; //Количество характеристик
            var rows_cnt  = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; //Количество созданных уровней
            if (cnt>rows_cnt){
               $(data.options.multiOfferWrap+" "+data.options.addLevel).show(); 
            }else{
               $(data.options.multiOfferWrap+" "+data.options.addLevel).hide();  
            }    
        },
        /**
        * Проверяет право на удаление многомерных комплектаций
        */
        checkMODelete = function(){
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; 
            if (cnt>1){
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).show();
            }else{
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).hide();
            }
            return false;
        },
        /**
        * Показывает окно многомерных комплектаций
        * Клик на галочке мн. компл.
        */
        showMOffers = function(){
            openMOffers();
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; 
            if (cnt==0){
                methods.addMultiOfferLevel(); //Добавим нулевой уровень если требуется
            }
            if ($(this).prop('checked')){
               $(data.options.multiOfferWrap,$this).show(); 
            }else{
               $(data.options.multiOfferWrap,$this).hide();  
            }
        },
        /**
        * Открывает окно многомерных комплектаций
        */
        openMOffers = function(){
            $(data.options.multiOfferWrap,$this).show();
            $(this).hide();
            methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны
            return false;
        }
          
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });
}
})(jQuery);