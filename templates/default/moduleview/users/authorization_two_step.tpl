{* Диалог авторизации пользователя *}
{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}

<div class="authorization">
    <div class="authorizationWrapper">
        {if $is_dialog_wrap}
            <h2 class="h2">{t}Подтверждение{/t}</h2>
        {/if}

        <form method="POST" action="{$router->getUrl('users-front-auth',
            ['Act' => 'verify',
             'token' => $token])}" class="dialogForm">

            {hook name="users-authorization-two-step:form" title="{t}Авторизация-подтверждение:форма{/t}"}
                {$this_controller->myBlockIdInput()}
                {$verification_engine->getVerificationFormView()}

                <div class="floatWrap">
                    <button type="submit" class="submitButton">{t}Войти{/t}</button>
                </div>
            {/hook}
        </form>
    </div>
</div>