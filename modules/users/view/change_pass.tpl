<form method="POST" class="col-md-6">
    <h2 class="h2">{t}Восстановление пароля{/t}</h2>
    <p>{t}Придумайте надежный новый пароль. Сразу после изменения пароля вы будете авторизованы.{/t}</p>

    <div class="mb-4">
        <label class="form-label">{t}Новый пароль{/t}</label>
        <input type="password" size="30" name="new_pass" class="form-control{if !empty($error)} is-invalid{/if}">
        {if $error}<div class="invalid-feedback d-block" data-field="new_pass">{$error}</div>{/if}
    </div>

    <div class="mb-4">
        <label class="form-label">{t}Повтор нового пароля{/t}</label>
        <input type="password" size="30" name="new_pass_confirm" class="form-control">
    </div>

    <div>
        <button type="submit" class="btn btn-primary">{t}Сменить пароль{/t}</button>
    </div>
</form>