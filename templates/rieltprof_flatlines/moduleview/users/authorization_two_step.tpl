{* Диалог авторизации пользователя *}
{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
<div class="form-style modal-body mobile-width-small">
    {if $is_dialog_wrap}
        <h2 class="h2">{t}Подтверждение{/t}</h2>
    {/if}

    <form method="POST" action="{$router->getUrl('users-front-auth',
        ['Act' => 'verify',
         'token' => $token])}">

        {hook name="users-authorization-two-step:form" title="{t}Авторизация-подтверждение:форма{/t}"}
            {$this_controller->myBlockIdInput()}
            {$verification_engine->getVerificationFormView()}

            <div class="form__menu_buttons mobile-flex">
                <button type="submit" class="link link-more">{t}Войти{/t}</button>
            </div>
        {/hook}
    </form>
</div>