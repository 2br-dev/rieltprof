$.contentReady(function() {
    $('.photo_block').each(function() {

        var $photoBlock = $(this);
        var $form = $(this).closest('form');
        
        /**
        * Визуальная сортировка фотографий
        */
        $('.photo-list', this).sortable({
            handle: '.handle',
            tolerance: 'pointer',
            cancel: '.disable',
            placeholder: "sortable-placeholder",
            update: function(e, ui) {
                var pos = ui.item.index();
                var url = ui.item.closest('.photo-list').data('sortUrl');
                
                var first_bad_index = $('.fail:first', ui.item.closest('.photo-list')).index();
                if (first_bad_index > -1 && pos >= first_bad_index) {
                    $(this).sortable('cancel');
                } else {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            pos: pos,
                            photoid: ui.item.data('id')
                        }
                    });
                    $form.trigger('changePhoto', $photoBlock);
                }
            }
        });
        
        /**
        * Удалить выбранные фотографии в списке
        */ 
        $('.delete-list', this).off('click.photoBlock').on('click.photoBlock', function() {
            if (!confirm(lang.t('Вы действительно хотите удалить выбранные фото?'))) return false;
            var data = [];
            var photo_ids = []; //массив c id фото
            $('input[name="photos[]"]:checked', $photoBlock).each(function() {
                data.push({
                    name:"photos[]",
                    value: $(this).val()
                }); 
                photo_ids.push($(this).val());
            });
            
            var url = $(this).attr('formaction');
            var selected = $('.photo-one:has(.chk input:checkbox:checked)', $photoBlock);
            selected.css('opacity', '0.5');
            
            $.ajaxQuery({
                url: url,
                data:data,
                success: function() {
                    selected.remove();
                    $('.upload-block', $photoBlock).removeClass('can-delete');
                    $form.trigger('changePhoto', $photoBlock);
                }
            });
            return false;
        });
        
        /**
        * Выделение всех фото
        */
        $('.check-all', this).off('click.photoBlock').on('click.photoBlock', function() {
            if ($('input[name="photos[]"]', $photoBlock).length == $('input[name="photos[]"]:checked', $photoBlock).length) {
                $('input[name="photos[]"]', $photoBlock).prop('checked', false).change();
            } else {
                $('input[name="photos[]"]', $photoBlock).prop('checked', true).change(); 
            }
        });
        
        /**
        * Назначение действий на фото
        */
        $('.photo-one', this).each(function() {

            if ($(this).data('photoOne')) return;
            $(this).data('photoOne', {});
            
            $('.title .short', this).click(function() {
                var $short_title = $(this);
                $('.edit_title', $(this).parent())
                    .show()
                    .focus()
                    .rsCheckOutClick(function() {
                        //Сохраняем описание
                        if ($short_title.text() != $(this).val()) {
                            $short_title.text($(this).val());
                            $.ajaxQuery({
                                url: $short_title.closest('.photo-list').data('editUrl'),
                                type: 'post',
                                data: {
                                    photoid: $short_title.closest('.photo-one').data('id'),
                                    title: $(this).val()
                                },
                            })
                        }
                        $(this).hide();
                    }, this);
            });
            
            /**
            * Удаление одной фотографии
            * 
            */
            $('.delete', this).click(function() {
                if (confirm(lang.t('Вы действительно хотите удалить фото?'))) {
                    var photo_wrap = $(this).closest('.photo-one');
                    var photo_id   = photo_wrap.data('id');
                    var block      = photo_wrap.css('opacity', '0.5');
                    $.ajaxQuery({
                        url: $(this).attr('href'),
                        success: function() {
                            block.remove();
                            $form.trigger('changePhoto', $photoBlock);
                        }
                    });
                }
                return false;
            });
            
            /**
            * Поворот фотографии
            * 
            */
            $('.rotate', this).click(function() {
                var $photoOne = $(this).closest('.photo-one');
                var img = $photoOne.find('.image img').css('opacity', 0.5);
                var a = $photoOne.find('.bigview');
                
                $.ajaxQuery({
                    url: $(this).attr('href'),
                    success: function() {
                        img.css('opacity', 1);
                        
                        var new_img_src, new_a_href;
                        
                        if (img.data('originalSrc')) {
                            new_img_src = img.data('originalSrc')+'?r='+Math.random();                            
                        } else {
                            img.data('originalSrc', img.attr('src'));
                            new_img_src = img.attr('src')+'?r='+Math.random();
                        }
                        
                        if (a.data('originalHref')) {
                            new_a_href = a.data('originalHref')+'?r='+Math.random();                            
                        } else {
                            a.data('originalHref', a.attr('href'));
                            new_a_href = a.attr('href')+'?r='+Math.random();                            
                        }
                        
                        img.attr('src', new_img_src);
                        a.attr('href', new_a_href);
                    }
                });
                
                return false;
            });
            
            /**
            * Выделение фотографий
            * 
            */
            $('.chk input:checkbox').change(function() {
                var selected = $('.chk input:checkbox:checked', $photoBlock);
                if (selected.length) {
                    $('.upload-block', $photoBlock).addClass('can-delete');
                } else {
                    $('.upload-block', $photoBlock).removeClass('can-delete');
                }
            });
                
        });
        
        $photoBlock.fileupload({
            dataType: 'json',
            dropZone: $('.dragzone', this),
            pasteZone:null,
            filesContainer: $('.photo-list', this),
            uploadTemplateId:null,            
            downloadTemplateId:null,
            sequentialUploads:true,
            url: $photoBlock.attr('action'),
            type: 'POST',
            formData: function() {
                return {}
            },
            add: function(e, data) {
                var $photo = $(
                '<li class="photo-one inqueue">\
                  <div class="chk"></div>\
                    <div class="image">\
                        <a class="cancel"></a>\
                        <div class="wait"></div>\
                        <div class="action"></div>\
                        <div class="filename"></div>\
                    </div>\
                    <div class="title">\
                        <div class="progress">\
                            <div class="bar"></div>\
                            <div class="percent">0%</div>\
                        </div>\
                    </div>\
                    <div class="move disable"></div>\
                </li>');
                $photo.find('.delete').attr('title', lang.t('отменить загрузку'));
                $photo.find('.action').text(lang.t('В очереди'));
                $photo.find('.filename').text(lang.t(data.files[0].name));
                
                $('.photo-list', this).append($photo);
                data.li = $photo;
                                
                var jqXHR = data.submit();
                $photo.find('.cancel').one('click', function() {
                    jqXHR.abort();
                });
            },
            send: function(e, data) {
                data.li.removeClass('inqueue').addClass('uploading');
                data.li.find('.action').text(lang.t('Идет загрузка'));
            },
            progress: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                data.li.find('.bar').css('width', progress+'%');
                data.li.find('.percent').text(progress+'%');
            },
            done: function(e, data) {
                data.li.find('.bar').css('width', '100%');
                data.li.find('.percent').text('100%');
                if (data.result.items[0].success) {
                    data.li.replaceWith(data.result.items[0].html);
                    data.form.trigger('new-content');  
                    $form.trigger('changePhoto', $photoBlock);
                } else {
                    data.textStatus = data.result.items[0].errors.join(', ');
                    data.fail(e, data);
                }
            },
            fail: function(e, data) {
                data.li.removeClass('uploading').addClass('fail');
                data.li.find('.wait').remove();
                data.li.find('.action').text(data.textStatus);
                data.li.find('.cancel').click(function() {
                    data.li.remove();
                });
            },
            
            start: function(e) {
                $('.photo-list', $photoBlock).addClass('disable-sort');
            },
            
            stop: function(e) {
                $('.photo-list', $photoBlock).removeClass('disable-sort');
            }
        });
        
        // скрываем кнопку "выбрать все" если нет фото
        $form.on('changePhoto', function(e, context) {
            if ($('.photo-one', context).length) {
                $('.upload-block', context).removeClass('no-photos');
            } else {
                $('.upload-block', context).addClass('no-photos');
            }
        });
    });
});