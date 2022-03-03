<div class="rating_info">
    <div class="rating_user" data-initial="{$user['rating']|string_format:"%.1f"}" title="{$user['rating']|string_format:"%.1f"}"></div>
    <p>{$config->num_word($user->getCountReviews(),['отзыв', 'отзыва', 'отзывов'])}</p>
</div>
