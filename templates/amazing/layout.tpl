{* Базовый шаблон, который загржается на всех страницах *}
{strip}
    {addmeta http-equiv="X-UA-Compatible" content="IE=Edge" unshift=true}
    {addmeta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"}

    {addcss rel="preload" file="fonts/Manrope-Bold.woff2" as="font" type="font/woff2" crossorigin="anonymous" no_compress=true}
    {addcss rel="preload" file="fonts/Manrope-Regular.woff2" as="font" type="font/woff2" crossorigin="anonymous" no_compress=true}

    {addcss file="bootstrap.css"}
    {addcss file="swiper.css"}
    {addcss file="nouislider.css"}
    {addcss file="main.css"}
    {addcss file="skeleton.css"}
    {addcss file="%users%/verification.css"}

    {addjs file="libs/swiper.min.js" header=true}
    {addjs file="libs/nouislider.min.js"}
    {addjs file="libs/bootstrap.min.js"}
    {addjs file="libs/autocomplete.min.js"}
    {addjs file="libs/wnumb.min.js"}
    {addjs file="libs/loading-attribute-polyfill.min.js"}

    {addjs file="core6/rsplugins/scroller.js" basepath="common"}
    {addjs file="core6/rsplugins/modal.js" basepath="common"}
    {addjs file="core6/rsplugins/cookie.js" basepath="common"}
    {addjs file="core6/rsplugins/toast.js" basepath="common"}
    {addjs file="core6/rsplugins/opendialog.js" basepath="common"}
    {addjs file="rscomponent/theme.js"}
    {addjs file="%users%/rscomponent/verification.js"}
    {addjs file="%catalog%/rscomponent/changeoffer.js"}
    {if $THEME_SETTINGS.body_increased_width}
        {$app->setBodyClass('container_large', true)}
    {/if}
    {if $THEME_SETTINGS.show_offers_in_list}
        {addjs file="%catalog%/rscomponent/offerspreview.js"}
    {/if}
    {* Подскажем скриптам, какая модель поведения шапки должна быть *}
    {$app->setBodyAttr("data-sticky-header", $THEME_SETTINGS.sticky_header)}

    {include file="%THEME%/colors.tpl" assign="colors"}
    {$app->setAnyHeadData("<style>{$colors}</style>")|devnull}
{/strip}

{* Запускает рендеринг HTML на основе данных Конструктора сайта RadyScript *}
{$app->blocks->renderLayout()}

{* Мобильный таб-бар *}
{include file="%THEME%/helper/usertemplate/footer/mobile_bar.tpl"}

{if $THEME_SETTINGS.show_cookie_use_policy}
    {include file="%THEME%/helper/usertemplate/footer/cookie_policy.tpl"}
{/if}

{* Подключаем файл scripts.tpl, если он существует в папке темы. В данном файле
рекомендуется добавлять JavaScript код, который должен присутствовать на всех страницах сайта *}
{tryinclude file="%THEME%/scripts.tpl"}

{addjs file="core6/rs.jscore.js" basepath="common" unshift=true}

{if $THEME_SETTINGS.enable_jquery}
    {addjs file="libs/jquery.min.js" unshift=true header=true name="jquery"}
{/if}