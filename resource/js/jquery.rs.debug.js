/**
* Плагин инициализирует работу контекстных меню в режиме редактирования
*
* @author ReadyScript lab.
*/
(function($){

    /**
     * Контекстное меню отладки
     *
     * @param method
     * @return {number|*}
     */
    $.fn.debugContextMenu = function(method) {
        var defaults = {
            moduleWrap: '.module-wrapper',
            menuId: 'debug-context-box',
            correction: {
                x: -10,
                y: 15
            }
        },
        args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('debugContext');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('debugContext', data);
                    data.options = $.extend({}, defaults, initoptions);
                    data.items = $this.data('debugContextmenu');
                    if (data.items.length) {
                        $this.on('contextmenu.debugContext', showContextMenu);
                    }
                },
                hide: function() {
                    $('#'+data.options.menuId).hide();
                    var menuData = $('#'+data.options.menuId).data('debugContextMenu');
                    if (menuData) {
                        menuData.module.removeClass('hover');
                    }
                }
            };

            var showContextMenu = function(e) {
                methods.hide();
                var module = $(e.currentTarget).closest(data.options.moduleWrap).addClass('hover');

                var $menu = getContextMenuDiv(data.items, module);
                $menu.css({
                    top:e.pageY + data.options.correction.y,
                    left:e.pageX + data.options.correction.x
                }).data('debugContextMenu', {module:module}).trigger('showContextMenu');
                e.preventDefault();
                e.stopPropagation();
            },

            getContextMenuDiv = function(items, module_wrapper) {
                //Контейнер, который следует обновить, после выполнения действия
                var updateContainer = $('.module-content.updatable', module_wrapper);

                var $menu = $('#'+data.options.menuId);
                if (!$menu.length) {
                    $menu = $('<div id="'+data.options.menuId+'">'+
                                  '<div class="debug-context-back" />'+
                                  '<i class="debug-context-corner" />'+
                                  '<ul class="debug-context-items" />'+
                              '</div>').appendTo('body');

                    $(module_wrapper).on('click.debugContext', function(e){e.stopPropagation();});
                    $('html').on('click.debugContext', function(e) {
                        methods.hide();
                    })
                    .on('keypress.debugContext', function(e) {if (e.keyCode == 27 ) methods.hide();} );
                }
                var $ul = $menu.show().find('.debug-context-items').empty();
                for(var i in items) {
                    var $li = $('<li />').append(
                        $('<a />').attr(items[i].attributes).html(items[i].title)
                            .bind('click', function() {
                                methods.hide();
                            })
                            .data('crudOptions', {updateElement: updateContainer, ajaxParam:{noUpdateHash: true}})
                    );
                    $ul.append($li);
                }
                $menu.trigger('new-content');
                return $menu;
            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

    $.adaptTop = function() {
        var checkWidth = function() {
            if ($(document).width() <= 760 && !$('#debug-top-block').hasClass('debug-mobile')) {
                $('#debug-top-block').addClass('debug-mobile');
            }
            if ($(document).width() > 760 && $('#debug-top-block').hasClass('debug-mobile')) {
                $('#debug-top-block').removeClass('debug-mobile');
            }
        };
        $(window).on('resize.detectMedia', checkWidth);
        checkWidth();
    };

    /**
     * Инициализирцет обертку блока конструктора, навешивая нужные стили
     *
     * @param $module_wrapper - ссылка на текущий объект обертки
     */
    function initConstructorWrapperContent($module_wrapper)
    {
        let $module_content = $('.module-content', $module_wrapper);

        //Посмотрим, а есть ли у нашел родителя класс для манипуляций
        let moduleParent = $module_wrapper.parent(); //Родитель для блока
        if (moduleParent.hasClass('gridblock')){ //Пропустим обёртку вспомогательного класса
            moduleParent = moduleParent.parent();
        }
        if ($module_wrapper.hasClass('can-drag') && !moduleParent.hasClass('section-content')){ //Добавим класс, который отсутствует у родителя
            moduleParent.addClass('section-content');
        }

        //Делаем перетаскиваемыми наборы инструментов на панели блока
        $('.module-tools', $module_wrapper).draggable({
            handle: '.dragblock'
        });

        //Применим свойства к обертке
        let $item = $module_content.find('> *:first');
        let keys  = ['display', 'float'];

        $.each(keys, function(k, v) {
            let new_val = $item.css(v);

            if (v == 'display' && (new_val == 'inline' || new_val == 'none')){
                new_val = 'inline-block';
            }
            if (v == 'display' && new_val == 'flex'){
                new_val = 'block';
            }
            if ($item[0] && $item[0].tagName == 'SCRIPT'){
                new_val = 'block';
            }
            $module_wrapper.css(v, new_val);
        });

        //Если модуль создан в конструкторе сайта, то правильно поставил float
        if ($module_wrapper.hasClass('can-drag')){

        }

        if ($item.css('position') == 'absolute') {
            let newStyle = {
                position     : 'absolute',
                marginTop    : $item.css('margin-top'),
                marginBottom : $item.css('margin-bottom'),
                marginLeft   : $item.css('margin-left'),
                marginRight  : $item.css('margin-right'),
                left         : $item.css('left'),
                top          : $item.css('top')
            },
            itemStyle = {
                position:'static',
                marginLeft:   0,
                marginRight:  0,
                marginTop:    0,
                marginBottom: 0
            };
            $item.css(itemStyle);
            $module_wrapper.closest('.block-section-wrapper').css(newStyle);
        }
    }

    /**
     * Удаляет события по отслеживанию показа плюсиков
     * @param {String} val - класс
     */
    function removeEventsForShowPluses(val)
    {
        $('body').off('mouseenter.showPlusesOver', val)
                 .off('mouseleave.showPlusesOut', val);
        $(val).off('mousemove.showPlusesMove');
        $('.debug-add-to-position', $(val)).removeClass('highlight');
    }

    /**
     * Добавляет необходимые события и уберает ненужные на блоки, где надо показывать кнопки добавления блоков (до/после)
     *
     * @param {String} type - классы блоков где показывать
     */
    function addMouseEventToShowPlusesForBlocks(type)
    {
        let needed_blocks; //блоки, которые нужно обрабатывать
        let modules_blocks  = [".module-wrapper.can-drag"];
        let sections_blocks = [".block-row-wrapper", ".block-section-wrapper"];
        let body = $('body');
        //Удалим обработчики
        modules_blocks.forEach(removeEventsForShowPluses);
        sections_blocks.forEach(removeEventsForShowPluses);

        switch(type){
            case "containers": //Контейнеры
                return;
            case "sectionsandrows": //Секции и строки
                needed_blocks = sections_blocks;
                break;
            case "blocks": //Блоки
            default:
                needed_blocks = modules_blocks;
                break;
        }

        needed_blocks.forEach((needed_block) => {
            /**
             * Навесимся на наведение на блоки
             */
            body.on('mouseenter.showPlusesOver', needed_block, function(e){

                let blockHeight = $(this).outerHeight();
                let delta = blockHeight * 0.1;
                if (delta < 10) delta = 10;
                if (delta > 55) delta = 55;

                //Определим координаты для проверки
                let coorTop    = $(this).offset().top;
                let coorBottom = $(this).offset().top + blockHeight;
                let pluses_top    = $("> .debug-add-to-position.top", $(this)); //Верхний плюс
                let pluses_bottom = $("> .debug-add-to-position.bottom", $(this)); //Нижний плюс
                $(this).on('mousemove.showPlusesMove', function(event){ //Покажем нужный плюсик
                    pluses_top.removeClass('highlight');
                    pluses_bottom.removeClass('highlight');
                    let mousePos = event.pageY; //Положение по оси Y
                    //Диапозон в 60 пикселей при ближения
                    //Верхний плюс
                    let coorTopCheck1 = coorTop - delta;
                    let coorTopCheck2 = coorTop + delta;

                    if (mousePos > coorTopCheck1 && mousePos < coorTopCheck2){ //Добавим верхнему плюсу событие
                        pluses_top.addClass('highlight');
                    }
                    //Нижний плюс
                    let coorBottomCheck1 = coorBottom - delta;
                    let coorBottomCheck2 = coorBottom + delta;
                    if (mousePos > coorBottomCheck1 && mousePos < coorBottomCheck2){ //Добавим верхнему плюсу событие
                        pluses_bottom.addClass('highlight');
                    }
                });
            })
            /**
             * Навесимся когда мы за блоком блоки
             */
            .on('mouseleave.showPlusesOut', needed_block, function(){
                $(this).off('mousemove.showPlusesMove', needed_block);
                $(".debug-add-to-position", $(this)).removeClass('highlight');
            });
        });
    }

    /**
     * Расширим jquery sortable, чтобы добиться нужного нам поведения для перемещаемого мышкой блока, т.к. он пытается брать координаты наведения изходя из родителя
     * Для этого переделаем методы расчета
     */
    $.widget( "constructor.constructorSortable", $.ui.sortable, {
        /**
         * Генерируем позицию заглучши перетаскиваемого элемента
         * @param event
         * @return {{top: number, left: number}}
         * @private
         */
        _generatePosition: function(event) {

            var top, left,
                o = this.options,
                pageX = event.pageX,
                pageY = event.pageY,
                scroll = this.cssPosition === "absolute" && !(this.scrollParent[0] !== this.document[0] && $.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent, scrollIsRootNode = (/(html|body)/i).test(scroll[0].tagName);

            // This is another very weird special case that only happens for relative elements:
            // 1. If the css position is relative
            // 2. and the scroll parent is the document or similar to the offset parent
            // we have to refresh the relative offset during the scroll so there are no jumps
            if(this.cssPosition === "relative" && !(this.scrollParent[0] !== this.document[0] && this.scrollParent[0] !== this.offsetParent[0])) {
                this.offset.relative = this._getRelativeOffset();
            }

            /*
             * - Position constraining -
             * Constrain the position to a mix of grid, containment.
             */

            if(this.originalPosition) { //If we are not dragging yet, we won't check for options

                if(this.containment) {
                    if(event.pageX - this.offset.click.left < this.containment[0]) {
                        pageX = this.containment[0] + this.offset.click.left;
                    }
                    if(event.pageY - this.offset.click.top < this.containment[1]) {
                        pageY = this.containment[1] + this.offset.click.top;
                    }
                    if(event.pageX - this.offset.click.left > this.containment[2]) {
                        pageX = this.containment[2] + this.offset.click.left;
                    }
                    if(event.pageY - this.offset.click.top > this.containment[3]) {
                        pageY = this.containment[3] + this.offset.click.top;
                    }
                }

                if(o.grid) {
                    top = this.originalPageY + Math.round((pageY - this.originalPageY) / o.grid[1]) * o.grid[1];
                    pageY = this.containment ? ( (top - this.offset.click.top >= this.containment[1] && top - this.offset.click.top <= this.containment[3]) ? top : ((top - this.offset.click.top >= this.containment[1]) ? top - o.grid[1] : top + o.grid[1])) : top;

                    left = this.originalPageX + Math.round((pageX - this.originalPageX) / o.grid[0]) * o.grid[0];
                    pageX = this.containment ? ( (left - this.offset.click.left >= this.containment[0] && left - this.offset.click.left <= this.containment[2]) ? left : ((left - this.offset.click.left >= this.containment[0]) ? left - o.grid[0] : left + o.grid[0])) : left;
                }
            }

            return {
                top: (
                    pageY -	10 -									//Изменено				// The absolute mouse position
                    this.offset.relative.top	-											// Only for relative positioned nodes: Relative offset from element to offset parent
                    this.offset.parent.top +												// The offsetParent's offset without borders (offset + border)
                    ( ( this.cssPosition === "fixed" ? -this.scrollParent.scrollTop() : ( scrollIsRootNode ? 0 : scroll.scrollTop() ) ))
                ),
                left: (
                    pageX - 10 -									//Изменено			    // The absolute mouse position
                    this.offset.relative.left	-											// Only for relative positioned nodes: Relative offset from element to offset parent
                    this.offset.parent.left +												// The offsetParent's offset without borders (offset + border)
                    ( ( this.cssPosition === "fixed" ? -this.scrollParent.scrollLeft() : scrollIsRootNode ? 0 : scroll.scrollLeft() ))
                )
            }
        },
        /**
         * Отлов пересечения курсора
         * @param item
         * @return {*}
         * @private
         */
        _intersectsWithPointer: function(item) {

            var isOverElementHeight = (this.options.axis === "x") || this._isOverAxis(this.positionAbs.top, item.top, item.height), //Изменено
                isOverElementWidth = (this.options.axis === "y") || this._isOverAxis(this.positionAbs.left, item.left, item.width), //Изменено
                isOverElement = isOverElementHeight && isOverElementWidth,
                verticalDirection = this._getDragVerticalDirection(),
                horizontalDirection = this._getDragHorizontalDirection();

            if (!isOverElement) {
                return false;
            }

            return this.floating ?
                ( ((horizontalDirection && horizontalDirection === "right") || verticalDirection === "down") ? 2 : 1 )
                : ( verticalDirection && (verticalDirection === "down" ? 2 : 1) );

        },
        /**
         * Отлов пересечения по плоскостям броска
         * @param item
         * @return {*}
         * @private
         */
        _intersectsWithSides: function(item) {

            var isOverBottomHalf = this._isOverAxis(this.positionAbs.top, item.top + (item.height/2), item.height), //Изменено
                isOverRightHalf = this._isOverAxis(this.positionAbs.left, item.left + (item.width/2), item.width),  //Изменено
                verticalDirection = this._getDragVerticalDirection(),
                horizontalDirection = this._getDragHorizontalDirection();

            if (this.floating && horizontalDirection) {
                return ((horizontalDirection === "right" && isOverRightHalf) || (horizontalDirection === "left" && !isOverRightHalf));
            } else {
                return verticalDirection && ((verticalDirection === "down" && isOverBottomHalf) || (verticalDirection === "up" && !isOverBottomHalf));
            }

        }
    });


    /**
     * Меняет между собой контейнеры, отправляя запрос
     *
     *
     * @param {Number} source_id - id исходного контейнера
     * @param {Number} destination_id - id контейнера куда кидаем
     */
    function changeContainerPositions(source_id, destination_id)
    {
        return new Promise((resolve, reject) => {
            let ajax_options = {
                source_id: source_id,
                destination_id : destination_id
            };

            $.rs.loading.show();
            //Отправим запрос на изменение позиции блока
            $.ajaxQuery({
                url : $("#all-containers-wrapper").data('sortUrl'),
                data: ajax_options,
                success: function(response) {
                    resolve(response);
                    $.rs.loading.hide();
                    document.location.reload(true);
                },error: function(e){
                    reject(e);
                    $.rs.loading.hide();
                }
            });
        });
    }

    /**
     * Клонирует контейнер на данную страницу
     *
     * @param {Number} container_id - контейнер, который надо скопировать
     * @param {Number|Boolean} clone_position - позиция на которую вставить
     * @param {Number} current_page - текущая страница
     */
    function cloneContainerToThisPage(container_id, clone_position, current_page)
    {
        return new Promise((resolve, reject) => {
            let ajax_options = {
                page_id: current_page,
                type: clone_position + 1,
                from_container : container_id
            };

            $.rs.loading.show();
            //Отправим запрос на изменение позиции блока
            $.ajax({
                url : $("#all-containers-wrapper").data('cloneUrl'),
                type: "POST",
                data: ajax_options,
                dataType: 'json',
                success: function(response) {
                    resolve(response);
                    $.rs.loading.hide();
                }, error: function(e){
                    reject(e);
                    $.rs.loading.hide();
                }
            });
        });
    }

    /**
     * Инициализирует перемещение события контейнеров
     */
    function initContainerDragEvents()
    {
        let body = $('body');
        let wasEscaped = false; //Была нажата кнопка Escape?
        /**
         * Сортировка контейнеров, перемещение
         */
        $(".drag-container-handler").on('mousedown.startContainerDrag', function(){
            let containerClass = '.container-wrapper';
            let fillClass      = 'fill-puffy';
            let sourceContainer = $(this).closest(containerClass);
            let container = $(sourceContainer);
            container.addClass('source-container destination-container ' + fillClass);
            body.addClass('constructor-drag-mode');
            //Назначим события при пересечении блока
            $(containerClass).on('mouseenter.containerDrag', function(){
                $(this).addClass(fillClass + " hover destination-container");
            }).on('mouseleave.containerDrag', function(){
                $(containerClass).removeClass(fillClass);
                $(this).removeClass('hover destination-container');
            });
            $(containerClass).disableSelection();
            wasEscaped = false;
            body.on('keyup.escKey', function (event) { //Если нажали ESC, прекратим перетаскивание
                if (event.key == 'Escape' || event.keyCode == 27){
                    let containersClass = '.container-wrapper';
                    let containers      = $(containersClass);
                    containers.enableSelection();
                    body.removeClass('constructor-drag-mode');
                    body.off('mousemove.containerDragging');
                    containers.off('mouseenter.containerDrag').off('mouseleave.containerDrag');
                    wasEscaped = true;
                }
            });
        });
        /**
         * Остановка сортировки контейнеров
         */
        body.on('mouseup.startContainerDrag', function () {
            let containersClass = '.container-wrapper';
            let containers      = $(containersClass);

            if (wasEscaped){
                containers.removeClass('hover fill-puffy source-container destination-container');
                body.off('keyup.escKey');
                return;
            }

            let sourceContainer = $(containersClass + '.source-container');
            let distContainer   = $(containersClass + '.destination-container');
            if (sourceContainer.data('containerId') != distContainer.data('containerId')){ //Если контейнер нужно поменять местами
                let parent      = $("#all-containers-wrapper");
                let oldIndex    = sourceContainer.index();
                let newIndex    = distContainer.index();

                if (oldIndex == 0){
                    distContainer.insertBefore($(containersClass + ':eq(0)', parent));
                }else{
                    distContainer.insertAfter($(containersClass + ':eq(' + oldIndex + ')', parent));
                }

                let current_page       = parent.data('pageId'); //Текущая страница
                let container_to_clone = false;//Контейнер, который нужно склонировать
                let clone_position     = false;//Порядковый номер контейнера, который нужно склонировать

                //Если есть смена страницы, то сначала сделаем клон, а потом меняем
                if (sourceContainer.data('pageId') != distContainer.data('pageId')){
                    if (sourceContainer.data('pageId') != current_page){
                        sourceContainer.needChange = true;
                        container_to_clone = sourceContainer;
                        clone_position     = oldIndex;
                    }else if(distContainer.data('pageId') != current_page){
                        distContainer.needChange = true;
                        container_to_clone = distContainer;
                        clone_position     = newIndex;
                    }
                }

                if (container_to_clone){ //Если нужно клонировать контейнер предварительно
                    cloneContainerToThisPage(container_to_clone.data('containerId'), clone_position, parent.data('pageId')).then((response) => {
                        changeContainerPositions(sourceContainer.needChange ? response['copy_id'] : sourceContainer.data('containerId'), distContainer.needChange ? response['copy_id'] : distContainer.data('containerId'));
                        sourceContainer.needChange = null;
                        distContainer.needChange = null;
                    }).catch((e) => {
                        sourceContainer.needChange = null;
                        distContainer.needChange = null;
                        console.error(e);
                    });
                }else{
                    changeContainerPositions(sourceContainer.data('containerId'), distContainer.data('containerId'));
                }
            }
            containers.enableSelection();
            containers.removeClass('hover fill-puffy source-container destination-container');
            body.removeClass('constructor-drag-mode');
            body.off('mousemove.containerDragging');
            containers.off('mouseenter.containerDrag').off('mouseleave.containerDrag');
        });
    }

    /**
     * Убирает события по перемещению контейнеров
     */
    function removeConainerDragEvents()
    {
        $(".drag-container-handler").off('mousedown.startContainerDrag');
        $('body').off('mouseup.startContainerDrag');
    }


    $(function() {

        //Делаем перетаскиваемыми наборы инструментов на панели секции
        $('.section-tools').draggable({
            handle: '.dragblock'
        });
        $('.row-tools').draggable({
            handle: '.dragblock'
        });
        $('.section-tools').draggable({
            handle: '.dragblock'
        });


        let body = $('body');
        //Подсказка на панели блока конструктора
        body.on('click remove', '.debug-hint', function() {
            $(this).tooltip('hide');
        })
        .tooltip({
            trigger: 'hover',
            html: true,
            placement: 'bottom',
            container: $('.admin-style:first')
        });

        //Придаем обрамляющему блоку, некоторые стили от внутреннего контента
        $('.module-wrapper').each(function() {
            initConstructorWrapperContent($(this));
        });

        /**
         * Показывает или скрывает блок лейбл места куда будет перемещение. Используется при сортировке блоков или секций
         *
         * @param {Object} placeholder - объект содержащий ссылку на блок лейбл места
         * @param {String} mode - текущий режим
         */
        function showEmptyOrNotContainerPlaceholder(placeholder, mode = 'blocks')
        {
             let parent; //Родитель
             let children; //Количество детей
             let is_empty; //Признак пустого блока или секции
             switch (mode){
                 case "section":
                     parent = placeholder.parent(); //Родитель
                     children = $('.col-container', parent).length;
                     is_empty = (children == 0);
                     break;
                 case "blocks":
                 default:
                     parent = placeholder.parent(); //Родитель
                     children = parent.children().length;
                     is_empty = (children == 1);
                     break;
             }
             if (is_empty){
                 placeholder.css({
                     width: 30,
                     height: 30,
                     display: 'inline-block'
                 });
             }else{
                 placeholder.css({
                     width: 1,
                     height: 1,
                     display: 'none'
                 });
             }
        }

        /**
         * Определяет предыдущий или следующий элемент у которого нужно показать, что будет перемещение
         * @param {Object} placeholder - объект содержащий ссылку на блок лейбл места
         * @param {Object} item - объект который перемещаем
         * @param {String} mode - тип текущего редактирования
         */
        function showVisualLinesForAppend(placeholder, item, mode = 'blocks')
        {
            let dataIdLabel; //параметр data для проверки на тотже объект
            let dragPlaceHolderTop; //Верхняя полоска
            let dragPlaceHolderBottom; //Нижняя полоска
            switch (mode){
                case "blocks":
                    dataIdLabel           = 'blockId';
                    dragPlaceHolderTop    = '#drag-placeholder-top';
                    dragPlaceHolderBottom = '#drag-placeholder-bottom';
                    break;
                case "section":
                    dataIdLabel           = 'sectionId';
                    dragPlaceHolderTop    = '#drag-placeholder-section-top';
                    dragPlaceHolderBottom = '#drag-placeholder-section-bottom';
                    break;
                case "rows":
                    dataIdLabel           = 'sectionId';
                    dragPlaceHolderTop    = '#drag-placeholder-row-top';
                    dragPlaceHolderBottom = '#drag-placeholder-row-bottom';
                    break;
            }
            //Найдем предыдущий элемент
            let prev       = placeholder;
            let prev_found = false;
            while(!prev_found){
                prev = prev.prev();
                if (!prev.length){
                    prev_found = true;
                    continue;
                }
                if (prev.hasClass('sortable-placeholder') || (prev.data(dataIdLabel) == item.data(dataIdLabel))){
                    continue;
                }
                prev_found = true;
            }

            $('.drag-placeholder-bottom').removeClass('open');
            $('.drag-placeholder-top').removeClass('open');

            //Если есть после кого вставить
            if (prev.length){
                prev.find(dragPlaceHolderBottom).first().addClass('open');
            }else{
                //Найдем следущий элемент
                let next = placeholder;
                let next_found = false;
                while(!next_found){
                    next = next.next();
                    if (!next.length){
                        next_found = true;
                        continue;
                    }
                    if (next.hasClass('sortable-placeholder') || (next.data(dataIdLabel) == item.data(dataIdLabel))){
                        continue;
                    }
                    next_found = true;
                }

                if (next.length) {
                    next.find(dragPlaceHolderTop).first().addClass('open');
                }
            }
        }

        /**
         * Действия при старте перетаскивания блока/секции или контейнера
         * @param {Object} item - объект который перетаскиваем
         * @param {Object} helper - вспомогательный объект хранящий место
         */
        function onDragStartClassManagment(item, helper)
        {
            item.show();
            item.addClass('now-dragged');
            item.parent().addClass('hover');
            helper.show();
            body.addClass('constructor-drag-mode'); //Спец класс для эфектов при перетаскивании
        }

        /**
         * Действия при остановке перетаскивания блока/секции или контейнера
         * @param {Object} item - объект который перетаскиваем
         * @param {Object} helper - вспомогательный объект хранящий место
         */
        function onDragStopClassManagment(item, helper)
        {
            body.append("<div id='dragAllHandler' class='drag-module-block-handlerplace'></div>");//Блок перетаскивания
            body.removeClass('constructor-drag-mode'); //Спец класс для эффектов при перетаскивании
            $('.drag-placeholder-bottom').removeClass('open');
            $('.drag-placeholder-top').removeClass('open');
        }

        /**
         * Показывает пустые секции и строки
         */
        function showEmptyRows()
        {
            let sections = $('.section-row-content');
            sections.removeClass('row-empty');
            sections.each(function(){
                if ($('.col-container', $(this)).length == 0){ //Если элемент путой, то покажем это
                    $(this).addClass('row-empty');
                }
            });
        }

        //Сортировка блоков
        if ($(".debug-mode-switcher .on").length){
            body.append("<div id='dragAllHandler' class='drag-module-block-handlerplace'></div>"); //Блок перетаскивания
        }

        //Сортировка внутри контейнеров строк
        let rows_to_drag = $('.container-rows-content');
        rows_to_drag.constructorSortable({
            connectWith: '.container-rows-content',
            items: '.block-row-wrapper',
            placeholder: 'sortable-placeholder',
            handle: '.drag-row-handler',
            helper: function(){
                return $("#dragAllHandler");
            },
            over: function(event, ui) { //Наведение на блок для перекидывания
                if (ui.sender){ //Если есть место куда кидаем блок
                    rows_to_drag.removeClass('hover');
                    ui.placeholder.parent().addClass('hover');
                }
            },
            sort: function(event, ui) { //Перемещение блока
                //Посмотрим родителя, и если он пустой, то покажем перенос
                showEmptyOrNotContainerPlaceholder(ui.placeholder, 'rows');
                showVisualLinesForAppend(ui.placeholder, ui.item, 'rows'); //Покажем линии вставки
            },
            start: function(event, ui) {
                onDragStartClassManagment(ui.item, ui.helper);
                rows_to_drag.each(function(){
                    let rowsContent = $('.row', $(this));
                    if (rowsContent.children().length == 0){ //Если элемент путой, то покажем это
                        rowsContent.addClass('is-empty');
                    }
                });
                body.on('keyup.escKey', function (event) { //Если нажали ESC, прекратим перетаскивание
                    if (event.key == 'Escape' || event.keyCode == 27){
                        rows_to_drag.constructorSortable('cancel');
                    }
                });
            },
            stop: function(event, ui) {
                onDragStopClassManagment(ui.item, ui.helper);
                body.off('keyup.escKey');
            },
            update: function(event, ui) { //Когда уже элемент изменен
                //Покажем пустые элементы
                rows_to_drag.removeClass('is-empty');
                rows_to_drag.each(function(){
                    $(this).removeClass('now-dragged hover'); //Спец класс для эфектов при перетаскивании
                    $('> .block-row-wrapper', $(this)).removeClass('now-dragged');

                    if ($('> .block-row-wrapper', $(this)).children().length == 0){ //Если элемент путой, то покажем это
                        $(this).addClass('is-empty');
                    }
                });

                let parent = ui.item.closest('[data-container-id]');

                let ajax_options = {
                    id: ui.item.data('sectionId'),
                    parent_id : parent.data('sectionId')
                };

                //Определим куда нужно перемещаться.
                let next = ui.item.next();
                let prev = ui.item.prev();
                ajax_options['position'] = 0;
                if (next.length || prev.length){
                    let position = 'before';
                    let section_id;
                    if (!next.length){
                        section_id = prev.data('sectionId');
                        position = "after";
                    }else{
                        section_id = next.data('sectionId');
                    }
                    ajax_options['position'] = position;
                    ajax_options['section_id'] = section_id;
                }

                $.rs.loading.show();
                //Отправим запрос на изменение позиции блока
                $.ajaxQuery({
                    url : ui.item.data('sortUrl'),
                    data: ajax_options,
                    success: function() {
                        $.rs.loading.hide();
                        document.location.reload(true);
                    }
                });
            }
        });

        //Инициализацмия перетаскивания секций
        let section_to_drag = $('.section-row-content');
        section_to_drag.constructorSortable({
            connectWith: '.section-row-content',
            items: '.col-container',
            placeholder: 'sortable-placeholder',
            handle: '.drag-section-handler',
            helper: function(){
                return $("#dragAllHandler");
            },
            over: function(event, ui) { //Наведение на блок для перекидывания
                if (ui.sender){ //Если есть место куда кидаем блок
                    section_to_drag.removeClass('hover');
                    ui.placeholder.parent().addClass('hover');
                }
            },
            sort: function(event, ui) { //Перемещение блока
                //Посмотрим родителя, и если он пустой, то покажем перенос
                showEmptyOrNotContainerPlaceholder(ui.placeholder, 'section');
                showVisualLinesForAppend(ui.placeholder, ui.item, 'section'); //Покажем линии вставки
            },
            start: function(event, ui) {
                ui.item.myParent = ui.item.parent();
                onDragStartClassManagment(ui.item, ui.helper);
                body.on('keyup.escKey', function (event) { //Если нажали ESC, прекратим перетаскивание
                    if (event.key == 'Escape' || event.keyCode == 27){
                        section_to_drag.constructorSortable('cancel');
                    }
                });
            },
            stop: function(event, ui) {
                onDragStopClassManagment(ui.item, ui.helper);
                section_to_drag.removeClass('puffy hover'); //Спец класс для эфектов при перетаскивании
                $('.col-container').removeClass('now-dragged'); //Спец класс для эфектов при перетаскивании
                body.off('keyup.escKey');
            },
            update: function(event, ui) { //Когда уже элемент изменен
                showEmptyRows();
                let parent = ui.item.closest('.section-row-content');

                let ajax_options = {
                    id: ui.item.data('sectionId'),
                    parent_id : parent.data('sectionId')
                };

                //Определим куда нужно перемещаться.
                let next = ui.item.next();
                let prev = ui.item.prev();
                ajax_options['position'] = 0;
                if (next.length || prev.length){
                    let position   = 'before';
                    let section_id;
                    if (!next.length){
                        section_id = prev.data('sectionId');
                        position = "after";
                    }else{
                        section_id = next.data('sectionId');
                    }
                    ajax_options['position']   = position;
                    ajax_options['section_id'] = section_id;
                }

                $.rs.loading.show();
                //Отправим запрос на изменение позиции блока
                $.ajaxQuery({
                    url : ui.item.closest('.section-row-content').data('sortUrl'),
                    data: ajax_options,
                    success: function() {
                        $.rs.loading.hide();
                    }
                });
            }
        });

        //Сортировка внутри блоков
        let blocks_to_drag = $('.section-content');
        blocks_to_drag.constructorSortable({
            connectWith: '.section-content',
            items: '.module-wrapper.can-drag',
            placeholder: 'sortable-placeholder',
            handle: '.drag-all-block-handler',
            helper: function(){
                return $("#dragAllHandler");
            },
            over: function(event, ui) { //Наведение на блок для перекидывания
                if (ui.sender){ //Если есть место куда кидаем блок
                    $('.block-section-wrapper').removeClass('hover');
                    ui.placeholder.parent().addClass('hover');
                }
            },
            sort: function(event, ui) { //Перемещение блока
                //Посмотрим родителя, и если он пустой, то покажем перенос
                showEmptyOrNotContainerPlaceholder(ui.placeholder, 'blocks');
                showVisualLinesForAppend(ui.placeholder, ui.item, 'blocks'); //Покажем линии вставки
            },
            start: function(event, ui) {
                onDragStartClassManagment(ui.item, ui.helper);
                $('.block-section-wrapper').each(function(){
                    let sectionContent = $('.section-content', $(this));
                    if (sectionContent.children().length == 0){ //Если элемент путой, то покажем это
                        sectionContent.addClass('is-empty');
                    }
                });
                body.on('keyup.escKey', function (event) { //Если нажали ESC, прекратим перетаскивание
                    if (event.key == 'Escape' || event.keyCode == 27){
                        blocks_to_drag.constructorSortable('cancel');
                    }
                });
            },
            stop: function(event, ui) {
                onDragStopClassManagment(ui.item, ui.helper);
                body.off('keyup.escKey');
            },
            update: function(event, ui) { //Когда уже элемент изменен
                //Покажем пустые элементы
                blocks_to_drag.removeClass('is-empty');
                $('.block-section-wrapper').each(function(){
                    $(this).removeClass('now-dragged hover'); //Спец класс для эфектов при перетаскивании

                    let sectionContent = $('.section-content', $(this));
                    if ($(".module-wrapper.can-drag", sectionContent).length == 0){ //Если элемент путой, то покажем это
                        sectionContent.addClass('is-empty');
                    }
                });

                let parent = ui.item.closest('[data-section-id]');

                let ajax_options = {
                    id: ui.item.data('blockId'),
                    parent_id : parent.data('sectionId')
                };

                //Определим куда нужно перемещаться.
                let next = ui.item.next();
                let prev = ui.item.prev();
                ajax_options['position'] = 0;
                if (next.length || prev.length){
                    let position   = 'before';
                    let block_id;
                    if (!next.length){
                        block_id = prev.data('blockId');
                        position = "after";
                    }else{
                        block_id = next.data('blockId');
                    }
                    ajax_options['position'] = position;
                    ajax_options['block_id'] = block_id;
                }

                $.rs.loading.show();
                //Отправим запрос на изменение позиции блока
                $.ajaxQuery({
                    url : ui.item.data('sortUrl'),
                    data: ajax_options,
                    success: function() {
                        $.rs.loading.hide();
                        document.location.reload(true);
                    }
                });
            }
        });

        //Сделаем проверку приближения к элементу плюса на секция, блоках и т.д.
        if (body.hasClass('debug-mode-blocks')){
            addMouseEventToShowPlusesForBlocks('blocks');
        }
        if (body.hasClass('debug-mode-sectionsandrows')){
            addMouseEventToShowPlusesForBlocks('sectionsandrows');
        }
        if (body.hasClass('debug-mode-containers')){
            addMouseEventToShowPlusesForBlocks('containers');
            initContainerDragEvents();
        }

        //Добавляем элемент, который будет служить псевдо тегом body для элементов админисративной панели
        //Используется для избежания конфликтов CSS.
        $('<div class="admin-body admin-style"/>').appendTo('body');
        $.adaptTop();
    });



    $.contentReady(function() {
        $('[data-debug-contextmenu]', this).debugContextMenu();
        $('.debug-mode-switcher .toggle-switch').off();
        $('.debug-mode-switcher .toggle-switch').on($.rs.clickEventName, function() {
            $.ajaxQuery({
                url: $(this).data('url'),
                success: function() {
                    location.reload();
                }
            });
        });

        /**
         * Переключение видов отладки в режиме отладки
         */
        $('.rs-toggle-debug-modes a').off().on($.rs.clickEventName, function () {
            let $this = $(this);
            $.ajaxQuery({
                url: $(this).attr('href'),
                success: function() {
                    let bodyClass = $this.data('bodyClass');
                    let body = $('body');
                    body.removeClass('debug-mode-blocks debug-mode-sectionsandrows debug-mode-containers');
                    body.addClass("debug-mode-" + bodyClass);

                    let icon = $("> i", $this.closest('.rs-toggle-debug-modes'));
                    icon.removeClass('rs-icon-debug-blocks rs-icon-debug-sectionsandrows rs-icon-debug-containers');
                    icon.addClass('rs-icon-debug-' + bodyClass);

                    //Произведем необходимые действия при переключении
                    switch(bodyClass) {

                        case "blocks":
                            $(".section-row-content.row-empty").each(function(){
                                let emptyRow = $(this);
                                if (emptyRow.children().length == 0 && !$(this).hasClass('is-row')){
                                    emptyRow.removeClass('section-row-content row-empty');
                                    emptyRow.addClass('section-content is-empty');
                                }
                            });
                            removeConainerDragEvents();
                            break;

                        case "sectionsandrows":
                            $(".section-content.is-empty").each(function(){
                                let emptyRow = $(this);
                                if (emptyRow.children().length == 0){
                                    emptyRow.removeClass('section-content is-empty');
                                    emptyRow.addClass('section-row-content row-empty');
                                }
                            });
                            removeConainerDragEvents();
                            break;

                        case "containers":
                            initContainerDragEvents();
                            break;
                    }
                    setTimeout(() => {
                        addMouseEventToShowPlusesForBlocks(bodyClass); //Навесим события показа плюса
                    }, 500);
                }
            });
            return false;
        });
    });

    //Обновление и вставка блока
    $(window).on('new-module-refresh', function(event){
        initConstructorWrapperContent($(event.target));
    });

})(jQuery);

/**
 * При запросе на добавление блока в публичной части добавляет id секции к части запроса. Вызывается по клику на элемент с data-crud-options аттрибутом
 * @param options
 */
function addConstructorModuleSectionId(options)
{
    var button = $(options['button']);
    var section_wrapper = button.closest('[data-section-id]');
    if (section_wrapper.length){
        var section_id = section_wrapper.data('sectionId');
        options.extraParams = {
            section_id: section_id,
        };
        options.dialogOptions.crudOptions['sectionId'] = section_id;
    }
    return options;
}