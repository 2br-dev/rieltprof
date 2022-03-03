<input name="is_reg_user" type="radio" value="0" id="link-user" {if !$elem.is_reg_user}checked{/if}><label for="link-user">{t}Связать с зарегистрированным пользователем{/t}</label><br>
<input name="is_reg_user" type="radio" value="1" id="reg-user" {if $elem.is_reg_user}checked{/if}><label for="reg-user">{t}Зарегистрировать нового пользователя{/t}</label><br>
<br>
<div id="partner-link-user" class="reg-tab">
    {assign var=field value=$elem->__user_id}
    {include file=$field->getOriginalTemplate()}<br>
</div>
<div id="partner-reg-user" class="reg-tab" {if !$elem.is_reg_user}style="display:none"{/if}>
    <table class="intable">
        <tr>
            <td class="otitle">{$elem.__reg_name->getTitle()}</td>
            <td>{include file=$elem.__reg_name->getRenderTemplate() field=$elem.__reg_name}</td>
        </tr>    
        <tr>
            <td class="otitle">{$elem.__reg_surname->getTitle()}</td>
            <td>{include file=$elem.__reg_surname->getRenderTemplate() field=$elem.__reg_surname}</td>
        </tr>    
        <tr>
            <td class="otitle">{$elem.__reg_phone->getTitle()}</td>
            <td>{include file=$elem.__reg_phone->getRenderTemplate() field=$elem.__reg_phone}</td>
        </tr>        
        <tr>
            <td class="otitle">{$elem.__reg_e_mail->getTitle()}</td>
            <td>{include file=$elem.__reg_e_mail->getRenderTemplate() field=$elem.__reg_e_mail}</td>
        </tr>        
        <tr>
            <td class="otitle">{$elem.__reg_openpass->getTitle()}</td>
            <td>
                {include file=$elem.__reg_openpass->getRenderTemplate() field=$elem.__reg_openpass}
                <input name="changepass" type="hidden" value="1">
            </td>
        </tr>
    </table>
</div>
<script>
    $(function() {
        var regChange = function() {
            var value = $('input[name="is_reg_user"]:checked');
            $('.reg-tab').hide();
            $('#partner-'+value.attr('id')).show();
        }
        $('input[name="is_reg_user"]').change(regChange);
        regChange();
        
    });
</script>