{addcss file="common/owlcarousel/owl.carousel.min.css" basepath="common"}
{addjs file="owlcarousel/owl.carousel.min.js" basepath="common"}

{addjs file="%mobilesiteapp%/promo.js"}

{$app->autoloadScripsAjaxBefore()}
<div class="mobile-site-app">
    <h2>{t alias="Промо - заголовок сервиса"}Сервис <nobr>ReadyScript Mobile <sup>&reg;</sup></nobr>{/t}</h2>
    <p>{t alias="Промо - текст предоставление возможностей"}предоставляет возможность быстро создать и опубликовать в AppStore и GooglePlay мобильное<br>приложение для вашего интернет-магазина{/t}</p>

    <a target="_blank" href="{$app_api->getDemoUrl()}" class="btn btn-lg btn-yellow">{t}Посмотрите бесплатно{/t}</a>
    <p class="f-12 m-t-10">{t alias="Промо - текст как будет выглядеть"}как будет выглядеть ваше собственное<br>мобильное приложение прямо сейчас{/t}</p>

    <div class="info-columns">
        <div class="check-items">
            <h3>{t}С помощью мобильного приложения, вы можете{/t}:</h3>
            <ul>
                <li>{t}Повысить уровень предоставляемой вашей компанией сервиса{/t}</li>
                <li>{t}Своевременно уведомлять пользователей о скидках и акциях через push уведомления{/t}</li>
                <li>{t}Увеличить продажи за счет новой аудитории пользователей{/t}</li>
                <li>{t}Повысить уровень доверия к вам как со стороны пользователей, так и со стороны поисковых систем{/t}</li>
            </ul>
        </div>

        <div class="screenshots">
            <div class="phone"></div>

            <div class="owl-carousel images">
                <div><img src="{$mod_img}/screen/1.jpg"></div>
                <div><img src="{$mod_img}/screen/2.jpg"></div>
                <div><img src="{$mod_img}/screen/3.jpg"></div>
                <div><img src="{$mod_img}/screen/4.jpg"></div>
                <div><img src="{$mod_img}/screen/5.jpg"></div>
                <div><img src="{$mod_img}/screen/6.jpg"></div>
                <div><img src="{$mod_img}/screen/7.jpg"></div>
                <div><img src="{$mod_img}/screen/8.jpg"></div>
            </div>

            <div class="pages"></div>
        </div>

        <div class="description">
            <div class="item">
                <h3>{t}Интеграция{/t}</h3>
                <p>{t}Ваш интернет-магазин будет сразу интегрирован с вашим мобильным приложением, вам не нужно будет задумываться о технических деталях. Товары, категории, пункты меню, фильтры и другое будет автоматически получено из вашего интернет-магазина через API.{/t}</p>
            </div>
            <div class="item">
                <h3>{t}Индивидуализация{/t}</h3>
                <p>{t}ReadyScript Mobile предлагает набор возможностей по индивидуализации приложения, вы сможете загружать SplashScreen(заставку), выбирать цветовую схему приложения, загружать собственные баннеры, настраивать выборку товаров для главной страницы и др.{/t}</p>
            </div>
        </div>
    </div>
</div>
{$app->autoloadScripsAjaxAfter()}