/**
 * Plugin позволяет проводить виртуальные обучающие туры по админ. панели ReadyScript
 *
 * @author ReadyScript lab.
 */
(function($) {
    $.tour = function(method) {
        var
            defaults = {
                startTourButton: '.start-tour',
                baseUrl: '/',
                folder: '',
                adminSection: global.adminSection,
                tipInfoCorrectionY:20,
            },
            args = arguments,
            timeoutHandler;

        var data = $('body').data('tour');
        if (!data) { //Инициализация
            data = {
                options: defaults
            };
            $('body').data('tour', data);
        }

        //public
        var
            methods = {
                init: function(tours, localOptions) {
                    data.tours = tours;
                    data.options = $.extend({}, data.options, localOptions);

                    $(data.options.startTourButton).click(function() {
                        methods.start($(this).data('tourId'), 'index', true);
                    });
                    //Если тур был запущен раннее, то пытаем определить действие
                    var tourId = $.cookie('tourId');
                    if (tourId) methods.start(tourId);

                },
                start: function(tour_id, startStep, force) {
                    if (!data.tours[tour_id]) {
                        console.log('Tour '+tour_id+' not found');
                        return;
                    };
                    $.cookie('tourId', tour_id, {path:'/'});
                    data.tour = data.tours[tour_id];
                    data.tourTotalSteps = getTotalSteps();
                    data.tourStepIndex = [];
                    $.each(data.tour, function(i, val) {
                        data.tourStepIndex.push(i);
                    });

                    var
                        step = findStep(data.tour, startStep, force);

                    //Проверка: если step = false, то значит стартовая страница не соответствует туру.
                    if (step) {
                        runStep(step);
                    } else {
                        if (step !== null) {
                            methods.stop();
                        }
                    }

                    $('body').bind('keypress.tour', function(e){
                        if (e.keyCode == 27) methods.stop();
                    });
                },
                stop: function() {
                    $.cookie('tourId', null, {path:'/'});
                    $.cookie('tourStep', null, {path:'/'});
                    hideStep();
                },
            }

        //private
        var
            /**
             * Выполняет поиск текущего шага в туре по принципу:
             * текущий URL должен совпадать с URL, заявленным в шаге
             *
             * @param tour
             */
            findStep = function(tour, step, force) {
                if (!step) step = $.cookie('tourStep');
                if (!step && !$('#debug-top-block').is('.debug-mobile')) {
                    step = 'index';
                }
                if (!data.tour[step]) return false;

                //Проверяем соответствует ли шаг тура текущей странице
                var a = $('<a />').attr('href', location.href).get(0);
                var relpath = ('/'+a.pathname.replace(/^([/])/gi, '')) + a.search;
                var relpath_mask = relpath.replace(data.options.adminSection, '%admin%').replace(/([/])$/gi, '');

                var steppath;
                if (step) {
                    steppath = data.options.folder + data.tour[step].url.replace(/([/])$/gi, '');
                }

                if (relpath_mask != steppath && !force) {
                    foundStep = false;
                    //Пытаемся найти шаг, по URL.
                    var before, found;
                    for(var key in data.tour) {
                        if (data.options.folder + data.tour[key].url.replace(/([/])$/gi, '') == relpath_mask) {
                            if (!before || before == step) { //Этот шаг идет вслед за предыдущим отображенным
                                //Мы нашли шаг по URL, возвращаем его
                                foundStep = key;
                                break;
                            }
                        }
                        before = key;
                    }

                    //Если не нашли, то выводим сообщение о прерывании тура
                    if (!foundStep) {
                        showDialog({
                            type: 'dialog',
                            message: lang.t('Вы перешли на страницу, не предусмотренную интерактивным курсом. <br>Вернуться и продолжить обучение?'),
                            buttons: {
                                yes: step,
                                no: false
                            }
                        });
                        return null;
                    }
                    step = foundStep;
                }

                return step;
            },

            getStepIndex = function(step) {
                var i = 1;
                for(var key in data.tour) {
                    if (key == step) return i;
                    i++;
                }
                return false;
            },

            getTotalSteps = function() {
                var i = 0;
                for(var key in data.tour) i++;
                return i;
            },

            runStep = function(step, noRedirect) {
                var tourStep = data.tour[step];
                hideStep();

                data.curStep = step;
                data.curStepIndex = getStepIndex(step);
                $.cookie('tourStep', step, {path:'/'});

                //Проверим, соответствует ли текущая страница шагу step
                var a = $('<a />').attr('href', location.href).get(0);
                var relpath = ('/'+a.pathname.replace(/^([/])/gi, '')) + a.search;
                var relpath_mask = relpath.replace(data.options.adminSection, '%admin%').replace(/([/])$/gi, '');
                if (relpath_mask != data.options.folder + tourStep.url.replace(/([/])$/gi, '') && !noRedirect) {
                    //Необходим переход на другую страницу
                    $.rs.loading.show();
                    location.href = data.options.folder + tourStep.url.replace('%admin%', data.options.adminSection);
                    return;
                }

                //Выполняет один шаг обучения
                var type = (tourStep.type) ? tourStep.type : 'tip';
                if (tourStep.onStart) tourStep.onStart();
                switch (type) {
                    case 'dialog': showDialog(tourStep); break;
                    case 'tip': showTip(step); break;
                    case 'info': showInfo(step); break;
                    case 'form': showForm(step); break;
                }

                //Выполняем watch
                if (tourStep.watch) {
                    $('body').on(tourStep.watch.event + '.tour', tourStep.watch.element, function() {
                        runAction(tourStep.watch.next, true);
                    });
                }

                $('a[data-step]').click(function() {
                    runAction( $(this).data('step') );
                });
            },

            overlayShow = function(blur) {
                if (blur) {
                    $('body > *').addClass('filterBlur');
                }
                $('<div id="tourOverlay"></div>').appendTo('body');

            },

            overlayHide = function() {
                $('#tourOverlay').remove();
                $('body > *').removeClass('filterBlur');
            },

            showDialog = function(tourStep) {
                overlayShow(true);
                var dialog = $('<div id="tipDialog" />').addClass('tipDialog').append('<a class="tipDialogClose" />')
                var content = $('<div class="tipContent" />').html(tourStep.message);
                var buttons = $('<div class="tipButtons" />');

                $.each(tourStep.buttons, function(key, val) {
                    var button = $('<a class="tipButton"/>');
                    var buttonText = (typeof(val) == 'object' && val.text) ? val.text : false;

                    switch(key) {
                        case 'no': {
                            button.text(buttonText ? buttonText : lang.t('Нет')).addClass('tipNo');
                            break;
                        }
                        case 'yes': {
                            button.text(buttonText ? buttonText : lang.t('Да')).addClass('tipYes');
                            break;
                        }
                        case 'finish': {
                            button.text(buttonText ? buttonText : lang.t('Завершить')).addClass('tipYes');
                            break;
                        }
                        default: {
                            button.text(buttonText).attr(val.attr);
                        }
                    }
                    button.click(hideDialog);

                    //Переход на следующий шаг
                    if (typeof(val) == 'string' || typeof(val) == 'boolean' || typeof(val) == 'object') {
                        button.click(function() {
                            var next = (typeof(val) == 'object') ? val.step : val;
                            runAction(next);
                        });
                    }

                    $('.tipDialogClose', dialog).click(methods.stop);
                    $('#tourOverlay').click(methods.stop);

                    button.appendTo(buttons);
                });

                dialog
                    .append(content)
                    .append(buttons)
                    .appendTo('body')
                    .addClass('flipInX animated');

                dialog.css({
                    marginLeft: -parseInt(dialog.width()/2),
                    marginTop:-parseInt(dialog.height()/2),
                });

            },

            showTip = function(step)
            {
                var
                    tourStep = data.tour[step];

                tourStep.tip = $.extend(true, {
                    correctionX: 0,
                    correctionY: 0,
                    animation: 'fadeInDown',
                    css: {
                        'minWidth': 280
                    }
                }, tourStep.tip);


                if (tourStep.tip.fixed) {
                    tourStep.tip.css['position'] = 'fixed';
                }

                var element = [];

                if (typeof(tourStep.tip.element) == 'object') {

                    for(var i=0; i<tourStep.tip.element.length; i++) {
                        var currentElement = tourStep.tip.element[i];

                        if (typeof(currentElement) == 'string') {
                            var selector = currentElement;
                        } else {
                            var selector = currentElement.selector;
                        }

                        element = $(selector).first();

                        if (currentElement.whenUse && currentElement.whenUse(element)) {
                            break;
                        } else if (!currentElement.whenUse && element.is(':visible')) {
                            break;
                        }
                    }

                    if (typeof(currentElement) == 'object') {
                        //Объединяем параметры конкретного элемента с общими
                        tourStep.tip = $.extend(tourStep.tip, currentElement);
                    }
                } else {
                    element = $(tourStep.tip.element).first();
                }

                if (!element.length) {
                    if (tourStep.tip.notFound) {
                        runAction(tourStep.tip.notFound);
                    }
                    return;
                }

                var tip = $('<div class="tipTour" />')
                tip.html('<div class="tipContent">'+tourStep.tip.tipText+'</div>')
                    .append(getStatusLine())
                    .append('<i class="corner"/>')
                    .css(tourStep.tip.css)
                    .appendTo('body')
                    .data('originalWidth', tip.width())
                    .width(tip.width())
                    .draggable();

                getTipPosition(element, tourStep.tip, tip);

                scrollWindow(tip);

                $(window).bind('resize.tour', function() {
                    getTipPosition(element, tourStep.tip, tip);
                });

                if (tourStep.tip.animation) {
                    tip.addClass(tourStep.tip.animation + ' animated');
                }

                if (tourStep.whileTrue) {
                    var whileTrue = function() {
                        if (!tourStep.whileTrue()) {
                            goNext();
                        } else {
                            timeoutHandler = setTimeout(whileTrue, 2000);
                        }
                    }();

                    timeoutHandler = setTimeout(whileTrue, 2000);
                }

                if (tourStep.checkTimeout) {
                    timeoutHandler = setTimeout(goNext, tourStep.checkTimeout);
                }
            },

            getTipPosition = function(element, tipData, tip)
            {

                var position = {
                        top: element.offset().top + element.innerHeight() + 10,
                        left: element.offset().left + element.width()/2,
                    },
                    bodyWidth = $('body').width();

                if (tipData.bottom) {
                    //Выноска находится внизу экрана
                    position.top = element.offset().top - getHeight(tip);
                    tip.addClass('bottom');
                }

                if (tipData.left) {
                    //Выноска находится внизу экрана
                    position.top = element.offset().top;
                    position.left = element.offset().left - getWidth(tip) - 10;
                    tip.addClass('left');
                }

                var tipWidth = getWidth(tip);

                if (tipWidth > bodyWidth-20) {
                    tip.width( bodyWidth-40 );
                    tipWidth = bodyWidth-20;
                }

                if (position.left + tipWidth > bodyWidth) {
                    position.marginLeft = -(position.left + tipWidth - bodyWidth + 10);
                } else {
                    position.marginLeft = 0;
                }
                position.left = position.left + tipData.correctionX;
                position.top = position.top + tipData.correctionY;

                if (position.left < 0) {
                    tip.width( tip.width() + position.left );
                    position.left = 0;
                }

                tip.css(position);

                //Устанавливаем смещение выноски
                tip.find('.corner').css('marginLeft', -position.marginLeft);


            },

            runAction = function(action, noRedirect) {

                switch(typeof(action)) {

                    case 'boolean': if (!action) {
                        methods.stop();
                    }; break;
                    case 'string': runStep(action, noRedirect); break;
                    case 'function': {
                        var result = action();
                        if (result) runStep(result, noRedirect);
                        if (result === false) return false;
                    }
                    default: return false;
                }
                return true;
            },

            closeFormDialog = function() {
                if (data.curStep && data.tour[data.curStep].type == 'form') {
                    //Пытаемся закрыть окно, если текущий шаг связан с формой
                    $('body').off('dialogBeforeDestroy.tour');
                    $('.dialog-window').dialog('close');
                }
            },

            goNext = function() {
                if (data.curStepIndex < data.tourTotalSteps) {
                    closeFormDialog();
                    runStep(data.tourStepIndex[data.curStepIndex]);
                }
            },

            goPrev = function() {
                if (data.curStepIndex > 1) {
                    closeFormDialog();
                    runStep(data.tourStepIndex[data.curStepIndex-2]);
                }
            },

            scrollWindow = function(oneTip) {

                if (oneTip.closest('.dialog-window').length) {
                    var $window = oneTip.closest('.contentbox');
                    var $windowHeight = $window.height() - 55;
                    var $scrollElement = $window;

                    var tipOffsetTop = oneTip.offset().top - 90 + $scrollElement.scrollTop();

                } else {
                    var $window = $(window);
                    var $windowHeight = $window.height();
                    var $scrollElement = $('html, body');

                    var tipOffsetTop = oneTip.offset().top - 90;
                }

                //Если tip не помещается на экран, то перемещаем scroll
                if ( tipOffsetTop < $window.scrollTop()
                    || tipOffsetTop > $window.scrollTop() + $windowHeight
                ) {
                    $scrollElement.animate({
                        scrollTop: tipOffsetTop - 50
                    });
                }
            },

            showForm = function(step)
            {
                var tourStep = data.tour[step],
                    checkTimeout,
                    tipMap = {};

                data.curSubStep = 0;
                data.totalSubSteps = 0;

                //Создаем массив tip.label => index, для быстрого нахождения index по label.
                $.each(tourStep.tips, function(i, tip) {
                    if (tip.label) {
                        tipMap[tip.label] = i;
                    }
                    data.totalSubSteps++;
                });

                //Запускает подшаги по событию
                $('body').on('new-content.tour', function() {
                    if (tourStep.tips[data.curSubStep].waitNewContent || data.curSubStep == 0) {
                        setTimeout(function() {
                            showSubTip(true);
                        }, 50);
                    }
                });

                //Возвращаемся на предыдущий шаг, если закрывается окно диалога
                $('body').on('dialogBeforeDestroy.tour', function() {
                    goPrev();
                });

                var showSubTip = function(skipCheckWait) {

                    $('.tipForm').each(function() {
                        if (tourStep.tips[ $(this).data('substep') ].onStop) {
                            tourStep.tips[ $(this).data('substep') ].onStop();
                        }
                        $(this).remove();
                        clearTimeout(checkTimeout);
                    });

                    tip = tourStep.tips[data.curSubStep];

                    if (!tip) return;

                    //Устанавливаем значения по умолчанию
                    tip = $.extend({
                        tipText: '',
                        css: {},
                        animation: null,
                        correctionX: 0,
                        correctionY: 0,
                        onStart: null,
                        onStop: null
                    }, tip);

                    var element = $(tip.element).first();

                    if ( (!skipCheckWait && tip.waitNewContent) ) return;

                    //Проверяем условие для отображения
                    if (typeof(tip.ifTrue) == 'function' ) {
                        if (!tip.ifTrue()) {
                            //Если отображать tip не следует, то перекидываем на другой tip
                            data.curSubStep = (tip.elseStep !== undefined) ? tipMap[tip.elseStep] : data.curSubStep + 1;
                            showSubTip();
                            return;
                        }
                    }

                    var goToNextSubStep = function() {
                        data.curSubStep = (tip.next) ? tipMap[tip.next] : data.curSubStep + 1;
                        showSubTip();
                    }

                    if ( !element.length  ) {
                        //Пытаемся перейти на следующий элемент
                        if (data.curSubStep>0) goToNextSubStep();
                        return;
                    }

                    var oneTip = $('<div class="tipTour tipForm" />')
                    oneTip.html('<div class="tipContent">'+tip.tipText+'</div>')
                        .data('substep', data.curSubStep)
                        .append('<i class="corner"></i>')
                        .append(getStatusLine())
                        .css(tip.css);

                    if (tip.correctionX) {
                        oneTip
                            .css('marginLeft', tip.correctionX);

                        if (tip.correctionX<0) {
                            oneTip.find('.corner').css({
                                left: -tip.correctionX
                            });
                        }
                    }

                    if (tip.correctionY) {
                        oneTip.css('marginTop', tip.correctionY);
                    }

                    if (tip.bottom) {
                        oneTip
                            .addClass('bottom')
                            .appendTo('body');

                        updateTipFormPosition(element, tip, oneTip);
                        $(window).on('resize.tour', function() {
                            updateTipFormPosition(element, tip, oneTip);
                        });

                    } else {
                        if (tip.insertAfter) {
                            oneTip
                                .insertAfter(element);
                        } else {
                            oneTip
                                .appendTo(element.parent());
                        }
                    }

                    if (tip.onStart) tip.onStart();

                    scrollWindow(oneTip);

                    if (tip.checkPattern) {
                        if ( (element.is('input') && element.attr('type') == 'text')
                            || element.is('textarea')) {

                            var checkText = function() {
                                if (tip.checkPattern.test( $(element).val() )) {
                                    goToNextSubStep();
                                } else {
                                    checkTimeout = setTimeout(checkText, 1500);
                                }
                            }
                            checkTimeout = setTimeout(checkText, 1500);
                        }
                        if (element.is('input') && element.attr('type') == 'checkbox') {
                            element.off('.tour').on('change.tour', function(e) {
                                if ($(this).is(':checked') ==  tip.checkPattern) {
                                    element.off('.tour');
                                    goToNextSubStep();
                                }
                            });
                        }

                        if (element.is('select')) {
                            element.off('.tour').on('change.tour', function(e) {
                                if (tip.checkPattern.test( $(this).val() )) {
                                    element.off('.tour');
                                    goToNextSubStep();
                                }
                            });
                        }
                    }

                    if (tip.checkSelectValue) {
                        element.on('change.tour', function(e) {
                            if (tip.checkSelectValue.test( $('option:selected', e.currentTarget).html() )) {
                                element.off('.tour');
                                goToNextSubStep();
                            }

                        });
                    }

                    if (tip.watch) {
                        var watchElement = tip.watch.element ? $(tip.watch.element) : element;

                        watchElement.one(tip.watch.event+'.tour', function() {
                            if (tip.watch.next) {
                                runAction(tip.watch.next);
                            } else {
                                goToNextSubStep();
                            }
                        });
                    }

                    if (tip.tinymceTextarea) {
                        var textarea = $(tip.tinymceTextarea);

                        var checkText = function() {
                            if (tip.checkPattern.test( textarea.html() )) {
                                goToNextSubStep();
                            } else {
                                setTimeout(checkText, 1000);
                            }
                        };
                        setTimeout(checkText, 1000);
                    }

                    if (tip.checkTimeout) {
                        checkTimeout = setTimeout(function() {
                            goToNextSubStep();
                        }, tip.checkTimeout);
                    }
                }

                showSubTip();
            },

            updateTipFormPosition = function(element, tipData, oneTip)
            {
                var position = {
                    top: element.offset().top + getHeight(element),
                    left: element.offset().left
                }

                if (tipData.bottom) {
                    position.top = element.offset().top - getHeight(oneTip);
                }

                if (oneTip.css('position') == 'fixed') {
                    position.top = position.top - $(window).scrollTop();
                }

                oneTip.css(position);

                //Выставляем смещение выноски
                if (tipData.correctionX) {
                    oneTip.find('.corner').css({
                        left: tipData.correctionX
                    });
                }
            },

            showInfo = function(step)
            {
                var tourStep = data.tour[step];
                overlayShow();

                if (tourStep.tips)
                    $.each(tourStep.tips, function(i, tip) {

                        //Устанавливаем значения по умолчанию
                        tip = $.extend({
                            tipText: '',
                            css: {},
                            animation: null,
                            position:['left', 'bottom'],
                            correctionX: 0,
                            correctionY: 0
                        }, tip);

                        var element = $(tip.element).first();

                        var canShow = element.length && (!tip.whenUse || tip.whenUse(element));

                        if (canShow) {
                            var oneTip = $('<div class="tipInfoTour" />')
                            oneTip.html('<div class="tipInfoTourContent">'+tip.tipText+'</div>')
                                .append('<i class="corner"><span class="line"><span class="arrow"></span></span></i>')
                                .addClass( tip.position[0]+tip.position[1][0].toUpperCase()+tip.position[1].substring(1) )
                                .css(tip.css)
                                .appendTo('body');

                            updateTipInfoPosition(tip.element, tip, oneTip);

                            $(window).on('resize.tour', function() {
                                updateTipInfoPosition(tip.element, tip, oneTip);
                            });

                            if (tip.animation) {
                                oneTip.addClass(tip.animation + ' animated');
                            }
                        }
                    });
                var
                    text = $('<div class="contentTour">').html(tourStep.message);

                $('<div class="infoTour" />')
                    .append('<div class="infoBack"/>')
                    .append('<h2>'+lang.t('Информация')+'</h2>')
                    .append(text)
                    .append(getStatusLine())
                    .appendTo('body')
                    .css('marginTop', -$('.infoTour').height()/2)
                    .draggable({handle: 'h2'});

                $('.goNext').addClass('pulse animated infinite');
            },

            getWidth = function(element) {
                return element.width() + parseInt(element.css('paddingLeft')) + parseInt(element.css('paddingRight'));
            },

            getHeight = function(element) {
                return element.height() + parseInt(element.css('paddingTop')) + parseInt(element.css('paddingBottom'));
            },

            updateTipInfoPosition = function(elementString, tipData, oneTip) {
                var
                    element = $(elementString),
                    horiz = tipData.position[0],
                    vert = tipData.position[1],
                    cornerSourceY,
                    css = {};

                if (!element.is(':visible')) {
                    oneTip.css('visibility', 'hidden');
                    return false;
                } else {
                    oneTip.css('visibility', 'visible');
                }

                switch(horiz) {
                    case 'left': css.left = element.offset().left + getWidth(element) - getWidth(oneTip);
                        if (vert == 'middle') {
                            css.left = css.left - getWidth(element);
                        }
                        break;
                    case 'center': css.left = element.offset().left + getWidth(element)/2 - getWidth(oneTip)/2; break;
                    case 'right': css.left = element.offset().left;
                        if (vert == 'middle') {
                            css.left = css.left + getWidth(element);
                        }
                        break;
                }

                switch(vert) {
                    case 'top': css.top = element.offset().top - getHeight(oneTip) - data.options.tipInfoCorrectionY; cornerSourceY = element.offset().top; break;
                    case 'middle': css.top = element.offset().top + getHeight(element)/2 - getHeight(oneTip)/2; cornerSourceY = element.offset().top + getHeight(element)/2; break;
                    case 'bottom': css.top = element.offset().top + getHeight(element) + data.options.tipInfoCorrectionY; cornerSourceY = element.offset().top + getHeight(element);  break;
                }

                css.marginTop = tipData.correctionY;
                css.marginLeft = tipData.correctionX;

                if (tipData.fixed) {
                    oneTip.css('position', 'fixed');
                }

                oneTip.css(css);

                //Устанавливаем высоту выноски
                var cornerCss = {
                    left: 'auto',
                    right: 'auto',
                    top: 'auto',
                    bottom: 'auto',
                    width: 10,
                    height: 1,
                }
                if (vert == 'middle') {

                    //Выноска горизонтальная
                    cornerCss.top = cornerSourceY-css.top;

                    if (horiz == 'right') {
                        cornerCss.width = (css.left + tipData.correctionX) - (element.offset().left + getWidth(element));
                        cornerCss.left = -cornerCss.width;
                    }
                    if (horiz == 'left') {
                        cornerCss.width = element.offset().left - (css.left + getWidth(oneTip) + tipData.correctionX);
                        cornerCss.right = -cornerCss.width;
                    }

                } else {
                    //Выноска вертикальная
                    cornerCss.left = element.offset().left + getWidth(element)/2 - css.left;
                    if (vert == 'bottom') {
                        cornerCss.height = Math.abs(cornerSourceY - css.top) + css.marginTop;
                        cornerCss.top = -cornerCss.height;
                    }
                    if (vert == 'top') {
                        cornerCss.height = Math.abs(cornerSourceY - (css.top + getHeight(oneTip))) - css.marginTop;
                        cornerCss.bottom = -cornerCss.height;
                    }
                }

                oneTip.find('.corner').css(cornerCss);
            },

            getStatusLine = function()
            {
                var
                    tourStep = data.tour[data.curStep],
                    curSubStep = '',
                    showNext = false;

                if (tourStep.type == 'form') {
                    var
                        curSubStep = '<span class="tourSubStep">.'+(data.curSubStep)+'</span>',
                        showNext = curSubStep < data.totalSubSteps;
                }

                var infoline = $('<div class="infoLineTour">').html(
                    '<span class="infoLineStep">'+lang.t('шаг')+' <strong>'+data.curStepIndex+'</strong>'+curSubStep+' '+lang.t('из')+' '+data.tourTotalSteps+'</span>'
                );

                if (data.curStepIndex>1) {
                    infoline.prepend( $('<a class="goPrev"><i class="zmdi zmdi-arrow-left"></i><span>'+lang.t('назад')+'</span></a>').on('click', goPrev) );
                    $('body').on('keydown.tour', function(e) {
                        if (e.ctrlKey && e.keyCode == 37) goPrev();
                    });
                }
                if (data.curStepIndex < data.tourTotalSteps || showNext) {
                    infoline.append( $('<a class="goNext"><span>'+lang.t('далее')+'</span><i class="zmdi zmdi-arrow-right"></i></a>').on('click', goNext) );
                    $('body').on('keydown.tour', function(e) {
                        if (e.ctrlKey && e.keyCode == 39) goNext();
                    });
                }

                infoline.append( $('<a class="tourClose zmdi zmdi-close"></a>').on('click', methods.stop) );

                return infoline;
            },

            hideStep = function()
            {
                overlayHide();
                hideDialog();
                $('body').off('dialogBeforeDestroy.tour');
                $('.infoTour, .tipTour, .tipInfoTour').remove();
                $(window).off('.tour');
                $('*').off('.tour');
                clearTimeout(timeoutHandler);

                if (data.curStep && typeof(data.tour[data.curStep].onStop) == 'function') data.tour[data.curStep].onStop();
            },

            hideDialog = function()
            {
                overlayHide();
                $('#tipDialog').remove();
            };

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object') {
            return methods.init.apply( this, args );
        }
    };
})(jQuery);