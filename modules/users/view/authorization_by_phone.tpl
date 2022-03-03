{* Диалог авторизации пользователя *}
{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}

<div class="authorization">
    <div class="authorizationWrapper">
        {if $is_dialog_wrap}
            <h2 class="h2" data-dialog-options='{ "width": "360" }'>{t}Авторизация{/t}</h2>
        {/if}

        {if !empty($status_message)}<div class="page-error">{$status_message}</div>{/if}

        <form method="POST" action="{$router->getUrl('users-front-auth', ['Act' => 'byphone'])}" class="dialogForm">
            {hook name="users-authorization-by-phone:form" title="{t}Авторизация по телефону:форма{/t}"}
                {$this_controller->myBlockIdInput()}
                <input type="hidden" name="referer" value="{$data.referer}">

                {if $error}<div class="error">{$error}</div>{/if}
                <input type="text" placeholder="{t}Номер телефона{/t}" name="phone" value="{$data.phone}" class="login {if !empty($error)}has-error{/if}" autocomplete="off">

                <div class="floatWrap">
                    <div class="rememberBlock">
                        <input type="checkbox" id="rememberMe" name="remember" value="1" {if $data.remember}checked{/if}> <label for="rememberMe">{t}Запомнить меня{/t}</label>
                    </div>
                    <button type="submit">{t}Войти{/t}</button>
                </div>

                <div class="noAccount">
                    {t}Не приходит код? {/t}&nbsp;&nbsp;&nbsp;<a href="{$router->getUrl('users-front-auth')}" {if $is_dialog_wrap}class="inDialog"{/if}>{t}Войти с помощью пароля{/t}</a><br>
                    {t}Нет аккаунта? {/t}&nbsp;&nbsp;&nbsp;<a href="{$router->getUrl('users-front-register')}" {if $is_dialog_wrap}class="inDialog"{/if}>{t}Зарегистрироваться{/t}</a><br>
                    {t}Забыли пароль? {/t}&nbsp;&nbsp;&nbsp;<a href="{$router->getUrl('users-front-auth', ["Act" => "recover"])}" {if $is_dialog_wrap}class="inDialog"{/if}>{t}Забыли пароль?{/t}</a>
                </div>
            {/hook}
        </form>
    </div>
</div>