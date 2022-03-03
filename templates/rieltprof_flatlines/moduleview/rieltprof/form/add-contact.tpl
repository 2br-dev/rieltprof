<div class="modal" id="abuse-contact">
    <div class="title">
        <span>
            Внести контакт
        </span>
        <div class="close-wrapper">
            <a href="" class="close"></a>
        </div>
    </div>
    <form class="modal-body" id="add-contact-form" method="POST" data-url="{$router->getUrl('rieltprof-front-blacklist', ['Act' => 'addContact'])}">
        <div class="input-field">
            <input type="text" class="styled phone_mask" name="phone">
            <label>Номер телефона</label>
            <span class="review_error error-phone"></span>
            <span class="review_error error-denied"></span>
        </div>
        <div class="input-field">
            <textarea name="comment" id="" cols="30" rows="10" class="styled"></textarea>
            <label>Комментарий</label>
            <span class="review_error error-comment"></span>
        </div>
        <div class="right-align">
            <a href="" class="btn" id="add-contact">Отправить</a>
        </div>
    </form>
</div>
<div class="shadow"></div>
