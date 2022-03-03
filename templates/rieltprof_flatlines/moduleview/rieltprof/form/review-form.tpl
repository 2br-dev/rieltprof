<form
    action=""
    class="modal"
    id="feedback-modal"
    data-url="{$router->getUrl('rieltprof-front-review')}"
    method="POST"
>
    <div class="title">
			<span>
				Оставить отзыв
			</span>
        <div class="close-wrapper">
            <a href="" class="close"></a>
        </div>
    </div>
    <div class="modal-body">
        <input type="hidden" name="from" value="{$from['id']}">
        <input type="hidden" name="to" value="{$to['id']}">
        <div class="rating">
            <input type="hidden" name="rating" id="rating">
        </div>
        <div class="input-field">
            <textarea name="review_text" id="feedback" cols="30" rows="10"></textarea>
            <label for="feedback">Текст отзыва</label>
            <span class="review_error"></span>
        </div>
    </div>
    <div class="modal-footer">
        <a href="" class="btn" id="review_submit">Отправить</a>
    </div>
</form>
