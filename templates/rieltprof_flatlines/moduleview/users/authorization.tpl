{* Диалог авторизации пользователя *}
<div class="authorization">
    <div class="auth-form-wrapper">
        <form method="POST" action="{$router->getUrl('users-front-auth')}" id="login" class="modal-custom">
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="/">
            <div class="modal-content">
                <div class="left"></div>
                <div class="right">
                    <div class="header">
                        <div class="left">
                            <img src="{$THEME_IMG}/exclusives.svg" alt="">
                        </div>
                        <div class="right">
                            <img src="{$THEME_IMG}/logo.svg" alt="">
                        </div>
                    </div>
                    <div class="text-data">
                        <div class="row header-holder">
                            <strong>Авторизация</strong>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-field">
                                    <input
                                            type="text"
                                            name="login"
                                            value="{$data.login|default:$Setup.DEFAULT_DEMO_LOGIN}"
                                    ><label for="">E-mail или телефон</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-field">
                                    <input type="password" name="pass" value="{$Setup.DEFAULT_DEMO_PASS}"><label for="">Пароль</label>
                                </div>
                            </div>
                        </div>
                        <div class="row-fix">
                            <div class="col">
                                <input type="checkbox" id="remember" name="remember" value="1" {if $data.remember}checked{/if}> <label for="remember">Запомнить меня</label>
                            </div>
                            <div class="col right-align">
                                <a href="{$router->getUrl('users-front-auth', ["Act" => "recover"])}" class="rs-in-dialog">{t}Забыли пароль?{/t}</a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="left">
                            <a href="{$router->getUrl('users-front-register')}" class="btn btn-outlined waves-effect waves-dark">{t}Заявка на регистрацию{/t}</a>
                        </div>
                        <div class="right">
                            <input type="submit" value="{t}Войти{/t}" class="btn waves-effect waves-light">
                            {*                        <a onclick="javascript:$(this).parents('form').submit();" class="btn waves-effect waves-light">Войти</a>*}
                        </div>
                    </div>
                </div>
            </div>
            <div class="error">
                {if !empty($status_message)}<div class="pageError">{$status_message}</div>{/if}

                {if empty($errors) && $current_user->hasError()}
                    {$errors=$current_user->getErrorsStr()}
                {/if}
                {if !empty($error)}
                    <div class="error">
                        <img src="{$THEME_IMG}/attention.png" alt="Ошибка авторизации">
                        <div class="error_message">
                            <p>Ошибка авторизации</p>
                            <p>{$error}</p>
                        </div>

                    </div>
                {/if}
            </div>
        </form>
    </div>
    {include file="%rieltprof%/statistics.tpl"}
    {moduleinsert name="Rieltprof\Controller\Block\Partners"}
</div>


{*{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}*}
{*<div class="form-style modal-body mobile-width-small">*}
{*    {if $is_dialog_wrap}*}
{*        <h2 class="h2">{t}Авторизация{/t}</h2>*}
{*    {/if}*}

{*    {if !empty($status_message)}<div class="page-error">{$status_message}</div>{/if}*}

{*    <form method="POST" action="{$router->getUrl('users-front-auth')}">*}
{*        {hook name="users-authorization:form" title="{t}Авторизация:форма{/t}"}*}
{*        {$this_controller->myBlockIdInput()}*}
{*        <input type="hidden" name="referer" value="{$data.referer}">*}
{*        <input type="hidden" name="remember" value="1" checked>*}

{*        <input type="text" placeholder="{$login_placeholder}" name="login" value="{$data.login|default:$Setup.DEFAULT_DEMO_LOGIN}" {if !empty($error)}class="has-error"{/if} autocomplete="off">*}
{*        {if $error}<span class="formFieldError">{$error}</span>{/if}*}

{*        <input type="password" placeholder="{t}Введите пароль{/t}" name="pass" value="{$Setup.DEFAULT_DEMO_PASS}" {if !empty($error)}class="has-error"{/if} autocomplete="off">*}

{*        <div class="form__menu_buttons mobile-flex">*}
{*            <button type="submit" class="link link-more">{t}Войти{/t}</button>*}

{*            <div class="other-buttons">*}
{*                {if $is_dialog_wrap}*}
{*                    <a href="{$router->getUrl('users-front-register')}" class="rs-in-dialog">{t}Зарегистрироваться{/t}</a><br>*}
{*                {/if}*}

{*                <a href="{$router->getUrl('users-front-auth', ["Act" => "recover"])}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Забыли пароль?{/t}</a>*}
{*            </div>*}
{*        </div>*}
{*        {/hook}*}
{*    </form>*}
{*</div>*}
