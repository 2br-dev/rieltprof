{addjs file="{$mod_js}comments.js" basepath="root"}
<section class="comments">
    <div class="head">
        <span class="text">{t}Отзывы{/t}({$total})</span>
    </div>
    <div class="writeComment{if !empty($error)} on{/if}">
    <a name="comments"></a>
        {if $mod_config.need_authorize == 'Y' && !$is_auth}
        <span class="needAuth">{t}Чтобы оставить отзыв необходимо авторизоваться{/t}</span>
        {else}
        <a class="title rs-parent-switcher">{t}написать отзыв{/t}</a>
        <form method="POST">
            {$this_controller->myBlockIdInput()}
            <i class="corner"></i>
            <ul class="adaptForm">
                {if !empty($error)}
                    <li class="error">
                        {foreach from=$error item=one}
                        <p>{$one}</p>
                        {/foreach}
                    </li>
                {/if}
                {if $already_write}<li>{t}Разрешен один отзыв на товар, предыдущий отзыв будет заменен{/t}</li>{/if}
                <li>

                    <div class="name">
                        <div class="caption">{t}Имя{/t}</div>
                        <div class="field"><input type="text" name="user_name" value="{$comment.user_name}"></div>
                    </div>
                    <div class="ball">
                        <div class="rate">
                            <input class="inp_rate" type="hidden" name="rate" value="{$comment.rate}">
                            <div class="stars">
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                            </div>
                            <div class="descr">{$comment->getRateText()}</div>
                        </div>
                        <div class="caption">{t}Ваша оценка{/t}</div>
                    </div> 
                </li>
                <li>
                    <div class="text">
                        <div class="caption">{t}Отзыв{/t}</div>
                        <div class="field"><textarea name="message" rows="5">{$comment.message}</textarea></div>
                    </div>
                </li>
                {if !$is_auth}
                    <li class="caption">
                        {$comment->__captcha->getTypeObject()->getFieldTitle()}
                        {$comment->getPropertyView('captcha')}
                    </li>
                {/if}
                <li>
                    <div class="submit">
                        <input type="submit" value="{t}Отправить{/t}">
                    </div>
                </li>
            </ul>
        </form>
        {/if}
    </div>
    <ul class="commentList">
        {$list_html}
    </ul>  
    {if $paginator->total_pages > $paginator->page}
        <a data-pagination-options='{ "appendElement":".commentList" }' data-href="{$router->getUrl('comments-block-comments', ['_block_id' => $_block_id, 'cp' => $paginator->page+1, 'aid' => $aid])}" class="onemoreEmpty ajaxPaginator">{t}еще комментарии{/t}</a>
    {/if}
</section>