<div class="formbox">
    <form id="userAddForm" method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":600, "height":720 }'>

        <input name="is_reg_user" type="radio" value="0" id="link-user" checked>&nbsp;<label for="link-user">{t}Связать с зарегистрированным пользователем{/t}</label><br>
        <input name="is_reg_user" type="radio" value="1" id="reg-user">&nbsp;<label for="reg-user">{t}Зарегистрировать нового пользователя{/t}</label><br>
        <br>
        <div id="partner-link-user" class="reg-tab">
            {include file=$field->getOriginalTemplate()}<br>
        </div>
        <div id="partner-reg-user" class="reg-tab" style="display:none">
            <table class="otable">
                <tr>
                    <td class="otitle">{t}Тип лица{/t}</td>
                    <td>
                        {$is_company=$user.is_company|default:0}
                        <input id="user_type_person" class="user-type" type="radio" name="is_company" value="0" {if !$is_company}checked="checked"{/if}/>
                        <label for="user_type_person">{t}Физическое лицо{/t}</label>&nbsp;
                        <input id="user_type_company" class="user-type" type="radio" name="is_company" value="1" {if $is_company}checked="checked"{/if}/>
                        <label for="user_type_company">{t}Юридическое лицо{/t}</label>
                    </td>
                </tr>
                <tr class="company {if !$user.is_company}hidden{/if}">
                    <td class="otitle">{$user.__company->getTitle()}</td>
                    <td>{$user.__company->formView()}</td>
                </tr>
                <tr class="company {if !$user.is_company}hidden{/if}">
                    <td class="otitle">{$user->__company_inn->getTitle()}</td>
                    <td>{$user.__company_inn->formView()}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__name->getTitle()}</td>
                    <td>{include file=$user->__name->getRenderTemplate() field=$user->__name}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__surname->getTitle()}</td>
                    <td>{include file=$user->__surname->getRenderTemplate() field=$user->__surname}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__midname->getTitle()}</td>
                    <td>{include file=$user->__midname->getRenderTemplate() field=$user->__midname}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__phone->getTitle()}</td>
                    <td>{include file=$user->__phone->getRenderTemplate() field=$user->__phone}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__e_mail->getTitle()}</td>
                    <td>{include file=$user->__e_mail->getRenderTemplate() field=$user->__e_mail}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user.__openpass->getTitle()}</td>
                    <td>
                        <input type="password" name="openpass" value="{$user.openpass}">
                        {include file="%system%/coreobject/type/form/block_error.tpl" field=$user.__openpass}
                        <a class="f-18 btn zmdi zmdi-eye show-password" title="{t}Показать пароль{/t}"></a>
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
            </table>

        </div>
    </form>
</div>