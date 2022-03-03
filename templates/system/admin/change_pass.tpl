{strip}
    {addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
    {addcss file="flatadmin/app.css?v=2" basepath="common"}

    {addjs file="jquery.min.js" basepath="common"}
    {addjs file="bootstrap/bootstrap.min.js" basepath="common"}

    {addmeta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"}

    {$app->setBodyClass('admin-style login-content')}

    {* Поместите файл background.jpg в папку /storage/branding, чтобы установить ваш фирменный фон *}
    {if $alternative_background_url}{$app->setBodyAttr('style', "background-image:url({$alternative_background_url})")}{/if}
{/strip}

<div class="lc-block auth-win">
    <div class="rs-loading"></div>

    <div class="caption-line">

        <div class="logo-line">
            <img class="rs-cart" src="{$Setup.IMG_PATH}/adminstyle/flatadmin/auth/rs-cart.svg">
            <img class="rs-text" src="{$Setup.IMG_PATH}/adminstyle/flatadmin/auth/rs-logo.svg">
        </div>

        <div class="select-lang dropdown">
            <span class="gray-around" title="{t}Выбор языка{/t}" data-toggle="dropdown">{$locale_list[$current_lang]}</span>
            {if count($locale_list)>1}
                <ul class="dropdown-menu pull-right" style="min-width:50px;">
                    {foreach $locale_list as $locale_key => $locale}
                        {if $current_lang != $locale_key}
                            <li><a href="{adminUrl do=false mod_controller=false Act=changeLang lang=$locale_key referer=$url->getSelfUrl()}">{$locale}</a></li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
        </div>
    </div>

    <div class="error-message">{$err}</div>
    <div class="success-message">{$data.successText}</div>

    <div class="form-box">

        <form method="POST" id="auth" action="{adminUrl mod_controller=false do=false Act="changePassword" uniq=$uniq}" class="body-box">
            {csrf form="change_password"}
            <input type="hidden" name="lang" value="{$current_lang}">
            <input type="hidden" name="referer" value="{$referer|escape}">

            <div class="field-zone">
                <input type="password" class="pass" id="login" name="new_pass" value="{$data.login|escape}" placeholder="{t}Новый пароль{/t}">
                <input type="password" class="pass" id="pass" name="new_pass_confirm" placeholder="{t}Повтор нового пароля{/t}"></p>
            </div>

            <p class="buttons">
                <button type="submit" class="btn btn-primary btn-lg ok va-m-c"><i class="zmdi zmdi-check m-r-5"></i> <span>{t}Сменить пароль{/t}</span></button>
            </p>
        </form>

    </div>
</div>