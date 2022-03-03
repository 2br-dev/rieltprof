{extends file="%install%/wrap.tpl"}
{block name="content"}
<h2>{t}Установка лицензии{/t}</h2>
<form method="POST" id="step-form">
    <div class="case">
        <input type="radio" name="license" class="rb" value="license" id="license" {if $license=='license'}checked{/if}>
        <div class="more">
            <label for="license">{t}Использовать лицензионную версию интернет-магазина{/t}</label><br>
            <p>{t alias="укажите номер лицензии.."}Укажите номер лицензии, который вы получили в <a href="http://readyscript.ru/my/orders/" target="_blank">личном кабинете</a> на сайте ReadyScript.ru.<br>
                В случае, если Вы еще не приобрели лицензию, Вы можете <a href="http://readyscript.ru/" target="_blank">оформить заказ прямо сейчас</a>{/t}</p>

            <div class="p-relative">
                <input type="text" placeholder="{t}Введите номер лицензии{/t}" value="{$license_key|escape}" name="license_key" class="inp-style license-field{if $result !== true} has-error{/if}">
                <div class="field-error top-corner" {if $result !== true}style="display:block"{/if}>
                    {if $result !== true}
                    <span class="text"><i class="cor"></i>
                        <span class="">{$result}</span>
                    </span>
                    {/if}
                </div>
            </div>
            <a data-href="{$router->getUrl('install', [Act=>'checkLicense'])}" class="check-license">{t}проверить{/t}</a>
            
        </div>
    </div>

    <div class="hr sim"></div>

    <div class="case" name="">
        <input type="radio" name="license" class="rb" value="trial" id="trial" {if $license=='trial'}checked{/if}>
        <div class="more">
            <label for="trial">{t}Использовать пробную 30-ти дневную версию системы{/t}</label><br>
            <p>{t}Пробная версия может использоваться в течение 30 дней, и не содержит каких-либо ограничений функционала. После истечения пробного периода, Вы должны приобрести лицензию или удалить продукт.{/t}</p>
        </div>
    </div>
</form>
<div class="button-line mtop30">
    <a class="next">{t}далее{/t}</a>
</div>
{/block}