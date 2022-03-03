{strip}
{addcss file="flatadmin/iconic-font/css/material-design-iconic-font.min.css" basepath="common"}
{addcss file="flatadmin/app.css?v=2" basepath="common"}

{addjs file="jquery.min.js" basepath="common"}
{addjs file="bootstrap/bootstrap.min.js" basepath="common"}

{addmeta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"}

{$app->setBodyClass('admin-style login-content')}

{* Поместите файл background.jpg в папку /storage/branding, чтобы установить ваш фирменный фон *}
{if $alternative_background_url}{$app->setBodyAttr('style', "background-image:url({$alternative_background_url})")}{/if}

{if $js}{addjs file="jquery.rs.auth.js" basepath="common"}{/if}
{/strip}

<div class="lc-block auth-win">
    <div class="rs-loading"></div>

    <div class="caption-line">

        <div class="logo-line">
            <img class="rs-cart" src="{$Setup.IMG_PATH}/adminstyle/flatadmin/auth/rs-cart.svg" alt="">
            <img class="rs-text" src="{$Setup.IMG_PATH}/adminstyle/flatadmin/auth/rs-logo.svg" alt="">
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
        <form method="POST" id="auth" action="{adminUrl mod_controller=false do=false Act="auth"}" class="body-box" {if $data.do == 'recover'} style="display:none"{/if}>
            <input type="hidden" name="lang" value="{$current_lang}">
            <input type="hidden" name="referer" value="{$referer|escape}">
            <input type="hidden" name="remember" value="1">

            <div class="field-zone">
                <input type="text" class="login" name="login" placeholder="{$login_placeholder}" value="{$data.login|default:$Setup.DEFAULT_DEMO_LOGIN}">
                <input type="password" class="pass" name="pass" placeholder="{t}Пароль{/t}" value="{$Setup.DEFAULT_DEMO_PASS}">
            </div>

            <p class="buttons">
                <button type="submit" class="btn btn-primary btn-lg ok va-m-c"><i class="zmdi zmdi-check m-r-5"></i> <span>{t}войти{/t}</span></button>
                <a href="{adminUrl mod_controller=false do="recover" Act="auth"}" class="text-nowrap btn-lg forget-pass to-recover"><span>{t}Забыли пароль?{/t}</span></a>
            </p>
        </form>

        <form method="POST" id="recover" action="{adminUrl mod_controller=false do="recover" Act="auth"}" class="body-box recover" {if $data.do != 'recover'}style="display:none"{/if}>
            <div class="field-zone">
                <input type="text" id="login" class="login" name="login" placeholder="{$recover_login_placeholder}" value="{$data.login|escape}">
                <p class="help">
                    <i class="corner"></i>
                    {t}На E-mail будет отправлено письмо с дальнейшими инструкциями по восстановленю пароля{/t}
                </p>
            </div>
            <p class="buttons">
                <button type="submit" class="btn btn-primary btn-lg ok va-m-c">{t}отправить{/t}</button>
                <a href="{adminUrl mod_controller=false Act="auth" do=false}" class="btn-lg forget-pass back-to-auth"><span>{t}авторизация{/t}</span></a>
            </p>
        </form>
    </div>
</div>