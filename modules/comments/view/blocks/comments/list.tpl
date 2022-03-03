{foreach from=$commentlist item=comment}
<li {$comment->getDebugAttributes()}>
    <div class="right bg">
        <div class="rating"><span class="value mark{$comment.rate}"></span></div>
        <span class="commentsCount">{$comment->getRateText()}</span>
    </div>
    <div class="left">
        <div class="info">
            <span class="date">{$comment.dateof|dateformat:"@date @time"}</span>
            <span class="user">{$comment.user_name}</span>
        </div>
        <p class="message">{$comment.message|nl2br}</p>
    </div>
</li>
{/foreach}