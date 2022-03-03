{* Страница восстановления пароля *}

{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
{hook name="users-authorization:form" title="{t}Авторизация:форма{/t}"}
    <div class="form-style modal-body mobile-width-small" id="recover-pass">
        {if $is_dialog_wrap}
            <h2 class="h2">{t}Восстановление пароля{/t}</h2>
        {/if}
        <form method="POST" action="{$router->getUrl('users-front-auth', ["Act" => "recover"])}">
            {$this_controller->myBlockIdInput()}

            <div class="input-field">
                <input type="text" name="login" value="{$data.login}" class="styled {if !empty($error)}has-error{/if}" {if $send_success}readonly{/if}>
                <label>Введите E-mail</label>
            </div>
            {if $error}<span class="formFieldError">{$error}</span>{/if}

            {if $send_success}
                <p class="recover-text success">
                    {t}Письмо успешно отправлено. Следуйте инструкциям в письме{/t}
                </p>
            {else}
                <p class="recover-text">
                    {t}На указанный контакт будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля{/t}
                </p>
                <div class="form__menu_buttons right-align">
                    <button type="submit" class="btn waves-effect waves-light waves-input-wrapper">{t}Восстановить{/t}</button>
                </div>
            {/if}
        </form>
    </div>
{/hook}
