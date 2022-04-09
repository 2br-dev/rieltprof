{if !$smarty.cookies.cookiesPolicy}
    {addjs file="core6/rsplugins/cookie.js" basepath="common"}
    {addjs file="rscomponent/cookiepolicy.js"}
    <!-- Политика куки -->
    <div class="cookies-policy cookies-policy_active">
        <div class="container">
            <div class="cookies-policy__inner">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        {t}Мы используем файлы cookie. Продолжив использование сайта, Вы соглашаетесь с политикой использования файлов cookie, обработки персональных данных и конфиденциальности.{/t}
                        <a target="_blank" href="{$router->getUrl('site-front-policy-agreement')}">{t}Подробнее{/t}</a>
                    </div>
                    <div class="col-sm-auto">
                        <button type="button" class="btn btn-primary w-100">{t}Принять{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}