{addcss file="%emailsubscribe%/button.css"}
{addjs file="%emailsubscribe%/button.js"}

<div id="signUpUpdate" class="footer-signup-wrapper">
    {if $success}
        <div class="formSuccessText">
           {$success}
        </div>
    {else}
        <div class="title">{t}Подписаться на рассылку:{/t}</div>
        <form class="footer-signup-form formStyle" action="{$router->getUrl('emailsubscribe-block-subscribebutton')}" method="POST">
            {$this_controller->myBlockIdInput()}
            {if $errors}
                {foreach $errors as $error}
                    <div class="error">
                      {$error}
                    </div>
                {/foreach}
            {/if}
            <input type="text" name="email" placeholder="user@example.com"/>
            {if $CONFIG.enable_agreement_personal_data}
                {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Подписаться{/t}"}
            {/if}
            <div class="row buttonsLine">
                <button type="submit">{t}Подписаться{/t}</button>
            </div>
            {$easy_captcha_html}
        </form>
    {/if}
</div>

