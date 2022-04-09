{addjs file="jquery.activetabs.js"}
{$user_config=$this_controller->getModuleConfig()}

{if $url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    <h2 data-dialog-options='{ "width": "755" }'>{t}Регистрация пользователя{/t}</h2>
{/if}

{if count($user->getNonFormErrors())>0}
    <div class="pageError">
        {foreach from=$user->getNonFormErrors() item=item}
        <p>{$item}</p>
        {/foreach}
    </div>
{/if}    

<form method="POST" action="{$router->getUrl('users-front-register')}">
    {$this_controller->myBlockIdInput()}
    <input type="hidden" name="referer" value="{$referer}">
    <input type="hidden" name="is_company" value="{$user.is_company}">
    
    {hook name="users-registers:form" title="{t}Регистрация:форма{/t}"}    
        <div class="userProfile activeTabs" data-input-name="is_company">
            <div class="formSection">
                <div class="sectionListBlock">
                    <ul class="lineList tabList">
                        <li><a class="item {if !$user.is_company}act{/if}" data-input-val="0" data-tab="#profile"><i>{t}частное лицо{/t}</i></a></li>
                        <li><a class="item {if $user.is_company}act{/if}" data-class="thiscompany" data-input-val="1" data-tab="#profile"><i>{t}компания{/t}</i></a></li>
                    </ul>
                </div>
            </div>
            
            <table class="formTable tabFrame{if $user.is_company} thiscompany{/if}" id="profile">
                {hook name="users-registers:form-fields" title="{t}Регистрация:поля формы{/t}"}
                <tbody class="organization">
                    <tr>
                        <td class="key">{t}Название организации{/t}:</td>
                        <td class="value">
                            {$user->getPropertyView('company')}
                            <div class="help">{t}Например: ООО "Аудитор"{/t}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">{t}ИНН{/t}:</td>
                        <td class="value">
                            {$user->getPropertyView('company_inn')}
                            <div class="help">{t}10 или 12 цифр{/t}</div>
                        </td>
                    </tr>                
                </tbody>                  
                <tbody>
                {if $user_config.user_one_fio_field}
                    <tr>
                        <td class="key">{t}Ф.И.О.{/t}</td>
                        <td class="value">
                            {$user->getPropertyView('fio')}
                            <div class="help">{t}Например, Иванов Иван Иванович{/t}</div>
                        </td>
                    </tr>
                {else}
                    {if $user_config->canShowField('name')}
                        <tr>
                            <td class="key">{t}Имя{/t}</td>
                            <td class="value">
                                {$user->getPropertyView('name')}
                                <div class="help">{t}Может состоять только из букв, знака тире. Например: Иван{/t}</div>
                            </td>
                        </tr>
                    {/if}
                    {if $user_config->canShowField('surname')}
                        <tr>
                            <td class="key">{t}Фамилия{/t}</td>
                            <td class="value">
                                {$user->getPropertyView('surname')}
                                <div class="help">{t}Может состоять только из букв, знака тире. Например: Петров{/t}</div>
                            </td>
                        </tr>
                    {/if}
                    {if $user_config->canShowField('midname')}
                        <tr>
                            <td class="key">{t}Отчество{/t}</td>
                            <td class="value">
                                {$user->getPropertyView('midname')}
                                <div class="help">{t}Может состоять только из букв, знака тире. Например: Иванович{/t}</div>
                            </td>
                        </tr>
                    {/if}
                {/if}

                {if $user_config->canShowField('phone')}
                    <tr>
                        <td class="key">{t}Телефон{/t}</td>
                        <td class="value">
                            {$user->getPropertyView('phone')}
                            <div class="help">{t}Например: +7 918 00011222{/t}</div>
                        </td>
                    </tr>
                {/if}

                {if $user_config->canShowField('login')}
                    <tr>
                        <td class="key">{t}Логин{/t}</td>
                        <td class="value">
                            {$user->getPropertyView('login')}
                        </td>
                    </tr>
                {/if}

                {if $user_config->canShowField('e_mail')}
                    <tr>
                        <td class="key">E-mail</td>
                        <td class="value">
                            {$user->getPropertyView('e_mail')}
                        </td>
                    </tr>
                {/if}

                <tr>
                    <td class="key">{t}Пароль{/t}</td>
                    <td class="value">
                        <div class="ib">
                            <input type="password" name="openpass" {if count($user->getErrorsByForm('openpass'))}class="has-error"{/if}>
                            <div class="help">{t}Пароль{/t}</div>
                        </div>
                        <div class="ib ml10">
                            <input type="password" name="openpass_confirm">
                            <div class="help">{t}Повтор пароля{/t}</div>
                        </div>
                        <div class="formFieldError">
                            {$user->getErrorsByForm('openpass', ',')}
                        </div>
                    </td>
                </tr>
                
                {if $conf_userfields->notEmpty()}
                    {foreach from=$conf_userfields->getStructure() item=fld}
                    <tr>
                        <td class="key">{$fld.title}</td>
                        <td class="value">
                            {$conf_userfields->getForm($fld.alias)}
                            {assign var=errname value=$conf_userfields->getErrorForm($fld.alias)}
                            {assign var=error value=$user->getErrorsByForm($errname, ', ')}
                            {if !empty($error)}
                                <span class="formFieldError">{$error}</span>
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                {/if}

                {if $user->__captcha->isEnabled()}
                    <tr class="captcha">
                        <td class="key">{$user->__captcha->getTypeObject()->getFieldTitle()}</td>
                        <td class="value">{$user->getPropertyView('captcha')}</td>
                    </tr>
                {/if}

                </tbody>
                {/hook}
            </table>
        </div>

        {if $CONFIG.enable_agreement_personal_data}
            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Зарегистрироваться{/t}"}
        {/if}

        <button type="submit" class="formSave">{t}Зарегистрироваться{/t}</button>
    {/hook}
</form>