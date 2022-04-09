{* Восстановление пароля по одному из полей авторизации *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Восстановление пароля{/t}{/block}
{block "body"}
    {$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    {if $send_success}
        {t}Письмо успешно отправлено. Следуйте инструкциям в письме{/t}
    {else}
        <form method="POST" action="{$router->getUrl('users-front-auth', ["Act" => "recover"])}">
            {$this_controller->myBlockIdInput()}
            <div class="g-4 row row-cols-1">
                {hook name="users-recover-pass:form" title="{t}Авторизация:восстановление пароля{/t}"}
                    <div>
                        {t}На указанный контакт будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля{/t}
                    </div>
                    <div>
                        <label for="input-restore-pass1" class="form-label">{$login_placeholder}</label>
                        <input type="text" name="login" value="{$data.login}" class="form-control{if !empty($error)} is-invalid{/if}" value="{$data.login}" {if $send_success}readonly{/if} id="input-restore-pass1">
                        {if !empty($error)}<div class="invalid-feedback">{$error}</div>{/if}
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">{t}Отправить{/t}</button>
                    </div>
                    <div class="d-flex align-items-center justify-content-between fs-5">
                        <a href="{$router->getUrl('users-front-auth')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Вспомнили пароль?{/t}</a>
                        <a href="{$router->getUrl('users-front-register')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}У меня нет аккаунта{/t}</a>
                    </div>
                {/hook}
            </div>
        </form>
    {/if}
{/block}