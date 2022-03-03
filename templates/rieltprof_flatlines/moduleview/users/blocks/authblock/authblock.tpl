{* Блок авторизации *}
<div class="gridblock">
    <div class="cart-wrapper hover-wrapper">
        <div class="cart-block">
            <div class="cart-block-wrapper">
                {if $is_auth}
                    <div class="t-drop-account">
                        <div class="t-close"><i class="pe-2x pe-7s-close-circle"></i></div>
                        <div class="t-drop-account_wrap">
                            {hook name="users-blocks-authblock:username" title="{t}Блок авторизации:имя пользователя{/t}"}
                                <span class="t-drop-account__name">{$current_user.name} {$current_user.surname}</span>
                            {/hook}

                            {if $use_personal_account}
                                <span class="t-drop-account__balance">Баланс:&nbsp;{hook name="users-blocks-authblock:balance" title="{t}Блок авторизации:баланс{/t}"}
                                    <a href="{$router->getUrl('shop-front-mybalance')}">{$current_user->getBalance(true, true)}</a>{/hook}
                                </span>
                            {/if}

                            <ul class="t-drop-account__list">
                                {hook name="users-blocks-authblock:cabinet-menu-items" title="{t}Блок авторизации:пункты меню личного кабинета{/t}"}
                                    <li class="item"><a href="{$router->getUrl('users-front-profile')}">{t}Профиль{/t}</a></li>
                                    <li class="item"><a href="{$router->getUrl('shop-front-myorders')}">{t}Мои заказы{/t}</a></li>
                                    {if $return_enable}
                                        <li class="item"><a href="{$router->getUrl('shop-front-myproductsreturn')}">{t}Мои возвраты{/t}</a></li>
                                    {/if}
                                    {if $use_personal_account}
                                        <li class="item"><a href="{$router->getUrl('shop-front-mybalance')}">{t}Лицевой счет{/t}</a></li>
                                    {/if}
                                {/hook}
                            </ul>
                            <div class="t-drop-account__logout">
                                <a href="{$router->getUrl('users-front-auth', ['Act' => 'logout'])}" class="t-drop-account__logout-exit">
                                    <div class="t-drop-account__logout-icon">
                                        <svg id="Capa_1" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 384.971 384.971" style="enable-background:new 0 0 384.971 384.971;"
                                            xml:space="preserve"><g><g id="Sign_Out"><path d="M180.455,360.91H24.061V24.061h156.394c6.641,0,12.03-5.39,12.03-12.03s-5.39-12.03-12.03-12.03H12.03C5.39,0.001,0,5.39,0,12.031V372.94c0,6.641,5.39,12.03,12.03,12.03h168.424c6.641,0,12.03-5.39,12.03-12.03C192.485,366.299,187.095,360.91,180.455,360.91z"></path><path d="M381.481,184.088l-83.009-84.2c-4.704-4.752-12.319-4.74-17.011,0c-4.704,4.74-4.704,12.439,0,17.179l62.558,63.46H96.279c-6.641,0-12.03,5.438-12.03,12.151c0,6.713,5.39,12.151,12.03,12.151h247.74l-62.558,63.46c-4.704,4.752-4.704,12.439,0,17.179c4.704,4.752,12.319,4.752,17.011,0l82.997-84.2C386.113,196.588,386.161,188.756,381.481,184.088z"></path></g></g></svg></div>
                                    {t}Выйти{/t}</a>
                            </div>
                        </div>
                    </div>
                    <div class="icon-account">
                        <i class="i-svg i-svg-user"></i>
                    </div>
                {else}
                    <div class="icon-account">
                        <a href="{$authorization_url}" title="{t}Войти или зарегистрироваться{/t}" class="rs-in-dialog">
                            <i class="i-svg i-svg-user"></i></a>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>