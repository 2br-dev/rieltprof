<form method="POST" class="form-style modal-body">
    <h2 class="h2">{t}Восстановление пароля{/t}</h2>

    <div class="form-group">
        <label class="label-sup">{t}Новый пароль{/t}</label>
        <input type="password" size="30" name="new_pass" class="inp{if !empty($error)} has-error{/if}">
        <span class="formFieldError">{$error}</span>
    </div>

    <div class="form-group">
        <label class="label-sup">{t}Повтор нового пароля{/t}</label>
        <input type="password" size="30" name="new_pass_confirm">
    </div>

    <div class="form__menu_buttons">
        <button type="submit" class="link link-more">{t}Сменить пароль{/t}</button>
    </div>
</form>