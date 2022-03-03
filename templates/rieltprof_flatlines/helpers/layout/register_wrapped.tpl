{* Вспомогательный шаблон, назначен в конструкторе сайта у секции, оборачивающей "Главное содержимое" на странице Регистрация *}

<div class="form-style">
    <ul class="nav nav-tabs hidden-xs hidden-sm">
        <li><a href="{$router->getUrl('users-front-auth')}">{t}Вход{/t}</a></li>
        <li class="active"><a>{t}Регистрация{/t}</a></li>
    </ul>
    <div class="tab-content">
        <div class="visible-xs visible-sm hidden-md hidden-lg mobile_nav-tabs">
            <span>{t}Регистрация{/t}</span>
            <div class="right-arrow"><i class="pe-2x pe-7s-angle-up-circle"></i></div>
        </div>

        <div>
            {$wrapped_content}
        </div>

        <div class="visible-xs visible-sm hidden-md hidden-lg mobile_nav-tabs open">
            <span><a href="{$router->getUrl('users-front-auth')}">{t}Вход{/t}</a></span>
            <div class="right-arrow"><i class="pe-2x pe-7s-angle-up-circle"></i></div>
        </div>
    </div>
</div>