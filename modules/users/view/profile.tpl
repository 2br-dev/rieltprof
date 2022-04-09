{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
<div class="col-xxl-4 col-xl-5 col-md-6">
    <h1>{t}Мои данные{/t}</h1>

    {if count($user->getNonFormErrors())>0}
        <div class="alert alert-danger">{$user->getNonFormErrors()|join:"<br>"}</div>
    {/if}

    {if $result}
        <div class="alert alert-success">{$result}</div>
    {/if}

    <form method="POST">
        {csrf}
        {$this_controller->myBlockIdInput()}
        <input type="hidden" name="referer" value="{$referer}">

        <div class="mb-5">
            <ul class="lk-profile-status">
                <li>
                    <input name="is_company" value="0" id="private-persone" type="radio" {if !$user.is_company}checked{/if}>
                    <label for="private-persone">{t}Частное лицо{/t}</label>
                </li>
                <li>
                    <input name="is_company" value="1" id="company" type="radio" {if $user.is_company}checked{/if}>
                    <label for="company">{t}Компания или ИП{/t}</label>
                </li>
            </ul>
        </div>
        <div class="company-fields collapse{if $user.is_company} show{/if}">
            <div class="mb-3">
                <label class="form-label">{t}Наименование компании{/t}</label>
                {$user->getPropertyView('company', ['placeholder' => "{t}Например, ООО Ромашка{/t}"])}
            </div>
            <div class="mb-3">
                <label class="form-label">{t}ИНН{/t}</label>
                {$user->getPropertyView('company_inn', ['placeholder' => "{t}10 или 12 цифр{/t}"])}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{t}Фамилия{/t}</label>
            {$user->getPropertyView('surname', ['placeholder' => "{t}Например, Иванов{/t}"])}
        </div>
        <div class="mb-3">
            <label class="form-label">{t}Имя{/t}</label>
            {$user->getPropertyView('name', ['placeholder' => "{t}Например, Иван{/t}"])}
        </div>
        <div class="mb-3">
            <label class="form-label">{t}Отчество{/t}</label>
            {$user->getPropertyView('midname', ['placeholder' => "{t}Например, Иванович{/t}"])}
        </div>
        <div class="mb-3">
            <label class="form-label">{t}Телефон{/t}</label>
            {$user->getPropertyView('phone', ['placeholder' => "{t}Например, +7(XXX)-XXX-XX-XX{/t}"])}
        </div>
        <div class="mb-3">
            <label class="form-label">{t}Электронная почта{/t}</label>
            {$user->getPropertyView('e_mail', ['placeholder' => "{t}Например, demo@example.com{/t}"])}
        </div>
        {if $conf_userfields->notEmpty()}
            {foreach $conf_userfields->getStructure() as $fld}
                <div class="mb-3">
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

        <div class="lk-profile-change-pass" data-bs-toggle="collapse" data-bs-target="#change-pass-block">
            <input type="checkbox" id="change-pass" name="changepass" value="1" {if $user.changepass}checked{/if}>
            <label for="change-pass">{t}Сменить пароль{/t}</label>
        </div>
        <div class="collapse{if $user.changepass} show{/if}" id="change-pass-block">
            <div class="pt-4">
                <div class="mb-3">
                    <label class="form-label">{t}Старый пароль{/t}</label>
                    {$user->getPropertyView('current_pass')}
                </div>
                <div class="mb-3">
                    <label class="form-label">{t}Новый пароль{/t}</label>
                    {$user->getPropertyView('openpass')}
                </div>
                <div>
                    <label class="form-label">{t}Повторите пароль{/t}</label>
                    {$user->getPropertyView('openpass_confirm')}
                </div>
            </div>
        </div>
        <div class="mt-lg-5 mt-4">
            <button class="btn btn-primary col-12 col-sm-auto" type="submit">{t}Сохранить изменения{/t}</button>
        </div>
    </form>
</div>
{/block}