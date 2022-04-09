<form method="POST">
    <table class="formTable">
        <tr>
            <td class="key">{t}Новый пароль{/t}</td>
            <td class="value"><input type="password" size="30" name="new_pass" {if !empty($error)}class="has-error"{/if}>
                <span class="formFieldError">{$error}</span>
                <div class="help">{t}Пароль должен содержать не менее 6-ти знаков{/t}</div>
            </td>
        </tr>
        <tr>
            <td class="key">{t}Повтор нового пароля{/t}</td>
            <td class="value"><input type="password" size="30" name="new_pass_confirm"></td>
        </tr>        
    </table>
    <button type="submit" class="formSave">{t}Сменить пароль{/t}</button>
</form>