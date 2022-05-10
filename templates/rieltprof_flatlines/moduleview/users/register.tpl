{* Регистрация пользователя *}
{$user_config=ConfigLoader::byModule('users')}
<div class="authorization">
    <div class="auth-form-wrapper">
        <form method="POST" action="{$router->getUrl('users-front-register')}" class="register modal-custom" id="register" enctype="multipart/form-data">
            <input type="hidden" name="referer" value="/">
            {$this_controller->myBlockIdInput()}
            {if count($user->getNonFormErrors())>0}
                <div class="pageError">
                    {foreach $user->getNonFormErrors() as $item}
                        <p>{$item}</p>
                    {/foreach}
                </div>
            {/if}
            <div class="modal-content">
                <div class="left"></div>
                <div class="right">
                    <div class="header">
                        <div class="left">
                            <img src="{$THEME_IMG}/exclusives.svg" alt="">
                        </div>
                        <div class="right">
                            <img src="{$THEME_IMG}/logo.svg" alt="">
                        </div>
                    </div>
                    <div class="text-data">
                        <div class="row header-holder">
                            <strong>Регистрация</strong>
                        </div>
                        {if $user_config.user_one_fio_field}
                            <div class="row">
                                <div class="col">
                                    <div class="input-field">
                                        {$user->getPropertyView('fio', ['class' => 'nempty'], [form => true, errors => false])}
                                        <label for="">ФИО*</label>
                                        <div class="formFieldError">{$user->getErrorsByForm('fio', ',')}</div>
                                    </div>
                                </div>
                            </div>
                        {else}
                            <div class="row">
                                <div class="col">
                                    <div class="input-field">
    {*                                    <input type="text" name="surname" {if count($user->getErrorsByForm('surname'))}class="has-error"{/if}>*}
                                        {$user->getPropertyView('surname', ['class' => 'nempty'], [form => true, errors => false])}
                                        <label for="">Фамилия</label>
                                        <div class="formFieldError">{$user->getErrorsByForm('surname', ',')}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-field">
    {*                                    <input type="text" name="name" {if count($user->getErrorsByForm('name'))}class="has-error"{/if}>*}
                                        {$user->getPropertyView('name', ['class' => 'nempty'], [form => true, errors => false])}
                                        <label for="">Имя</label>
                                        <div class="formFieldError">{$user->getErrorsByForm('name', ',')}</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-field">
                                        {$user->getPropertyView('midname', ['class' => 'nempty'])}
                                        <label for="">Отчество</label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        <div class="row">
                            <div class="col">
                                <div class="input-field">
{*                                    <input type="text" name="e_mail" {if count($user->getErrorsByForm('e_mail'))}class="has-error"{/if}>*}
                                    {$user->getPropertyView('e_mail', ['class' => 'nempty'], [form => true, errors => false])}
                                    <label for="">E-mail</label>
                                    <div class="formFieldError">{$user->getErrorsByForm('e_mail', ',')}</div>
                                </div>
                            </div>
                        </div>
                        <div class="register-phone-block">
{*                                    <input type="text" name="phone" {if count($user->getErrorsByForm('phone'))}class="has-error"{/if}>*}
                            {$user->getPropertyView('phone', ['class' => 'nempty'], [form => true, errors => false])}
{*                                    <label for="">Телефон</label>*}
                                    <div class="formFieldError register-phone-error">{$user->getErrorsByForm('phone', ',')}</div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-field">
                                    <input type="password" name="openpass" {if count($user->getErrorsByForm('openpass'))}class="has-error"{/if}>
                                    <label class="fieldName">{t}Пароль*{/t}</label>
                                    <div class="formFieldError">{$user->getErrorsByForm('openpass', ',')}</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-field">
                                    <input type="password" name="openpass_confirm"><label class="fieldName">{t}Повтор пароля*{/t}</label>
                                    <div class="formFieldError">{$user->getErrorsByForm('openpass_confirm', ',')}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col avatar-holder">
                                <input type="file" id="avatar" name="photo"><label for="avatar" class="btn btn-flat waves-effect" id="photo">Фото</label>
                                <span class="filename">Выберите файл (не обязательное)</span>
                                <div class="formFieldError">{$user->getErrorsByForm('photo', ',')}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="left"><a href="/auth/" class="btn btn-outlined waves-effect waves-dark">Авторизация</a></div>
                        <div class="right">
{*                            <a href="index.html" class="btn waves-effect waves-light">Отправить заявку</a>*}
                            <input type="submit" value="{t}Зарегистрироваться{/t}" class="btn waves-effect waves-light"/>
                        </div>
                        {if $conf_userfields->notEmpty()}
                            {foreach $conf_userfields->getStructure() as $fld}
                                <div class="formLine">
                                    <label class="fieldName">{$fld.title}</label>
                                    {$conf_userfields->getForm($fld.alias)}
                                    {$errname=$conf_userfields->getErrorForm($fld.alias)}
                                    {$error=$user->getErrorsByForm($errname, ', ')}
                                    {if !empty($error)}
                                        <span class="formFieldError">{$error}</span>
                                    {/if}
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>
        </form>
        {include file="%rieltprof%/agreement-text.tpl"}
    </div>
    {include file="%rieltprof%/statistics.tpl"}
</div>
{* Регистрация пользователя *}
{*{$user_config=$this_controller->getModuleConfig()}*}
{*{$is_dialog_wrap=$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}*}

{*<div class="form-style modal-body mobile-width-wide">*}

{*    {if $is_dialog_wrap}*}
{*        <h2 class="h2">{t}Регистрация{/t}</h2>*}
{*    {/if}*}

{*    {if count($user->getNonFormErrors())>0}*}
{*        <div class="page-error">*}
{*            {foreach $user->getNonFormErrors() as $item}*}
{*                <div class="item">{$item}</div>*}
{*            {/foreach}*}
{*        </div>*}
{*    {/if}*}

{*    {if $result}*}
{*        <div class="page-success-result">{$result}</div>*}
{*    {/if}*}

{*    <form method="POST" action="{$router->getUrl('users-front-register')}">*}
{*        {csrf}*}
{*        {$this_controller->myBlockIdInput()}*}
{*        <input type="hidden" name="referer" value="{$referer}">*}

{*        {hook name="users-registers:form" title="{t}Регистрация:форма{/t}"}*}
{*            <div class="form-group">*}
{*                <input type="radio" name="is_company" value="0" id="is_company_no" {if !$user.is_company}checked{/if}>&nbsp;<label for="is_company_no">{t}Частное лицо{/t}</label><br>*}
{*                <input type="radio" name="is_company" value="1" id="is_company_yes" {if $user.is_company}checked{/if}>&nbsp;<label for="is_company_yes">{t}Юридическое лицо или ИП{/t}</label>*}
{*            </div>*}

{*            <div class="mobile-2-column">*}
{*                {hook name="users-registers:form-fields" title="{t}Регистрация:поля формы{/t}"}*}
{*                    <div class="form-fields_company{if !$user.is_company} hidden{/if}">*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Наименование компании{/t}</label>*}
{*                            {$user->getPropertyView('company', ['placeholder' => "{t}Например, ООО Ромашка{/t}"])}*}
{*                        </div>*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}ИНН{/t}</label>*}
{*                            {$user->getPropertyView('company_inn', ['placeholder' => "{t}10 или 12 цифр{/t}"])}*}
{*                        </div>*}
{*                    </div>*}

{*                {if $user_config.user_one_fio_field}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Ф.И.О.{/t}</label>*}
{*                        {$user->getPropertyView('fio', ['placeholder' => "{t}Например, Иванов Иван Иванович{/t}"])}*}
{*                    </div>*}
{*                {else}*}
{*                    {if $user_config->canShowField('name')}*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Имя{/t}</label>*}
{*                            {$user->getPropertyView('name', ['placeholder' => "{t}Например, Иван{/t}"])}*}
{*                        </div>*}
{*                    {/if}*}

{*                    {if $user_config->canShowField('surname')}*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Фамилия{/t}</label>*}
{*                            {$user->getPropertyView('surname', ['placeholder' => "{t}Например, Иванов{/t}"])}*}
{*                        </div>*}
{*                    {/if}*}

{*                    {if $user_config->canShowField('midname')}*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Отчество{/t}</label>*}
{*                            {$user->getPropertyView('midname', ['placeholder' => "{t}Например, Иванович{/t}"])}*}
{*                        </div>*}
{*                    {/if}*}
{*                {/if}*}

{*                {if $user_config->canShowField('phone')}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Телефон{/t}</label>*}
{*                        {$user->getPropertyView('phone', ['placeholder' => "{t}Например, +7(XXX)-XXX-XX-XX{/t}"])}*}
{*                    </div>*}
{*                {/if}*}

{*                {if $user_config->canShowField('login')}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Логин{/t}</label>*}
{*                        {$user->getPropertyView('login', ['placeholder' => "{t}Придумайте логин для входа{/t}"])}*}
{*                    </div>*}
{*                {/if}*}

{*                {if $user_config->canShowField('e_mail')}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}E-mail{/t}</label>*}
{*                        {$user->getPropertyView('e_mail', ['placeholder' => "{t}Например, demo@example.com{/t}"])}*}
{*                    </div>*}
{*                {/if}*}

{*                {if $conf_userfields->notEmpty()}*}
{*                    {foreach $conf_userfields->getStructure() as $fld}*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{$fld.title}</label>*}
{*                            {$conf_userfields->getForm($fld.alias)}*}

{*                            {$errname = $conf_userfields->getErrorForm($fld.alias)}*}
{*                            {$error = $user->getErrorsByForm($errname, ', ')}*}
{*                            {if !empty($error)}*}
{*                                <span class="formFieldError">{$error}</span>*}
{*                            {/if}*}
{*                        </div>*}

{*                    {/foreach}*}
{*                {/if}*}

{*                {if $user->__captcha->isEnabled()}*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{$user->__captcha->getTypeObject()->getFieldTitle()}</label>*}
{*                        {$user->getPropertyView('captcha')}*}
{*                    </div>*}
{*                {/if}*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Пароль{/t}</label>*}
{*                        {$user->getPropertyView('openpass')}*}
{*                    </div>*}
{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Повтор пароля{/t}</label>*}
{*                        {$user->getPropertyView('openpass_confirm')}*}
{*                    </div>*}
{*                {/hook}*}
{*            </div>*}
{*        {/hook}*}

{*        {if $CONFIG.enable_agreement_personal_data}*}
{*            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Зарегистрироваться{/t}"}*}
{*        {/if}*}

{*        <div class="form__menu_buttons mobile-center">*}


{*            <button type="submit" class="link link-more">{t}Зарегистрироваться{/t}</button>*}
{*        </div>*}
{*    </form>*}
{*</div>*}
