{* Шаблон диалога покупки в 1 клик *}

{if !$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    {* Отображаем, если идет вставка блока на странице корзины *}
    <div class="t-buy-oneclick">
        <a class="link link-more rs-in-dialog{if $param.disabled} disabled{/if}" data-url="{$router->getUrl('shop-block-oneclickcart', [_block_id => $_block_id])}">{t}Заказать по телефону{/t}</a>
    </div>
{else}

    {if $success}
        <div class="modal-body">
            <p class="oneClickCartSuccess">{t alias="с вами свяжется менеджер"}Спасибо!<br/> В ближайшее время с Вами свяжется наш менеджер.{/t}</p>
        </div>
        {* Запускаем обновление корзины *}
        <script type="text/javascript">
            $(function() {
                if ($.cart) $.cart('refresh');
            });
        </script>
    {else}
        {* Отображаем форму, если идет открытие блок через диалог *}
        <div class="form-style modal-body">
            <h2>{t}Покупка корзины в 1 клик{/t}</h2>

            {if !empty($errors)}
                <p class="page-error">
                    {foreach $errors as $error}
                        {$error}<br>
                    {/foreach}
                </p>
            {/if}

            <form class="one-click-form" method="POST" action="{$router->getUrl('shop-block-oneclickcart')}">
                {$this_controller->myBlockIdInput()}
                <div class="form-group">
                    <label class="label-sup">{t}Ваше имя{/t}</label>
                    <input type="text" value="{$name}" maxlength="100" name="name"/>
                </div>

                <div class="form-group">
                    <label class="label-sup">{t}Ваш телефон{/t}</label>
                    <input type="text" maxlength="20" value="{$phone}" name="phone"/>
                </div>

                {foreach $oneclick_userfields->getStructure() as $fld}
                    <div class="form-group">
                        <label class="label-sup">{$fld.title}</label>
                        {$oneclick_userfields->getForm($fld.alias)}
                    </div>
                {/foreach}

                <div class="form-group">
                    {if !$is_auth && $use_captcha && ModuleManager::staticModuleEnabled('kaptcha')}
                        <label class="label-sup">
                            {t}Введите код, указанный на картинке{/t}
                        </label>

                        <img height="42" width="100" src="{$router->getUrl('kaptcha', ['rand' => rand(1, 9999999)])}" alt=""/><br>
                        <input type="text" name="kaptcha" class="kaptcha">
                    {/if}

                    <div class="form__menu_buttons">
                        <button type="submit" class="link link-more">{t}Отправить{/t}</button>
                    </div>
                </div>
            </form>
    </div>
    {/if}
{/if}
