{* Профиль пользователя *}
{addjs file="rs.profile.js"}
{$config = \RS\Config\Loader::byModule('rieltprof')}
{$categories = $user->getUniqCategoryAds()}
<div class="global-wrapper" id="account">
    <div class="sidebar" id="profile-sidebar">
        <div class="profile-block">
            <div class="img">
                <a href="/"><img src="{$THEME_IMG}/logo_dark_mode.svg" alt=""></a>
            </div>
            <div class="avatar lazy-image" data-src="{$user.__photo->getUrl('320', '320', 'xy')}"></div>
            {include file="%rieltprof%/rating_info.tpl" user=$user config=$config}
        </div>
        <form method="POST" action="{$router->getUrl('users-front-profile')}">
            {csrf}
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$referer}">
            <input type="hidden" name="is_company" value="0"/>
            <div class="main-data">
                <div class="input-field">
                    {$user->getPropertyView('surname', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Фамилия</label>
                    <div class="formFieldError">{$user->getErrorsByForm('surname', ',')}</div>
                </div>
                <div class="input-field">
                    {$user->getPropertyView('name', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Имя*</label>
                    <div class="formFieldError">{$user->getErrorsByForm('name', ',')}</div>
                </div>
                <div class="input-field">
                    {$user->getPropertyView('midname', ['class' => 'nempty'])}
                    <label for="">Отчество</label>
                </div>
                <div class="auth-phone-block">
                    {$user->getPropertyView('phone', ['class' => 'nempty phone_mask'], [form => true, errors => false])}

                    <div class="formFieldError register-phone-error">{$user->getErrorsByForm('phone', ',')}</div>
                </div>
                <div class="input-field w100">
                    {$user->getPropertyView('e_mail', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">E-mail*</label>
                    <div class="formFieldError">{$user->getErrorsByForm('e_mail', ',')}</div>
                </div>
                <div class="w100">
                    <input type="checkbox" name="changepass" id="pass-accept" value="1" {if $user.changepass}checked{/if} class="checkbox">
                    <label for="pass-accept" class="check-label-w100">{t}Сменить пароль{/t}</label>
                </div>
                <div class="form-fields_change-pass{if !$user.changepass} hidden{/if}">
                    <div class="input-field w100">
                        {$user->getPropertyView('current_pass', ['class' => 'nempty'])}
                        <label for="">Старый пароль</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="openpass" {if count($user->getErrorsByForm('openpass'))}class="has-error"{/if}>
                        <label class="fieldName">{t}Пароль{/t}</label>
                        <div class="formFieldError">{$user->getErrorsByForm('openpass', ',')}</div>
                    </div>
                    <div class="input-field">
                        <input type="password" name="openpass_confirm">
                        <label class="fieldName">{t}Повтор пароля{/t}</label>
                        <div class="formFieldError">{$user->getErrorsByForm('openpass_confirm', ',')}</div>
                    </div>
                    <div class="actions">
                        {*                <a href="" class="btn critical">Удалить профиль</a>*}
                        <button type="submit" class="link link-more btn">{t}Сохранить{/t}</button>
                    </div>
                </div>
                <div class="action">
                    <a href="{$router->getUrl('rieltprof-front-auth', ['Act' => 'logout'])}" class="btn critical">Выход</a>
                    <button type="submit" class="link link-more btn">{t}Сохранить{/t}</button>
                </div>
            </div>
{*            {if $conf_userfields->notEmpty()}*}
{*                {foreach $conf_userfields->getStructure() as $fld}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{$fld.title}</label>*}
{*                        {$conf_userfields->getForm($fld.alias)}*}

{*                        {$errname = $conf_userfields->getErrorForm($fld.alias)}*}
{*                        {$error = $user->getErrorsByForm($errname, ', ')}*}
{*                        {if !empty($error)}*}
{*                            <span class="formFieldError">{$error}</span>*}
{*                        {/if}*}
{*                    </div>*}
{*                {/foreach}*}
{*            {/if}*}
            <div class="separator"></div>
        </form>
    </div>
    <div class="content">
        <div class="top-block">
            <div class="row">
                <div class="add-object-wrapper">
                    {include file='%rieltprof%/add-object-menu.tpl' referer='/my-review/'}
                    {include file='%rieltprof%/search-object-menu.tpl'}
                </div>
                <div class="separator"></div>
                <div class="switch-district">
                    <div class="segmented-radio">
                        {if $smarty.server.REQUEST_URI == "/my/"}
                            <p class="sale-category-link active">Мои объект</p>
                        {else}
                            <a href="/my/" class="sale-category-link">Мои объекты</a>
                        {/if}
                        {if $smarty.server.REQUEST_URI == "/my-review/"}
                            <p class="rent-category-link active">Мои отзывы</p>
                        {else}
                            <a href="/my-review/" class="rent-category-link">Мои отзывы</a>
                        {/if}
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
                        <a class="crumb">Мои отзывы</a>
                    </div>
                </div>
            </div>
            <div class="title">
                <span>Мои отзывы
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
                    <p>нет ни одного отзыва о Вас</p>
                {/if}
            </div>
        </div>
    </div>
    {include file='%rieltprof%/statusbar.tpl'}
</div>
