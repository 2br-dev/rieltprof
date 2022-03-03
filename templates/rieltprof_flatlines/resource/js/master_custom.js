$(() => {
    fillImages();
	$('body').on('click', 'a.phone', openPhonePopup);
    $('body').on('click', '.fab-wrapper.phone', loadFabPhone);
    $('body').on('click', '#review_submit', sendReview);
    $('body').on('click', '#add-contact', addContact);
    $('body').on('click', '.trigger-action-link', setActionInCookie);
    $('body').on('click', '.toggle-view', toggleViewInCookie);

    if($('.rating_user').length){
        var initial_value = Math.round($('.rating_user').data('initial'));
        $('.rating_user').rate({
            initial_value: initial_value,
            readonly: true,
            step_size: 0.1,
            max_value: 5,
            cursor: 'none'
        });
    }

    updateInput();
});

function updateInput() {
    $('.input-field input').each((index, el) => {
        if($(el).val() === '' || $(el).val() === ' '){
            $(el).removeClass('nempty');
        }else{
            $(el).addClass('nempty');
        }
    });
}

function loadFabPhone(e){

    var already = $(this).hasClass('expanded');
    var expandedClass = already ? "" : "expanded";

    if(!already){
        if(e !== undefined){
            e.preventDefault();
            e.stopPropagation();
        }
        var user = $(this).data("user");
        var url = $(this).data("url");
        $.ajax({
            type: 'POST',
            data: {
                user: user
            },
            url: url,
            dataType: 'JSON',
            success: response => {
                console.log(response);
                $(this).find('.fab-phone-link').attr('href', 'tel:' + response).text(response);
            }
        });
        $(this).addClass('expanded');
    }


    if(already){
    }
}

// Появеление телефона владельца объявления (в списке объектов)
function openPhonePopup(e){
    var expandedClass = $(this).hasClass("opened") ? "" : "opened";
    var _this = $(this);
    if(expandedClass != "") {
        e.preventDefault();
        e.stopImmediatePropagation();
        var user = $(this).parents('tr').data("user");
        var url = $(this).parents('tr').data("url");
        var product = $(this).parents('tr').data("product");


        // var expandedClass = $(this).find('.bubble').hasClass("opened") ? "opened" : "";

        // $('.bubble').removeClass('opened');
        // $('.bubble').text('');
        // if(expandedClass === '') {
        $.ajax({
            type: 'POST',
            data: {
                user: user
            },
            url: url,
            dataType: 'JSON',
            success: function (result) {
                // $('#phone-' + product + ' .bubble').addClass(expandedClass);
                // $('#phone-' + product + ' .bubble').text(res).toggleClass("opened");
                _this.attr('href', 'tel:'+result).addClass(expandedClass).find('.bubble').text(result);
            }
        });
        // }
    }
}

//Отправка отзыва на автора объявления
function sendReview(e) {
    e.preventDefault();
    let form = $('#feedback-modal');
    let url = form.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            from: $('input[name="from"]').val(),
            to: $('input[name="to"]').val(),
            rating: $('input[name="rating"]').val(),
            text: $('textarea[name="review_text"]').val()
        },
        dataType: 'json',
        success: (res) => {
            if(res.success){
                $('.modal-body').empty().text('Ваш отзыв успешно добавлен');
                $('.modal-footer').empty();
                $('a.feedback').css('display', 'none');
            }else{
                $('.review_error').text('Заполните поле Текст отзыва');
            }
        },
        error: (err) => {
            console.error(err)
        }
    });
}

//Добавление контакта в черный список
function addContact(e) {
    if(e){
        e.preventDefault();
    }
    let form = $('#add-contact-form');
    let url = form.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            phone: $('input[name="phone"]', form).val(),
            comment: $('textarea[name="comment"]', form).val()
        },
        dataType: 'json',
        success: (res) => {
            if(res.success){
                $('.modal').removeClass('open');
                form[0].reset();
                $('.nempty').removeClass('nempty');
            }else{
                if(res.error.denied){
                    $('.error-denied').text('Вы уже заносили этот номер телефона в черный список');
                }else {
                    $('.error-denied').text('');
                }
                if(res.error.phone){
                    $('.error-phone').text('Укажите телефон');
                }else {
                    $('.error-phone').text('');
                }
                if(res.error.comment){
                    $('.error-comment').text('Оставьте комментарий');
                }else {
                    $('.error-comment').text('');
                }
            }
        },
        error: (err) => {
            console.error(err)
        }
    });
}

//По клику Аренда или продажа - записываем данные в сессию для дальнейшей работы
function setActionInCookie() {
    document.cookie = 'action_folder = ' + $(this).data('action');
}

function toggleViewInCookie(){
    var mode = $(this).data('mode');
    console.log(mode);
    document.cookie = 'view_mode = ' + mode;
}
