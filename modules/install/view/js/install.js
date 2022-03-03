$.install = function() {    

    var setError = function($element, text) {
        var field = $element.attr('name');
        
        $element.removeClass('has-error');
        $('.field-error[data-field="'+field+'"]').empty();
        
        if (text) {
            var errorTip = $('<span class="text" />')
                                .html(text)
                                .append('<i class="cor"></i>');
            
            $element
                .addClass('has-error')
                .off('.formerror')
                .on({
                    'focus.formerror': function() {
                        $('.field-error[data-field="'+field+'"]').show();
                    },
                    'blur.formerror': function() {
                        $('.field-error[data-field="'+field+'"]').hide();
                        checkField($element);
                    }
                });
            $('.field-error[data-field="'+field+'"]').html(errorTip);
            if ($element.is(':focus')) {
                $element.focus();
            }
        }
    },    
    checkPageError = function() {
        $('.page-error').toggle( $('.has-error').length>0 );
    },
    checkField = function($element) {
        var verdict = true, 
            regex = new RegExp( $element.data('checkPattern') );
        
        if ( !regex.test($element.val()) ) {
            setError($element, $element.data('checkError'));
            verdict = false;
        } else {
            setError($element, false);
        }
        checkPageError();
        return verdict;
    },
    
    checkFields = function() {
        var is_ok = true;
        $('[data-check-pattern]').each(function() {
            is_ok = checkField( $(this) ) && is_ok;
        });
        
        //Проверка паролей
        var $confirm = $('input[name="supervisor_pass_confirm"]');
        if ( $('input[name="supervisor_pass"]').val() != $confirm.val() ) {
            is_ok = false;
            setError($confirm, $confirm.data('checkError'));
        }
        checkPageError();
        return is_ok;
    };
    
    var steps = {
        
        //Шаг 1
        "1": function() 
        {
            var fitToScreen = function() {
                var box = $('.license-text'),
                    bottom_y = $(window).height()- 200,
                    current_bottom_y = $(box).height() + $(box).offset().top,
                    minHeight = parseInt(box.css('minHeight')),
                    correction = bottom_y - current_bottom_y;
                
                var newHeight = box.height() + correction;
                if (newHeight < minHeight) newHeight = minHeight;
                box.height(newHeight);
            }
            
            $(window).resize(fitToScreen);
            fitToScreen();

            $('.scroll-block').mCustomScrollbar({
                scrollInertia: 0
            });
            
            $('#iagree').change(function() {
                $('.next').toggleClass('disabled', !$(this).prop('checked'));
            });
            
            $('.next').click(function() {
                if (!$(this).hasClass('disabled')) {
                    location.href = $(this).data('href');
                }
                return false;
            });
        },
        
        //Шаг 3
        "3": function() 
        {
            var install = function() {
                var $target = $(this);
                
                var installStart = function() {
                        var $dialog = $('.progress-window').dialog({
                            modal:true,
                            position:{
                                // CHANGED (previously used offset option in addition to my option)
                                my: "center",
                                at: "center"
                            },
                            dialogClass: 'iprogress-window',

                            draggable: false,
                            resizable: false,
                            closeOnEscape: false,
                            create: function() {
                                var dialog = $(this);
                                $('.close-window', this).click(function() {
                                    dialog.dialog('close');
                                });
                                
                            },
                            width:'940'
                        });
                        
                        var setProgressError = function(errors) {
                            $('#progress-run').hide();
                            var pe = $('#progress-error');
                            var container = $('.error-list', pe).empty();
                            for(var i in errors) {
                                var item = $('<li>'+
                                        '<div class="field"><span class="module-title"></span><i class="cor"></i></div>'+
                                        '<div class="text"></div>'+
                                        '</li>');
                                
                                item.find('.module-title').text(errors[i].moduleTitle);
                                item.find('.text').text(errors[i].message);
                                container.append(item);
                            }
                            pe.show();
                        }
                        
                        //Отправляем запрос на начало установки
                        var formData = $('#config-form').serializeArray();
                        $('#progress-run').show();
                        $('#progress-error').hide();
                        
                        var executeProcess = function(first) {
                            var data = formData.slice();
                            if (first) {
                                data.push({name:"start", value:1});
                            }
                            
                            $.post( $(this).data('href'), data, function(response) {
                                if (response.errors) {
                                    setProgressError(response.errors);
                                } else {
                                    //Задаем, параметры следующего шага                                                             
                                    $('.percent-value', $dialog).text(response.percent+'%');                                    
                                    $('.bar', $dialog).css('width', response.percent+'%');                                    
                                    $('.status', $dialog).html(response.next);
                                    if (response.complete) {
                                        //Это был завершающий шаг, все прошло успешно
                                        location.href = $target.data('nextUrl');                                        
                                    } else {
                                        executeProcess(); //Запускаем следующую итерацию
                                    }
                                }
                            }, 'json').error(function() {
                                setProgressError([{moduleTitle: lang.t("Ошибка"), message: lang.t("Сервер вернул некорректные данные. Пожалуйста, обратитесь к разработчику.")}]);
                            });
                        };
                        
                        executeProcess(true);
                    }
                    
                    if (checkFields())  {
                        installStart.call(this); //Начинаем установку, если нет ошибок
                    }

            }
            
            var recalcWidthInput = function(e) {
                var _this = this;
                setTimeout(function() {
                    var newWidth = $('#calc-width').text( $(_this).val() ).width();
                    $(_this).width(newWidth+20);
                }, 10);
            }
            
            $('.next').click(install);
            $('#supervisor_pass_confirm').keypress(recalcWidthInput).trigger('keypress');
            

            
        },
        
        "4": function() {
            var checkLicense = function() 
            {
                var url = $('.check-license').data('href');
                $('.license-field').prop('disabled', true);
                $.post(url, {license_key: $('.license-field').val()}, function(response) {
                    $('.license-field').prop('disabled', false);
                    var container = $('.field-error');
                    var errorTip = $('<span class="text" />')
                                        .html(response.result)
                                        .append('<i class="cor"></i>');
                    container.html( errorTip ).show();

                    if (response.success) {
                        errorTip.addClass('success');
                    } else {
                        $('.license-field').addClass('has-error');
                    }

                }, 'json');

            }
            $('.license-field').keypress(function() {
                $(this).removeClass('has-error');
                $('.field-error').hide();
            }).focus(function() {
                $('#license').prop('checked', true);
            });
            
            $('.check-license').click(checkLicense);
            $('.next').click(function() {
                $('#step-form').submit();
            });
            
            
        },
        "4a": function() {
            $('.next:not(.disabled)').click(function() {
                $('#activation-form').submit();
            });
      
            $('.field-error[data-error]').each(function() {
                var $fieldError = $(this);
                var $input = $('input[name="'+$(this).data('field')+'"]');
                setError($input, $(this).data('error'));
                $input.bind('keypress', function() {
                    $(this).removeClass('has-error');
                    $fieldError.empty();
                    checkPageError();
                });
            });
        },
        
        "5": function() {
            $('.show-password').click(function() {
                $(this).hide();
                $('.authdata .password').show();
            })
        }
        
        
    },
    currentStep = $('.install').data('currentStep');
    
    if (steps[currentStep]) steps[currentStep]();
}

$(function() {
    $.install();
});