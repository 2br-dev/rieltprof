{* Диалог авторизации пользователя, второй фактор *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Подтверждение{/t}{/block}
{block "body"}
    {$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    <form method="POST" action="{$router->getUrl('users-front-auth', ['Act' => 'verify', 'token' => $token])}">
        <div class="g-4 row row-cols-1">
            {hook name="users-authorization-two-step:form" title="{t}Авторизация-подтверждение:форма{/t}"}
                {$this_controller->myBlockIdInput()}
                {$verification_engine->getVerificationFormView()}
                <div>
                    <button type="submit" class="btn btn-primary w-100">{t}Войти{/t}</button>
                </div>
            {/hook}
        </div>
    </form>
{/block}