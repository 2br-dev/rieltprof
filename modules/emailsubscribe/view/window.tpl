{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Будьте в курсе наших выгодных предложений{/t}{/block}
{block "body"}
<div class="subscribe-window">
    {if $success}
        <div>
           {$success}
        </div>
    {else}
        <form action="{$router->getUrl('emailsubscribe-front-window')}" method="POST">
            {$this_controller->myBlockIdInput()}

                <p class="text-gray">{t}Укажите контактный Email, если Вы хотите быть в курсе наших новостей и получать сведения о скидках и акциях.{/t}</p>
                <div class="mb-3">
                    <input type="text" name="email" placeholder="user@example.com" class="form-control{if $errors} is-invalid{/if}"/>
                    {if $errors}
                        <div class="invalid-feedback d-block">{$errors|join:","}</div>
                    {/if}
                </div>
                {if $CONFIG.enable_agreement_personal_data}
                    {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Подписаться{/t}"}
                {/if}
                <div class="mt-5">
                    <input type="submit" class="btn btn-primary" value="{t}Подписаться{/t}"/>
                </div>
                {$easy_captcha_html}
        </form>
    {/if}
</div>
{/block}