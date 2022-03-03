{* Профиль пользователя *}
{addjs file="rs.profile.js"}
{$config = \RS\Config\Loader::byModule('rieltprof')}
{$categories = $user->getUniqCategoryAds()}
{$current_user = \RS\Application\Auth::getCurrentUser()}
<div class="global-wrapper" id="account">
    <div class="sidebar" id="profile-sidebar">
        <div class="profile-block">
            <div class="img">
                <a href="/"><img src="{$THEME_IMG}/logo_dark_mode.svg" alt=""></a>
            </div>
            <div class="avatar lazy-image" data-src="{$user.__photo->getUrl('320', '320', 'xy')}"></div>
            <div class="duration">
                <p>на rieltprof.ru c {$config->dateFormat($user['dateofreg'], 'd.m.Y')}</p>
            </div>
            {include file="%rieltprof%/rating_info.tpl" user=$user config=$config}
        </div>
        <form method="POST" action="{$router->getUrl('users-front-profile')}">
            {csrf}
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$referer}">
            <input type="hidden" name="is_company" value="0"/>
            <div class="main-data">
                <div class="input-field no-available">
                    {$user->getPropertyView('surname', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Фамилия</label>
                    <div class="formFieldError">{$user->getErrorsByForm('surname', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('name', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Имя</label>
                    <div class="formFieldError">{$user->getErrorsByForm('name', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('midname', ['class' => 'nempty'])}
                    <label for="">Отчество</label>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('phone', ['class' => 'nempty phone_mask'], [form => true, errors => false])}
                    <label for="">Телефон</label>
                    <div class="formFieldError">{$user->getErrorsByForm('phone', ',')}</div>
                </div>
                <div class="input-field w100 no-available">
                    {$user->getPropertyView('e_mail', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">E-mail</label>
                    <div class="formFieldError">{$user->getErrorsByForm('e_mail', ',')}</div>
                </div>
            </div>
            <div class="separator"></div>
        </form>
    </div>
    <div class="content">
        <div class="top-block">
            <div class="row">
                <div class="">
                    {if $current_user->canSendReview($current_user['id'], $user['id'])}
                        <a href="javascript:void(0);" class="feedback" data-target-modal="feedback-modal">Оставить отзыв</a>
                    {/if}
                </div>
                <div class="separator"></div>
                <div class="switch-district">
                    <div class="segmented-radio">
                        <a href="/owner-profile/{$user['id']}/" class="sale-category-link">Объявления</a>
                        <a href="/owner-review/{$user['id']}" class="rent-category-link">Отзывы</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-block">
            <div class="crumbs-wrapper">
                <div class="crumbs-rest-wrapper">
                    <div class="crumbs-rest">
                        <a href="/" class="crumb ">Главная</a>
                        <div class="separator">›</div>
                        <a class="crumb ">Отзывы о риеэлторе</a>
                    </div>
                </div>
            </div>
            <div class="title">
                <span>Отзывы
                    {if count($reviews)}
                        ({count($reviews)})
                    {/if}
                </span>
            </div>
            <div class="responses">
                {$config_rieltprof = \RS\Config\Loader::ByModule('rieltprof')}
                {if count($reviews)}
                    {foreach $reviews as $review}
{*                        {$author = $config_rieltprof->getUserById($review['user_from'])}*}
                        <div class="response">
                            <div class="body">
                                {$review['text']}
                            </div>
                            <div class="footer">
                                <div class="author">{$review['user_from']}</div>
                                <div class="date">{$review['date']}</div>
                                <div class="response-rating-wrapper" data-rate="{$review['rating']}">
                                    <div class="response-rating"></div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {else}
                    <p>нет ни одного отзыва об это пользователе</p>
                {/if}
            </div>
        </div>
    </div>
    {include file='%rieltprof%/statusbar.tpl'}
</div>
{include file='%rieltprof%/review-form.tpl' from=$current_user to=$user}
