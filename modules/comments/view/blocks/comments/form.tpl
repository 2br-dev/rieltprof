{* Страница создания нового отзыва *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{if $success}
    {block "class"}modal-lg{/block}
    {block "title"}{t}Отзыв отправлен{/t}{/block}
    {block "body"}
        <div>{t}Спасибо за отзыв! Ваше мнение помогает нам становиться лучше.{/t}</div>
        <div class="text-center mt-5">
            <a class="btn btn-primary" onclick="location.reload(); return false;">{t}Обновить страницу{/t}</a>
        </div>
    {/block}
{else}
    {block "class"}modal-lg rs-comments{/block}
    {block "title"}{t}Оставить отзыв{/t}{/block}
    {block "body"}
        {if $errors = $comment->getNonFormErrors()}
            <div class="alert alert-danger">{$errors|join:", "}</div>
        {/if}

        <form data-uk-grid method="POST" action="{$router->getUrl('comments-block-comments', ['_block_id' => $_block_id, 'aid' => $aid, 'cmdo' => 'commentFormDialog'])}">
            {$this_controller->myBlockIdInput()}
            <div class="row g-4">
                <div class="col-lg-6">
                    <label for="input-review1" class="form-label">{t}Имя{/t}</label>
                    {$comment->getPropertyView('user_name', ['id' => 'name', 'class' => 'uk-input', 'required' => true])}
                </div>
                <div>
                    <input class="inp_rate" type="hidden" name="rate" value="{$comment.rate}">
                    <div class="form-label">{t}Ваша оценка{/t}:</div>
                    <div class="d-flex align-items-lg-center flex-column flex-lg-row">
                        <ul class="stars-block rs-stars rs-rate">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <div class="fs-5 ms-lg-3 mt-2 mt-lg-0 text-gray">
                            {t}Оцените качество товара по шкале от 1 до 5 звезд{/t}
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-label">{t}Текст отзыва{/t}</label>
                    {$comment->getPropertyView('message', ['class' => 'uk-textarea', 'cols' => 30, 'rows' => 10, 'required' => true])}
                    {if $already_write}<div class="alert alert-warning mt-4">{t}Разрешен один отзыв на товар, предыдущий отзыв будет заменен{/t}</div>{/if}
                </div>

                {if !$is_auth}
                    <div>
                        <label class="form-label">{$comment->__captcha->getTypeObject()->getFieldTitle()}</label>
                        {$comment->getPropertyView('captcha')}
                    </div>
                {/if}

                <div>
                    {if $CONFIG.enable_agreement_personal_data}
                        {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Отправить отзыв{/t}"}
                    {/if}
                </div>

                <div>
                    <button type="submit" class="btn btn-primary col-12 col-lg-auto">{t}Отправить отзыв{/t}</button>
                </div>
            </div>
        </form>
    {/block}
{/if}