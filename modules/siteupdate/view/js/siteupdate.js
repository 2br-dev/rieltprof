$(function() {
    
    var steps = {
        
        //Шаг 1. Проверка обновлений
        '1': function() {
            
            $('.check-update').click(function() {
                var $this = $(this);

                //Для совместимости с RS 2.0
                var inProgress = $.rs ? $.rs.loading.inProgress : loading.inProgress;

                if (inProgress || $this.hasClass('disabled')) {
                    return false;
                }
                
                function toggle(work)
                {
                    $('.clicktostart').toggle(!work);
                    var beforeText = $this.text();
                    $this.text($this.data('changeText'));
                    $this.data('changeText', beforeText);
                }
                
                toggle(true);
                $('.error-block').empty();                
                
                $.ajaxQuery({
                    type: 'POST',
                    url: $(this).attr('href'),
                    success: function(response) {
                        if (!response.success) {
                            $('.error-block').fillError(response.formdata.errors);
                            toggle(false);
                        }
                    }
                })                

                return false;
            });
            
            if (location.hash == '#start') {
                location.hash = '';
                $('.check-update').click();
            }
        },
        
        //Шаг 2. Выбор комплектации продукта
        '2': function() {
            
            $('.submit').click(function() {
                $('.error-block').empty();
                $.ajaxQuery({
                    type: 'POST',
                    url: $(this).attr('href'),
                    data: {update_product: $('#update-product').val()},
                    success: function(response) {
                        if (!response.success) {
                            $('.error-block').fillError(response.formdata.errors);
                        }
                    }
                })
                return false;
            });

        },
        
        //Установка обновлений
        '3': function() {
            $('.saveform').click(function() {
                var $this = $(this);
                
                if ($(this).hasClass('disabled')) return false;
                var formData = $('.update-item-form').serializeArray();
                
                if (!$('input[name="chk[]"]:checked').length) {
                    $.messenger(lang.t('Не отмечено ни одного модуля для обновления'), {theme: 'error'});
                    return false;
                }
                
                var setProgressError = function(errors) {
                    $this.removeClass('disabled');
                    $('.chk_head, input[name="chk[]"]:not(".always-checked")').prop('disabled', false);
                    $('.error-block').fillError(errors);
                }                
                
                $('.chk_head, input[name="chk[]"]').prop('disabled', true);
                $this.addClass('disabled');
                $('.progress-block').slideDown('fast');
                    
                var executeUpgrade = function(first)
                {
                    var data = formData.slice();
                    if (first) {
                        data.push({name:"start", value:1});
                    }
                    
                    $.post( $(this).data('href'), data, function(response) {
                        if (response.errors) {
                            setProgressError(response.errors);
                        } else {
                            //Задаем, параметры следующего шага                                                             
                            $('.percent').text(response.percent+'%');
                            $('.progress-bar').css('width', response.percent+'%');                                    
                            $('.module').html(response.next);
                            
                            if (response.complete) {
                                //Это был завершающий шаг, все прошло успешно
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                                
                            } else {
                                executeUpgrade(); //Запускаем следующую итерацию
                            }
                        }
                    }, 'json');
                }
                
                $(window).scrollTop(0);
                $('.error-block').empty();
                executeUpgrade(true);
                
                return false;
            });
            
            $('.chk_head').prop('checked', true).change();

            $('html').on('change', '.chk_head', function() {
                $('.chk input:not(.chk_head):enabled').change();
            });
            
            $('html').on('change', '.chk input:not(.chk_head)', function() {
                var items = $.cookie('updateItems');
                if (!items) items = '';
                var mask = new RegExp($(this).val() + ',', 'ig');
                items = items.replace(mask, '');
                if (!$(this).prop('checked')) {
                    items = items + $(this).val() + ',';
                }
                $.cookie('updateItems', items);
            });
            
            var items = $.cookie('updateItems');
            if (!items) items = '';
            var notChecked = items.split(',')
            for(var i in notChecked) {
                $('.chk input[value="'+notChecked[i]+'"]').prop('checked', false).change();
            }
        
            $('.text-success').show().delay(10000).fadeOut();
        }

    }
    
    var currentStep = $('.stepbystep').data('currentStep');
    if (steps[currentStep]) steps[currentStep]();

});