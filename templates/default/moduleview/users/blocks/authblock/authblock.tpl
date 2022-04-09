{if $is_auth}
<div class="authorized">
    <div class="top">
        <div class="user">
            {if $use_personal_account}
                {hook name="users-blocks-authblock:balance" title="{t}Блок авторизации:баланс{/t}"}
                    <a class="balance" href="{$router->getUrl('shop-front-mybalance')}" title="Средства на лицевом счете">{$current_user->getBalance(true, true)} </a>
                {/hook}
            {/if}
            {hook name="users-blocks-authblock:username" title="{t}Блок авторизации:имя пользователя{/t}"}
            <a  href="{$router->getUrl('users-front-profile')}" class="username">{$current_user.name} {$current_user.surname}</a>
            {/hook}
            </div>
        <div class="my">
            <div class="dropblock">
                <a class="dropdown-handler">{t}Личный кабинет{/t}</a>
                <ul class="dropdown">
                    {hook name="users-blocks-authblock:cabinet-menu-items" title="{t}Блок авторизации:пункты меню личного кабинета{/t}"}
                        <li><a href="{$router->getUrl('users-front-profile')}">{t}профиль{/t}</a></li>
                        <li><a href="{$router->getUrl('shop-front-myorders')}">{t}мои заказы{/t}</a></li>
                        {if $return_enable}
                            <li><a href="{$router->getUrl('shop-front-myproductsreturn')}">{t}мои возвраты{/t}</a></li>
                        {/if}
                        {if $use_personal_account}
                            <li><a href="{$router->getUrl('shop-front-mybalance')}">{t}лицевой счет{/t}</a></li>
                        {/if}
                    {/hook}
                        <li><a href="{$router->getUrl('users-front-auth', ['Act' => 'logout'])}" class="exit">{t}Выход{/t}</a></li>
                </ul>
            </div>
        </div>
    </div>    
</div>
{else}
<div class="auth alignright">
    {assign var=referer value=urlencode($url->server('REQUEST_URI'))}
    <a href="{$authorization_url}" class="first inDialog"><span>{t}Войти{/t}</span></a>
    <a href="{$router->getUrl('users-front-register', ['referer' => $referer])}" class="inDialog"><span>{t}Зарегистрироваться{/t}</span></a>
</div>
{/if}