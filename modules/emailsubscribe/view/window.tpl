<div class="wrapperSubscribeWindow" data-dialog-options='{ "width": "450" }'>
    {if $success}
        <div class="formSuccessText">
           {$success}
        </div>
    {else}
        <form action="{$router->getUrl('emailsubscribe-front-window')}" method="POST" class="formStyle">
            {$this_controller->myBlockIdInput()}
            
                <h1 class="dialogTitle">{t}Будьте в курсе наших выгодных предложений{/t}</h1>
                <p class="desc">{t}Укажите контактный Email, если Вы хотите быть в курсе наших новостей и получать сведения о скидках и акциях.{/t}</p>
                {if $errors}
                    {foreach $errors as $error}
                        <div class="error">
                          {$error}
                        </div>
                    {/foreach}
                {/if}
                <div class="row">
                    <input type="text" name="email" placeholder="user@example.com" class="email"/>
                </div>
                {if $CONFIG.enable_agreement_personal_data}
                    {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Подписаться{/t}"}
                {/if}
                <div class="row buttonsLine">
                    <input type="submit" class="formSave" value="{t}Подписаться{/t}"/>
                </div>
                {$easy_captcha_html}
        </form>
    {/if}
</div>