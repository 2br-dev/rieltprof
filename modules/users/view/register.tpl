{* Регистрация пользователя *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Регистрация{/t}{/block}
{block "body"}
    {$user_config = $this_controller->getModuleConfig()}
    {$is_dialog_wrap = $url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    <form method="POST" action="{$router->getUrl('users-front-register')}">
        {csrf}
        {$this_controller->myBlockIdInput()}
        <input type="hidden" name="referer" value="{$referer}">

        <div class="g-4 row row-cols-1">
            {hook name="users-registers:form" title="{t}Регистрация:форма{/t}"}
            <div>
                <div class="radio check">
                    <input type="radio" name="is_company" value="0" id="is_company_no" {if !$user.is_company}checked{/if}>
                    <label for="is_company_no">
                        <span class="radio-attr">
                            {include file="%THEME%/helper/svg/radio.tpl"}
                        </span>
                        <span>{t}Частное лицо{/t}</span>
                    </label>
                </div>
                <div class="radio check">
                    <input type="radio" name="is_company" value="1" id="is_company_yes" {if $user.is_company}checked{/if}>
                    <label for="is_company_yes">
                        <span class="radio-attr">
                            {include file="%THEME%/helper/svg/radio.tpl"}
                        </span>
                        <span>{t}Юридическое лицо или ИП{/t}</span>
                    </label>
                </div>
            </div>
            <div class="company-fields collapse{if $user.is_company} show{/if}">
                <div>
                    <label class="form-label">{t}Наименование компании{/t}</label>
                    {$user->getPropertyView('company', ['placeholder' => "{t}Например, ООО Ромашка{/t}"])}
                </div>
                <div class="mt-4">
                    <label class="form-label">{t}ИНН{/t}</label>
                    {$user->getPropertyView('company_inn', ['placeholder' => "{t}10 или 12 цифр{/t}"])}
                </div>
            </div>

            {if $user_config.user_one_fio_field}
                <div>
                    <label class="form-label">{t}Ф.И.О.{/t}</label>
                    {$user->getPropertyView('fio', ['placeholder' => "{t}Например, Иванов Иван Иванович{/t}"])}
                </div>
            {else}
                {if $user_config->canShowField('name')}
                    <div>
                        <label class="form-label">{t}Имя{/t}</label>
                        {$user->getPropertyView('name', ['placeholder' => "{t}Например, Иван{/t}"])}
                    </div>
                {/if}

                {if $user_config->canShowField('surname')}
                    <div>
                        <label class="form-label">{t}Фамилия{/t}</label>
                        {$user->getPropertyView('surname', ['placeholder' => "{t}Например, Иванов{/t}"])}
                    </div>
                {/if}

                {if $user_config->canShowField('midname')}
                    <div>
                        <label class="form-label">{t}Отчество{/t}</label>
                        {$user->getPropertyView('midname', ['placeholder' => "{t}Например, Иванович{/t}"])}
                    </div>
                {/if}
            {/if}

            {if $user_config->canShowField('phone')}
                <div>
                    <label class="form-label">{t}Телефон{/t}</label>
                    {$user->getPropertyView('phone', ['placeholder' => "{t}Например, +7(XXX)-XXX-XX-XX{/t}"])}
                </div>
            {/if}

            {if $user_config->canShowField('login')}
                <div>
                    <label class="form-label">{t}Логин{/t}</label>
                    {$user->getPropertyView('login', ['placeholder' => "{t}Придумайте логин для входа{/t}"])}
                </div>
            {/if}

            {if $user_config->canShowField('e_mail')}
                <div>
                    <label class="form-label">{t}E-mail{/t}</label>
                    {$user->getPropertyView('e_mail', ['placeholder' => "{t}Например, demo@example.com{/t}"])}
                </div>
            {/if}

            {if $conf_userfields->notEmpty()}
                {foreach $conf_userfields->getStructure() as $fld}
                    <div>
                        <label class="form-label">{$fld.title}</label>
                        {$conf_userfields->getForm($fld.alias, '%THEME%/helper/forms/userfields_forms.tpl')}

                        {$errname = $conf_userfields->getErrorForm($fld.alias)}
                        {$error = $user->getErrorsByForm($errname, ', ')}
                        {if !empty($error)}
                            <span class="invalid-feedback">{$error}</span>
                        {/if}
                    </div>

                {/foreach}
            {/if}

            {if $user->__captcha->isEnabled()}
                <div>
                    <label class="form-label">{$user->__captcha->getTypeObject()->getFieldTitle()}</label>
                    {$user->getPropertyView('captcha')}
                </div>
            {/if}

            <div>
                <label class="form-label">{t}Пароль{/t}</label>
                {$user->getPropertyView('openpass')}
            </div>
            <div>
                <label class="form-label">{t}Повтор пароля{/t}</label>
                {$user->getPropertyView('openpass_confirm')}
            </div>

            {if $CONFIG.enable_agreement_personal_data}
                {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Зарегистрироваться{/t}"}
            {/if}
            <div>
                <button type="submit" class="btn btn-primary w-100">{t}Зарегистрироваться{/t}</button>
            </div>
            {/hook}
        </div>
    </form>
{/block}