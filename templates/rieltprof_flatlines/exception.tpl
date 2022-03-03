<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{t error=$error.code}Ой, ошибочка %error{/t}</title>
    <link rel="stylesheet" type="text/css" href="{$THEME_CSS}/libs/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$THEME_CSS}/exception.css">
</head>
<body class="exception-body">
    <div class="container-fluid">
        {moduleinsert name="\Catalog\Controller\Block\Category" indexTemplate="blocks/category/exception_category.tpl"}

        <div class="exception-message">
            <div class="exception-message_code"><span>{$error.code}</span></div>
            <div class="exception-message_text">{$error.comment}</div>
            <a href="{$site->getRootUrl()}" class="exception-message_link">{t}перейти на главную{/t}</a>
        </div>
    </div>

</body>
</html>