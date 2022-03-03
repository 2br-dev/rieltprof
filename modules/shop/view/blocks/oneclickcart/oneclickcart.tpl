{if $success}
    {hook name="shop-oneclickcart:success" title="{t}Купить в один клик корзину:Успешное сообщение{/t}"}
        <p class="oneClickCartSuccess">{t}Спасибо{/t}!<br/> {t}В ближайшее время с Вами свяжется наш менеджер.{/t}</p>
    {/hook}
{else}
    {$catalog_config=ConfigLoader::byModule('catalog')}
    <div class="oneClickCart">
        <div id="toggleOneClickCart" class="oneClickCartWrapper" style="display:none;">
            <div class="togglePhoneWrapper formTable"> 
                <form class="oneClickCartForm" action="{$router->getUrl('shop-block-oneclickcart')}">
                    {$this_controller->myBlockIdInput()}

                    {hook name="shop-oneclickcart:form" title="{t}Купить в один клик корзину:Форма{/t}"}
                        {if !empty($errors)}
                            <p class="pageError">
                            {foreach $errors as $error}
                                {$error}<br>
                            {/foreach}
                            </p>
                        {/if}
                        <div class="row">
                            <div class="caption">{t}Ваше имя{/t}</div>
                            <div class="field"><input type="text" value="{$name}" maxlength="100" name="name"/></div>
                        </div>
                        <div class="row">
                            <div class="caption">{t}Ваш телефон{/t}</div>
                            <div class="field"><input type="text" maxlength="20" value="{$phone}" name="phone"/></div>
                        </div>
                        {foreach from=$oneclick_userfields->getStructure() item=fld}
                            <div class="row">
                                <div class="caption">
                                    {$fld.title}
                                </div>
                                <div class="field">
                                    {$oneclick_userfields->getForm($fld.alias)}
                                </div>
                            </div>
                        {/foreach}
                        {if !$is_auth && $use_captcha && ModuleManager::staticModuleEnabled('kaptcha')}
                            <div class="row">
                                <div class="caption">
                                    {t}Введите код, указанный на картинке{/t}
                                </div>
                                <div class="field">
                                    <img height="42" width="100" src="{$router->getUrl('kaptcha', ['rand' => rand(1, 9999999)])}" alt=""/><br>
                                    <input type="text" name="kaptcha" class="kaptcha">
                                </div>
                            </div>
                        {/if}

                        <div class="formSaveWrapper">
                           <button type="submit" class="formSave">{t}Отправить{/t}</button>
                        </div>
                    {/hook}
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){ 
            $.oneClickCart('bindChanges');
        });
    </script>
{/if}
