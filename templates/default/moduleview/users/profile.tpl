{addjs file="jquery.activetabs.js"}

{if count($user->getNonFormErrors())>0}
    <div class="pageError">
        {foreach from=$user->getNonFormErrors() item=item}
        <p>{$item}</p>
        {/foreach}
    </div>
{/if}    

{if $result}
    <div class="formResult success">{$result}</div>
{/if}

<form method="POST">
    {csrf}
    {$this_controller->myBlockIdInput()}
    <input type="hidden" name="referer" value="{$referer}">
    <input type="hidden" name="is_company" value="{$user.is_company}">
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
            <tbody class="organization">
                <tr>
                    <td class="key">{t}Название организации:{/t}</td>
                    <td class="value">
                        {$user->getPropertyView('company')}
                        <div class="help">{t}Например: ООО Аудиторская фирма "Аудитор"{/t}</div>
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
            <tr>
                <td class="key">{t}Имя{/t}</td>
                <td class="value">
                    {$user->getPropertyView('name')}
                    <div class="help">{t}Может состоять только из букв, знака тире. Например: Иван{/t}</div>
                </td>
            </tr>
            <tr>
                <td class="key">{t}Фамилия{/t}</td>
                <td class="value">
                    {$user->getPropertyView('surname')}
                    <div class="help">{t}Может состоять только из букв, знака тире. Например: Константинопольский{/t}</div>
                </td>    
            </tr>
            <tr>
                <td class="key">{t}Отчество{/t}</td>
                <td class="value">
                    {$user->getPropertyView('midname')}
                    <div class="help">{t}Может состоять только из букв, знака тире. Например: Иванович{/t}</div>
                </td>    
            </tr>
            <tr>
                <td class="key">{t}Телефон{/t}</td>
                <td class="value">
                    {$user->getPropertyView('phone')}
                    <div class="help">{t}Например{/t} +71234567890</div>
                </td>    
            </tr>    
            <tr>
                <td class="key">E-mail</td>
                <td class="value">
                    {$user->getPropertyView('e_mail')}
                    <div class="help">{t}Например{/t} aaa@mail.ru</div>
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
            </tbody>
        </table>
        
        <div class="formSection">
            <span class="formSectionTitle">{t}Пароль{/t}</span>
            <span class="inline ml10">
                <input type="checkbox" name="changepass" id="changepass" value="1" {if $user.changepass}checked{/if}>
                <label for="changepass">{t}Сменить пароль{/t}</label>
            </span>
        </div>
        <div class="changePasswordFrame">
            <table class="formTable">
                <tr>
                    <td class="key">{t}Текущий пароль{/t}:</td>
                    <td class="value">
                        <input type="password" name="current_pass" size="38" {if count($user->getErrorsByForm('current_pass'))}class="has-error"{/if}>
                        <span class="formFieldError">{$user->getErrorsByForm('current_pass', ',')}</span>
                    </td>
                </tr>
                <tr>
                    <td class="key">{t}Новый пароль{/t}:</td>
                    <td class="value">
                        <input type="password" name="openpass" size="38" {if count($user->getErrorsByForm('openpass'))}class="has-error"{/if}>
                        <span class="formFieldError">{$user->getErrorsByForm('openpass', ',')}</span>
                    </td>
                </tr>                
                <tr>
                    <td class="key">{t}Повтор нового пароля{/t}:</td>
                    <td class="value">
                        <input type="password" name="openpass_confirm" size="38">
                    </td>
                </tr>                                    
            </table>        
        </div>
    </div>

    <button type="submit" class="formSave">{t}Сохранить{/t}</button>
</form>

<script>
$(function() {
    var changePassword = function() {
        $('.changePasswordFrame').toggle(this.checked);
    }
    changePassword.call( $('#changepass').change(changePassword).get(0) );
});
</script>