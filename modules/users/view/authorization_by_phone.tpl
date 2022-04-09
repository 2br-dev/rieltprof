{* Диалог авторизации пользователя *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Вход{/t}{/block}
{block "body"}
    {$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    {if !empty($status_message)}<div class="alert alert-danger" role="alert">{$status_message}</div>{/if}

    <form action="{$router->getUrl('users-front-auth', ['Act' => 'byphone'])}" method="POST">
        {hook name="users-authorization-by-phone:form" title="{t}Авторизация по телефону:форма{/t}"}
        {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$data.referer}">
            <input type="hidden" name="remember" value="1" checked>
            <div class="g-4 row row-cols-1">
                <div>
                    <label for="input-auth-phone" class="form-label">{t}Номер телефона{/t}</label>
                    <input type="text" name="phone" placeholder="+X(XXX)XXX-XX-XX"
                           value="{$data.phone}"
                           class="form-control {if !empty($error)}is-invalid{/if}"
                           autocomplete="off" id="input-auth-phone">
                    {if $error}<div class="invalid-feedback">{$error}</div>{/if}
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-100">{t}Войти{/t}</button>
                </div>
                <div class="d-flex align-items-center justify-content-between fs-5">
                    <a href="{$router->getUrl('users-front-auth')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Войти с помощью пароля{/t}</a>
                    <a href="{$router->getUrl('users-front-register')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}У меня нет аккаунта{/t}</a>
                </div>
            </div>
        {/hook}
    </form>
{/block}