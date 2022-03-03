{$delay_refresh_code = $verify_session->getRefreshCodeDelay()}
{$state = $verify_session->getState()}
{$error = $verify_session->getErrorsStr()}
{$action = $verify_session->getAction()}

<div class="rs-verify-code-block rs-inline"
     data-token="{$verify_session->getToken()}"
     data-check-code-url="{$router->getUrl('users-front-verify', ["Act" => "checkCode"])}">

    <input data-token type="hidden" name="{$action->getTokenInputName()}" value="{$verify_session->getToken()}">
    <div class="rs-verify-line">
        <input data-phone type="text" name="{$action->getPhoneInputName()}"
               value="{$verify_session['phone']}"
               {if $state != 'initialized'}readonly{/if} {$action->getPhoneInputAttrLine()}>

        {if $state == 'codeEnter'}
            <input data-key type="text"
                   placeholder="{$verify_session.code_debug|default:"{t}Код{/t}"}"
                   data-auto-submit-length="{$verify_session->getCodeLength()}" autocomplete="off">

        {elseif $state == 'resolved'}
            <span class="rs-verify-ok"><img src="{$THEME_IMG}/icons/icon-succes.png" alt="ok" title="{t}Номер подтвержден{/t}"></span>
        {/if}
    </div>

    {if $state != 'resolved'}
        <div class="rs-verify-timer-line {if $delay_refresh_code > 0}rs-wait{/if}" data-delay-refresh-code-sec="{$delay_refresh_code}">
            {if $delay_refresh_code > 0}
                <span class="rs-verify-timer">
                    <span class="phrase">{t}Отправить новый код можно через{/t} <span class="rs-time">{$verify_session->formatSecond($delay_refresh_code)}</span> {t}сек.{/t}</span>
                </span>
            {/if}
            <a class="rs-verify-refresh-code" data-url="{$router->getUrl('users-front-verify', ["Act" => "sendCode"])}">{t}Получить код подтверждения{/t}</a>
            {if $state == 'codeEnter'}
                <br><a class="rs-verify-reset" data-url="{$router->getUrl('users-front-verify', ["Act" => "reset"])}">{t}Изменить номер телефона{/t}</a>
            {/if}
        </div>
    {else}
        <div class="rs-verify-reset-line">
            <a class="rs-verify-reset" data-url="{$router->getUrl('users-front-verify', ["Act" => "reset"])}">{t}Изменить номер телефона{/t}</a>
        </div>
    {/if}
    {* Сюда будет записана ошибка, в случае если не токен истечет *}
    <span class="rs-verify-verify-error formFieldError">{$error}</span>
</div>