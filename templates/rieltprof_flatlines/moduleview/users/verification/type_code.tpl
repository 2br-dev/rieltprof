{* Для работы во всплывающих окнах, должен подключаться в layout.tpl *}
{addjs file="%users%/verification.js"}
{addcss file="%users%/verification.css"}
{* --- *}

{$delay_refresh_code = $verify_session->getRefreshCodeDelay()}
{$error = $verify_session->getErrorsStr()}

<div class="rs-verify-code-block" data-token="{$verify_session->getToken()}">
    <div class="rs-verify-line">
        <input type="text" placeholder="{$verify_session.code_debug|default:"{t}Код{/t}"}" name="code" value="" class="rs-verify-key" autocomplete="off">

        {if $verify_session.send_counter > 0 && $verify_session.code_expire > time()}
            <div class="rs-verify-send-message">
                {t number=$verify_session->getPhoneMask()}Код отправлен на номер %number{/t}
            </div>
        {/if}

        <span class="rs-verify-timer-line {if $delay_refresh_code > 0}rs-wait{/if}" data-delay-refresh-code-sec="{$delay_refresh_code}">
            {if $delay_refresh_code > 0}
                <span class="rs-verify-timer">
                    <span class="phrase">{t}Отправить новый код можно через{/t} <span class="rs-time">{$verify_session->formatSecond($delay_refresh_code)}</span> {t}сек.{/t}</span>
                </span>
            {/if}
            <a class="rs-verify-refresh-code" data-url="{$router->getUrl('users-front-verify', ["Act" => "sendCode"])}">{t}Получить новый код{/t}</a>
        </span>
    </div>

    {* Сюда будет записана ошибка, в случае если токен истечет *}
    <span class="rs-verify-error formFieldError">{$error}</span>
</div>