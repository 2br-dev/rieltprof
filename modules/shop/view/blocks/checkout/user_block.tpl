{$user_config = ConfigLoader::byModule('users')}

{if $is_auth}
    <div class="checkout-auth-info mb-5 mb-4">
        {if $user.is_company}
            <div class="mb-3">
                <div class="fw-bold">{t}Наименование компании{/t}:</div>
                <div class="text-gray">{$user.company}</div>
            </div>
            <div class="mb-3">
                <div class="fw-bold">{t}ИНН{/t}:</div>
                <div class="text-gray">{$user.company_inn}</div>
            </div>
        {/if}
        <div class="mb-3">
            <div class="fw-bold">{t}ФИО{/t}:</div>
            <div class="text-gray">{$user.surname} {$user.name} {$user.midname}</div>
        </div>
        {foreach ['phone', 'e_mail'] as $field}
            {if $user[$field] != ''}
                <div class="mb-3">
                    <div class="fw-bold">{$user->getProp($field)->getDescription()}:</div>
                    <div class="text-gray">{$user[$field]}</div>
                </div>
            {/if}
        {/foreach}
        <div class="mt-lg-5"><a href="{$router->getUrl('users-front-auth', ['Act' => 'logout', 'referer' => $router->getUrl('shop-front-checkout')])}">{t}Сменить пользователя{/t}</a></div>
    </div>
{else}
    <div class="checkout-block mb-lg-5 mb-4">
        <div class="d-flex align-items-center mb-4">
            <div class="checkout-block__num"></div>
            <div class="checkout-block__title">{t}Данные получателя{/t}</div>
        </div>
        <div class="row g-3">
            <div class="col">
                <label class="form-label">{t}ФИО{/t}</label>
                {$order->getPropertyView('user_fio', ['placeholder' => "{t}Например, Иванов Петр Иванович{/t}", 'class' => 'rs-checkout_triggerUpdate'])}
            </div>
            <div class="col-xl-5 col-sm-5 col-lg-12">
                <label class="form-label">{t}E-mail{/t}</label>
                {$order->getPropertyView('user_email', ['placeholder' => "{t}Например, test@example.com{/t}", 'class' => 'rs-checkout_triggerUpdate'])}
            </div>
            <div>
                <label class="form-label">{t}Телефон{/t}</label>
                {$order->getPropertyView('user_phone', ['placeholder' => "{t}В формате: +7(XXX)XXXXXXX{/t}", 'class' => 'rs-checkout_triggerUpdate'])}
            </div>

            {if $shop_config.checkout_register_option == 'only_no_register'}
                <input type="hidden" name="register_user" value="0">
            {else}
                <div class="mb-3">
                    {if $shop_config.checkout_register_option == 'only_register'}
                        {$register_user = true}
                        <input type="hidden" name="register_user" value="1">
                    {else}
                        <div class="mb-3">
                            <div class="checkbox">
                                {$register_user = $order.register_user}
                                <input type="hidden" name="register_user" value="0">
                                <input type="checkbox" id="lk-checkbox" name="register_user" value="1" class="rs-checkout_registerUserInput" {if $register_user}checked{/if}>
                                <label for="lk-checkbox">
                                    <span class="checkbox-attr">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                                            <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                                        </svg>
                                    </span>
                                    <span>{t}Создать личный кабинет{/t}</span>
                                </label>
                            </div>
                        </div>
                    {/if}

                    <div class="rs-checkout_registerGeneratePassword {if !$register_user}d-none{/if}">

                        {if $user_config->canShowField('login')}
                            <div class="mb-3">
                                <label class="form-label">{t}Логин{/t}</label>
                                {$order->getPropertyView('user_login')}
                            </div>
                        {/if}

                        {foreach $user_user_fields->getStructure() as $fld}
                            <div class="mb-3">
                                <label class="form-label">{$fld.title}</label>
                                {$user_user_fields->getForm($fld.alias, '%THEME%/helper/forms/userfields_forms.tpl')}

                                {$errname = $user_user_fields->getErrorForm($fld.alias)}
                                {$error = $order->getErrorsByForm($errname, ', ')}
                                {if !empty($error)}
                                    <span class="invalid-feedback">{$error}</span>
                                {/if}
                            </div>
                        {/foreach}

                        <div class="checkbox mb-3">
                            <input id="lk-make-password" type="checkbox" name="user_autologin" value="1" class="checkbox rs-checkout_registerGeneratePasswordInput" {if $order.user_autologin || $order.user_autologin === null}checked{/if}>
                            <label for="lk-make-password">
                                <span class="checkbox-attr">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                                        <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                                    </svg>
                                </span>
                                <span>{t}Сгенерировать пароль автоматически{/t}</span>
                            </label>
                        </div>
                    </div>

                    <div class="rs-checkout_registerPassword {if $order.user_autologin || !$register_user}d-none{/if}">
                        <div class="mb-3">
                            <label class="form-label">{t}Пароль{/t}</label>
                            {$order->getPropertyView('user_openpass')}
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{t}Повтор пароля{/t}</label>
                            {$order->getPropertyView('user_pass2')}
                        </div>
                    </div>
                </div>
            {/if}

            {if $order->__code->isEnabled()}
                <div class="form-group captcha">
                    <label class="label-sup">{$order->__code->getTypeObject()->getFieldTitle()}</label>
                    {$order->getPropertyView('code')}
                </div>
            {/if}

            {foreach $order->getErrorsByForm('register_user') as $user_error}
                <div class="invalid-feedback d-block">{$user_error}</div>
            {/foreach}
        </div>
    </div>
{/if}