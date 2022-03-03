{* Список комментариев *}

{foreach $commentlist as $comment}
    <div class="row" itemprop="review" itemscope itemtype="http://schema.org/Review" {$comment->getDebugAttributes()}>
        <div class="col-xs-12 col-md-4">
            <div class="nav-tabs_review">
                <div class="nav-tabs_review_autor">
                    <span class="nav-tabs_review_autor_name" itemprop="author">{$comment.user_name}</span><br>
                    <small itemprop="datePublished">{$comment.dateof|dateformat:"@date @time"}</small>
                    <label itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                        {t}Оценка{/t}:<span class="rating" title="{$comment->getRateText()}">
                                            <span style="width:{$comment.rate*20}%" class="value"></span></span>
                        <meta itemprop="ratingValue" content="{$comment.rate}">
                    </label>

                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-8">
            <div class="nav-tabs_review">
                <div class="nav-tabs_review_wrapper">
                    <p itemprop="description">{$comment.message|nl2br}</p>
                </div>
            </div>
        </div>
    </div>
{/foreach}