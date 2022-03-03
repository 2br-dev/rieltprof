{if !empty($status_message)}<div class="pageError">{$status_message}</div>{/if}
<form method="POST" action="{$router->getUrl('users-front-auth')}" class="authorization">
    <div class="authorizationWrapper">
        {$this_controller->myBlockIdInput()}
        <input type="hidden" name="referer" value="{$data.referer}">
        {hook name="users-authorization:form" title="{t}Авторизация:форма{/t}"}
            {if $url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
                <h2 data-dialog-options='{ "width": "360" }'>{t}Авторизация{/t}</h2>
            {/if}
            <div class="dialogForm">
                {if !empty($error)}<div class="error">{$error}</div>{/if}
                <input type="text" name="login" value="{$data.login|default:$Setup.DEFAULT_DEMO_LOGIN}" class="login" placeholder="E-mail">
                <input type="password" name="pass" class="password" value="{$Setup.DEFAULT_DEMO_PASS}" placeholder="{t}Пароль{/t}">

                <div class="floatWrap">
                    <div class="rememberBlock">
                        <input type="checkbox" id="rememberMe" name="remember" value="1" {if $data.remember}checked{/if}> <label for="rememberMe">{t}Запомнить меня{/t}</label>
                    </div>
                    <button type="submit">{t}Войти{/t}</button>
                </div>

                <div class="noAccount">
                    {t}Нет аккаунта? {/t}&nbsp;&nbsp;&nbsp;<a href="{$router->getUrl('users-front-register')}" class="inDialog">{t}Зарегистрируйтесь{/t}</a><br>
                    {t}Забыли пароль? {/t}&nbsp;&nbsp;&nbsp;<a href="{$router->getUrl('users-front-auth', ["Act" => "recover"])}" class="inDialog">{t}Восстановить пароль{/t}</a>
                </div>
            </div>
        {/hook}
    </div>
</form>