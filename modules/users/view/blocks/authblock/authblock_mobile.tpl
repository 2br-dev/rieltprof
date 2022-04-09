<li class="offcanvas__lk">
    {if $is_auth}
        <a href="{$router->getUrl('users-front-profile')}" class="offcanvas__lk-item">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.364 13.9275C17.3671 12.9306 16.1774 12.1969 14.8791 11.76C16.0764 10.8752 16.8543 9.45393 16.8543 7.85428C16.8543 5.17762 14.6767 3 12 3C9.32332 3 7.1457 5.17762 7.1457 7.85428C7.1457 9.45396 7.92364 10.8752 9.12089 11.76C7.82264 12.1968 6.63295 12.9306 5.63602 13.9275C3.93618 15.6274 3 17.8875 3 20.2915C3 20.6828 3.31722 21 3.70854 21H20.2915C20.6828 21 21 20.6828 21 20.2915C21 17.8875 20.0639 15.6274 18.364 13.9275ZM8.56285 7.85428C8.56285 5.959 10.1047 4.41712 12.0001 4.41712C13.8954 4.41712 15.4373 5.959 15.4373 7.85428C15.4373 9.74956 13.8954 11.2914 12.0001 11.2914C10.1047 11.2915 8.56285 9.74956 8.56285 7.85428ZM4.44995 19.5829C4.80834 15.7326 8.05769 12.7086 12 12.7086C15.9423 12.7086 19.1917 15.7326 19.5501 19.5829H4.44995Z"/>
            </svg>
            <span class="ms-2">{$current_user.name} {$current_user.surname}</span>
        </a>
    {else}
        {$referer = urlencode($url->server('REQUEST_URI'))}
        <a href="{$authorization_url}" class="rs-in-dialog offcanvas__lk-item">{t}Вход{/t}</a>
        <a href="{$router->getUrl('users-front-register', ['referer' => $referer])}" class="rs-in-dialog offcanvas__lk-item">{t}Регистрация{/t}</a>
    {/if}
</li>