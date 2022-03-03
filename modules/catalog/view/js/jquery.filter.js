/**
* Плагин, инициализирующий работу фильтров
*/
(function( $ ){
    $.fn.productFilter = function( method ) {
        var defaults = {
            targetList             : '#products',     //Селектор блока, в котором отображаются товары
            form                   : '.filters',      //Селектор формы которая будет отправляться
            submitButton           : '.submitFilter', //Селектор кнопки отправки формы
            cleanFilter            : '.cleanFilter',  //Селектор кнопки очистки фильтра
            inputFromTo            : '.fromto',       //Селектор инпутов числовой характеристики
            
            //Для фильтра множественного выбора
            multiSelectRemoveProps : '.removeBlockProps',     //Селектор кнопки, которая убирает все выделенные характеристики в обном блоке
            multiSelectBlock       : '.typeMultiselect',      //Селектор обёртки множественного фильтра
            multiSelectInsertBlock : '.propsContentSelected', //Селектор обёртки всех строк с выбором характеристик отмеченным
            multiSelectRowsBlock   : '.propsContent',         //Селектор обёртки всех строк с выбором характестик не отмеченным
            multiSelectRow         : 'li',                    //Селектор обёртки одного фильтра
            multiHideClass         : 'hidden',                //Селектор класса для скрытия элментов в блоке
        },
        args = arguments,
        timer;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('productFilter');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data                    = {}; $this.data('productFilter', data);
                    data.options            = $.extend({}, defaults, initoptions);
                    data.options.baseUrl    = $(data.options.form).attr('action');
                    data.options.cleanState = $(data.options.form).serializeArray();

                    $(window).on('popstate', returnPageFilterFromFilter); //Функция возврата на предыдущую страницу по ajax через браузер
                    $(data.options.cleanFilter).click(methods.cleanFilters);
                    $('input[type="text"], input[type="hidden"], select', $this).each(function() {
                        $(this).data('lastValue', $(this).val());
                    });
                    
                    bindChanges();
                    changeMultiSelectCheckedRowsPosition();

                    $(data.options.submitButton).hide();
                },

                applyFilters: function(e, noApply) {
                    if (noApply) return false;
                    correctIntervals();
                    var newValues = $(data.options.form).serializeArray();
                    //Исключаем из фильтра элементы, неустановленные элементы
                    var readyValues = [];

                    //Добавляем поисковую фразу

                    if ($this.data('queryValue')) {
                        if ($this.data('queryValue') != 'undefined' && $this.data('queryValue').length) {
                            readyValues.push({
                                name: 'query',
                                value: $this.data('queryValue')
                            });
                        }
                    }

                    for(var key in newValues) {
                        var field = $('[name="' +  newValues[key].name + '"][data-start-value]', $this);
                        if (!field.length || field.data('startValue') != newValues[key].value) {
                            readyValues.push(newValues[key]);
                        }
                    }

                    //Сменим позиции мульти выбора у выбранных элементов
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        changeMultiSelectCheckedRowsPosition();
                    }, 300);

                    // выполним запрос
                    methods.queryFilters(readyValues);
                    return false;
                },

                /**
                 * Запрос результата применения фильтров
                 *
                 * @param newValues - массив объектов запроса
                 * @param updateHistoryState - Флаг указывающий на то нужно ли заносить в историю изменение адреса и заносить туда фильтры
                 */
                queryFilters: function(newValues, updateHistoryState = true){
                   $this.addClass('inLoading');
                   $(data.options.cleanFilter, $this).toggleClass('hidden', newValues.length == 0);

                   $.ajax({
                       url: data.options.baseUrl,
                       dataType:'json',
                       data: newValues,
                       success: function(response) {
                           var new_content = $(response.html);
                           $(data.options.targetList).replaceWith(new_content);
                           var url = decodeURIComponent(response.new_url);

                           // заносим ссылку в историю
                           if (updateHistoryState){
                               history.pushState(newValues, null, url);
                           }

                           //> зависимые фильтры
                           if(typeof response.filters_allowed_sorted !== "undefined"){
                               var allow_filters = Object.entries(response.filters_allowed_sorted);

                               if(allow_filters !== false) {
                                   allow_filters.forEach(function(filter){
                                       Object.entries(filter[1]).forEach(function(filter_val){
                                           //если есть, то включим
                                           var input_filter = $('input[name="pf['+filter[0]+'][]"][value="'+filter_val[0]+'"]');
                                           var input_bfilter = $('input[name="bfilter['+filter[0]+'][]"][value="'+filter_val[0]+'"]');
                                           if(filter_val[1] === false) {
                                               input_bfilter.parent().addClass('disabled-property');
                                               input_filter.parent().addClass('disabled-property');
                                           } else {
                                               input_bfilter.parent().removeClass('disabled-property');
                                               input_filter.parent().removeClass('disabled-property');
                                           }
                                       });
                                   });
                               }
                           }
                           //< зависимые фильтры

                           new_content.trigger('new-content');
                           $this.removeClass('inLoading');
                           $this.trigger('filters.loaded');
                       }
                   });
                },

                cleanFilters: function(e, noApply) {
                    $('input[type="text"], input[type="number"], input[type="hidden"], select', $this).each(function() {
                        $(this).val( $(this).data('startValue') ? $(this).data('startValue') : "" ).trigger('change', true);
                    });
                    $('input[type="checkbox"]', $this).prop('checked', false).trigger('change', true);
                    if (!noApply) methods.applyFilters();
                    //Сменим позиции мульти выбора у выбранных элементов
                    changeMultiSelectCheckedRowsPosition();
                    return false;
                }
            };

            //private
            /**
            * Возвращается через AJAX на страницу с прошлым фильтром, если таковая имеется в истории браузера.
            *
            */
            var returnPageFilterFromFilter = function()
            {
                methods.cleanFilters(null, true);
                var params = history.state ? history.state : [];
                $(params).each(function(i, value) {
                    setFilterParam(value);
                });
                methods.queryFilters(params, false);
            };

            /**
            * Устанавливает в HTML форме фильтров значения из переданного объекта
            * @param filter_obj - объект со значниями фильтра
            */
            var setFilterParam = function(filter_obj){
                var filter = $("[name='"+filter_obj.name+"']",data.options.form);
                tagName    = filter[0].tagName.toLowerCase();
                switch(tagName){
                    case "select":
                        $('option',filter).prop('selected',false);
                        $("select[name='"+filter_obj.name+"'] option[value='"+filter_obj.value+"']").prop('selected',true);
                        break;

                    case "input":
                        if (filter.length>1){ //Если несколько объектов подходящих(checkbox)
                            //То выберем нужный
                            filter = $("[name='"+filter_obj.name+"'][value='"+filter_obj.value+"']",data.options.form);
                        }
                        var type = filter.attr('type').toLowerCase();

                        switch (type){
                            case "checkbox":    //checkbox
                                filter.prop('checked', true);
                                break;

                            default:   //Текстовое поле
                                filter.val(filter_obj.value);       // Если просто input
                                break;
                        }
                        break;

                    default:
                        filter.val(filter_obj.value);
                        break;
                }
                filter.trigger('change', true);
            };


            /**
            * Меняет позиции выбранным элементам в блоках с мультивыбором
            *
            */
            var changeMultiSelectCheckedRowsPosition = function (){
               // Если блоки есть
               $(data.options.multiSelectBlock, $this).each(function(){
                   var have_checked = false;
                   var block        = $(this);
                   $('input', $(this)).each(function(){
                        var wrapperLi = $(this).closest(data.options.multiSelectRow); //Обёртка
                        if ($(this).prop('checked')){ //Если установлена галочка
                           have_checked = true;
                           wrapperLi.appendTo($(data.options.multiSelectInsertBlock, block));
                        }else{ //Если характеристика не выбрана, то проверим где-то она находится и поместим обратно в нужный блок, если нужно
                           if ($(this).closest(data.options.multiSelectInsertBlock).length){
                               wrapperLi.prependTo($(data.options.multiSelectRowsBlock, block));
                           }
                        }
                   });
                   //Переключим элементы для отображения
                   toggleMultiSelectHideElements(block, have_checked);
               });
            };

            /**
            * Отображает или прячет элементы в блоке с выбором характеристик в блоке
            *
            * @param block - объект блока с характеристиками
            */
            var toggleMultiSelectHideElements = function (block, have_checked){
                if (have_checked){
                   $(data.options.multiSelectInsertBlock, block).removeClass(data.options.multiHideClass);
                   $(data.options.multiSelectRemoveProps, block).removeClass(data.options.multiHideClass);
                }else{
                   $(data.options.multiSelectInsertBlock, block).addClass(data.options.multiHideClass);
                   $(data.options.multiSelectRemoveProps, block).addClass(data.options.multiHideClass);
                }
            };

            /**
            * Снимает все выбранные характеристики в одном блоке
            *
            * @param block - объект блока с характеристиками
            */
            var cleanBlockProps = function (){
                var block = $(this).closest(data.options.multiSelectBlock);
                $("input[type='checkbox']", block).prop('checked', false).trigger('change', true);
                methods.applyFilters();
                return false;
            };

            /**
            * Фиксирует факт изменения параметров в фильтрах и вызывает метод applyFilters
            */
            var bindChanges = function() {
                $(data.options.form).submit(methods.applyFilters);
                $('select, input[type="checkbox"], input[type="hidden"]', $this).change(methods.applyFilters);
                $(data.options.multiSelectRemoveProps, $this).on('click', cleanBlockProps);
                $('input[type="text"], input[type="number"]', $this).keyup(function(e) {
                    clearTimeout(this.keyupTimer);
                    if (e.keyCode == 13) {
                        return;
                    }
                    this.keyupTimer = setTimeout(function() {
                         methods.applyFilters();
                    }, 500);
                });
            };

            /**
             * Исправляет предельные значения числовых фильтров
             */
            var correctIntervals = function() {
                $(data.options.inputFromTo, data.options.form).each(function(){
                    var value = ($(this).val() == '') ? 0 : parseFloat($(this).val());
                    var min = parseFloat($(this).attr('min'));
                    var max = parseFloat($(this).attr('max'));
                    if (value == '') {
                        $(this).val($(this).data('startValue'));
                    } else if (value < min) {
                        $(this).val($(this).attr('min'));
                    } else if (value > max) {
                        $(this).val($(this).attr('max'));
                    }
                    $(this).change();
                });
            };
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

})( jQuery );


$(function() {
    $('.filterSection').productFilter();
});