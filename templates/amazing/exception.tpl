<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{t error=$error.code}Ой, ошибочка %error{/t}</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="{$THEME_CSS}/colors.css">
    <link rel="stylesheet" href="{$THEME_CSS}/bootstrap.css">
    <link rel="stylesheet" href="{$THEME_CSS}/main.css">
    <style>
        {include file="%THEME%/colors.tpl"}
    </style>
</head>
<body class="container_large">
<header class="head-checkout">
    <div class="container">
        <div class="row g-xl-6 g-md-5 g-3 align-items-center">
            <div class="col-auto">
                {moduleinsert name="\Main\Controller\Block\Logo" indexTemplate="blocks/logo/logo_lite.tpl"}
            </div>
            <div class="col">
                <div class="h1">{$error.code}</div>
            </div>
            <div class="col-sm-auto d-none d-sm-block">
                <a class="btn btn-outline-primary" href="{$router->getRootUrl()}">{t}Вернуться в магазин{/t}</a>
            </div>
        </div>
    </div>
</header>
<main>
    <div class="section pt-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 order-md-last">
                    <img src="{$THEME_IMG}/decorative/nlo.svg" width="650" alt="">
                </div>
                <div class="col-md-6 text-center text-md-start">
                    <h1 class="exception__title">Ой, произошла ошибочка :(</h1>
                    <p>{$error.comment}</p>
                    <div class="mt-6"><a href="{$router->getRootUrl()}" class="btn btn-primary">{t}На главную{/t}</a></div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="footer-checkout">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="text-center col-lg-auto col-12">
                {moduleinsert name="\Main\Controller\Block\Logo" indexTemplate="blocks/logo/logo_lite_footer.tpl"}
            </div>
            <div class="col-lg col-12 d-flex justify-content-lg-end order-lg-last">
                {include file="%THEME%/helper/usertemplate/footer/payments.tpl"}
            </div>
            <div class="col-lg-auto col-12">
                {include file="%THEME%/helper/usertemplate/footer/copyright.tpl"}
            </div>
        </div>
    </div>
</footer>