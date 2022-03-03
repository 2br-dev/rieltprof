{* Диалог авторизации пользователя *}
{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}

<div class="form-style modal-body mobile-width-small">
    {if $is_dialog_wrap}
        <h2 class="h2">{t}Авторизация{/t}</h2>
    {/if}

    {if !empty($status_message)}<div class="page-error">{$status_message}</div>{/if}

    <form method="POST" action="{$router->getUrl('users-front-auth', ['Act' => 'byphone'])}">
        {hook name="users-authorization-by-phone:form" title="{t}Авторизация по телефону:форма{/t}"}
        {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$data.referer}">
            <input type="hidden" name="remember" value="1" checked>

            <input type="text" placeholder="{t}Номер телефона{/t}" name="phone" value="{$data.phone}" {if !empty($error)}class="has-error"{/if} autocomplete="off">
            {if $error}<span class="formFieldError">{$error}</span>{/if}

            <div class="form__menu_buttons mobile-flex">
                <button type="submit" class="link link-more">{t}Войти{/t}</button>

                <div class="other-buttons">
                    <a href="{$router->getUrl('users-front-auth')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Войти с помощью пароля{/t}</a><br>
                    <a href="{$router->getUrl('users-front-register')}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Зарегистрироваться{/t}</a><br>
                    <a href="{$router->getUrl('users-front-auth', ["Act" => "recover"])}" {if $is_dialog_wrap}class="rs-in-dialog"{/if}>{t}Забыли пароль?{/t}</a>
                </div>
            </div>
        {/hook}
    </form>
</div>