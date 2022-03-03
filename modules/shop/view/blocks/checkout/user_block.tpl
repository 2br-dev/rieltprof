{$user_config = ConfigLoader::byModule('users')}

{if $is_auth}
    <table class="table-underlined">
        {if $user.is_company}
            <tr class="table-underlined-text">
                <td><span>{t}Наименование компании{/t}</span></td>
                <td><span>{$user.company}</span></td>
            </tr>
            <tr class="table-underlined-text">
                <td><span>{t}ИНН{/t}</span></td>
                <td><span>{$user.company_inn}</span></td>
            </tr>
        {/if}
        {foreach ['name','surname', 'midname', 'phone', 'e_mail'] as $field}
            {if $user[$field] != ''}
                <tr class="table-underlined-text">
                    <td>{$user->getProp($field)->getDescription()}</td>
                    <td>{$user[$field]}</td>
                </tr>
            {/if}
        {/foreach}

    </table>
    <div class="form-group changeUser">
        <a href="{$router->getUrl('users-front-auth', ['Act' => 'logout', 'referer' => $router->getUrl('shop-front-checkout')])}" class="link link-white">{t}Сменить пользователя{/t}</a>
    </div>
{else}
    <div class="user-without-register">
        <div class="form-group">
            <label class="label-sup">{t}Ф.И.О.{/t}</label>
            {$order->getPropertyView('user_fio', ['placeholder' => "{t}Фамилия, Имя и Отчество покупателя, владельца аккаунта{/t}"])}
        </div>
        <div class="form-group">
            <label class="label-sup">{t}E-mail{/t}</label>
            {$order->getPropertyView('user_email', ['placeholder' => "{t}E-mail покупателя, владельца аккаунта{/t}"])}
        </div>
        <div class="form-group">
            <label class="label-sup">{t}Телефон{/t}</label>
            {$order->getPropertyView('user_phone', ['placeholder' => "{t}В формате: +7(123)9876543{/t}"])}
        </div>
    </div>

    {foreach $order->getErrorsByForm('register_user') as $user_error}
        <div class="formFieldError">{$user_error}</div>
    {/foreach}

    {if $shop_config.checkout_register_option == 'only_no_register'}
        <input type="hidden" name="register_user" value="0">
    {else}
        <div class="form-group">
            {if $shop_config.checkout_register_option == 'only_register'}
                {$register_user = true}
                <input type="hidden" name="register_user" value="1">
            {else}
                <div>
                    <label>
                        {$register_user = $order.register_user || ($order.register_user === null && $shop_config.register_user_default_checked)}
                        <input type="hidden" name="register_user" value="0">
                        <input type="checkbox" name="register_user" value="1" class="rs-checkout_registerUserInput" {if $register_user}checked{/if}>
                        <span>{t}Создать личный кабинет{/t}</span>
                    </label>
                </div>
            {/if}

            <div class="rs-checkout_registerGeneratePassword {if !$register_user}rs-hidden{/if}">

                {if $user_config->canShowField('login')}
                    <div class="form-group">
                        <label class="label-sup">{t}Логин{/t}</label>
                        {$order->getPropertyView('user_login')}
                    </div>
                {/if}

                {foreach $user_user_fields->getStructure() as $fld}
                    <div class="form-group">
                        <label class="label-sup">{$fld.title}</label>
                        {$user_user_fields->getForm($fld.alias)}
                        {$errname = $user_user_fields->getErrorForm($fld.alias)}
                        {$error = $order->getErrorsByForm($errname, ', ')}
                        {if !empty($error)}
                            <span class="formFieldError">{$error}</span>
                        {/if}
                    </div>
                {/foreach}

                <label>
                    <input type="checkbox" name="user_autologin" value="1" class="rs-checkout_registerGeneratePasswordInput" {if $order.user_autologin || $order.user_autologin === null}checked{/if}>
                    <span>{t}Сгенерировать пароль автоматически{/t}</span>
                </label>
            </div>

            <div class="rs-checkout_registerPassword {if $order.user_autologin || !$register_user}rs-hidden{/if}">
                <div class="inline">
                    {$order->getPropertyView('user_openpass', ['placeholder' => "{t}Пароль{/t}"])}
                </div>
                <div class="inline">
                    {$order->getPropertyView('user_pass2', ['placeholder' => "{t}Повтор пароля{/t}"])}
                </div>
            </div>
        </div>
    {/if}

{/if}