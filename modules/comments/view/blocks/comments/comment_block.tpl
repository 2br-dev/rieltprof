{addjs file="core6/rsplugins/ajaxpaginator.js" basepath="common"}
{addjs file="%comments%/rscomponent/comments.js"}

{$users_config = ConfigLoader::byModule('users')}
<div class="col-md-5 order-md-last">
    <div class="product-rating">
        <div class="fw-bold text-gray mb-3">{verb item=$total values="{t}оценка,оценки,оценок{/t}"}</div>
        <div class="mb-4 d-flex align-items-center">
            <div class=" col-6">
                {$matrix = $comment_type->getMarkMatrix()}
                {foreach $matrix as $rate => $count}
                    <div class="d-flex align-items-center">
                        <div class="product-rating__stars">
                            <div class="product-rating__stars-act product-rating__stars_{$rate}"></div>
                        </div>
                        <div class="fs-5 ms-2">{$count}</div>
                    </div>
                {/foreach}
            </div>
            <div class="text-center col-6">
                <div class="product-rating__score">
                    {if $comment_type->getTypeId() == '\Catalog\Model\CommentType\Product'}
                        {$product = $comment_type->getLinkedObject()}
                        {$product->getRatingBall()}
                    {else}
                        {$comment_type->getRatingBall()}
                    {/if}
                </div>
                <div class="text-gray mt-2">{t}Рейтинг{/t}</div>
            </div>
        </div>
        <div>
            {if $mod_config.need_authorize == 'Y' && $current_user.id <= 0}
                <a data-href="{$users_config->getAuthorizationUrl(['referer' => $referer])}" class="btn btn-primary w-100 rs-in-dialog">{t}Авторизуйтесь,<br><small>чтобы оставить отзыв</small>{/t}</a>
            {else}
                <a data-href="{$router->getUrl('comments-block-comments', ['_block_id' => $_block_id, 'aid' => $aid, 'cmdo' => 'commentFormDialog'])}" class="btn btn-primary w-100 rs-in-dialog">{t}Оставить отзыв{/t}</a>
            {/if}
        </div>
    </div>
</div>

{if $total}
    <div class="col-md-7">
        <div class="rs-comment-list">
            {$list_html}
        </div>

        {if $paginator->total_pages > $paginator->page}
            <div class="mt-5">
                <a data-pagination-options='{ "appendElement":".rs-comment-list" }'
                   data-url="{$router->getUrl('comments-block-comments', ['_block_id' => $_block_id, 'cp' => $paginator->page+1, 'aid' => $aid])}"
                   class="btn btn-outline-primary col-12 rs-ajax-paginator">{t}еще комментарии...{/t}</a>
            </div>
        {/if}
    </div>
{/if}