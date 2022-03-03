{* Блок комментариев *}

{addjs file="rs.comments.js"}
{addjs file="rs.ajaxpagination.js"}

<div class="card-product_rating comments rs-comments">

    <div class="response-answer_useful">
        {if $total}
            <a class="link link-ask pull-right rs-write-comment">{t}Оставить комментарий{/t}</a>
        {/if}

        <div class="form-style comments_form rs-comment-form-wrapper {if !$error && $total}hidden{/if}">
            <span class="h1 pull-left">{t}Ваш отзыв{/t}</span>
            <div class="clearfix"></div>

            <form method="POST" action="#comments">
                {$this_controller->myBlockIdInput()}
                {if $errors = $comment->getNonFormErrors()}
                    <div class="page-error">
                        {foreach $errors as $one}
                            <p>{$one}</p>
                        {/foreach}
                    </div>
                {/if}

                <div class="form-group">
                    {$comment->getPropertyView('message')}
                    {if $already_write}<div class="already">{t}Разрешен один отзыв на товар, предыдущий отзыв будет заменен{/t}</div>{/if}
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input class="inp_rate" type="hidden" name="rate" value="{$comment.rate}">
                            <label class="label-sup stars-label">{t}Ваша оценка{/t} <span class="stars-desc rs-rate-descr">{$comment->getRateText()}</span></label>
                            <div class="stars-block rs-stars rs-rate">
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <p class="form-group">
                            <label class="label-sup">{t}Ваше имя{/t}</label>
                            {$comment->getPropertyView('user_name')}
                        </p>
                    </div>
                </div>

                {if !$is_auth}
                    <div class="form-group captcha">
                        <label class="label-sup">{$comment->__captcha->getTypeObject()->getFieldTitle()}</label>
                        {$comment->getPropertyView('captcha')}
                    </div>
                {/if}
                <button type="submit" class="link link-more">{t}Оставить отзыв{/t}</button>
            </form>
        </div>

        <div class="clearfix"></div>
        {if $total}
            <div class="comment-list">
                {$list_html}
            </div>
        {/if}

        {if $paginator->total_pages > $paginator->page}
            <div class="text-center more-wrapper">
                <a data-pagination-options='{ "appendElement":".comment-list" }' data-url="{$router->getUrl('comments-block-comments', ['_block_id' => $_block_id, 'cp' => $paginator->page+1, 'aid' => $aid])}" class="link link-white rs-ajax-paginator">{t}еще комментарии...{/t}</a>
            </div>
        {/if}
    </div>
</div>