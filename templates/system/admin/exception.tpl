<!DOCTYPE HTML>
<html>
<head>
    <meta name="Content-type" content="text/html; Charset=utf-8">

    <link rel="stylesheet" type="text/css" href="{$Setup.CSS_PATH}/flatadmin/iconic-font/css/material-design-iconic-font.min.css" media="all" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{$Setup.CSS_PATH}/flatadmin/exception.css" media="all" rel="stylesheet">

    <title>{t code=$error.code}Ой, ошибочка %code{/t}</title>
</head>
<body class="exception-bg">
    <div class="admin-style">
        <div class="exception">
            <p><img src="{$Setup.IMG_PATH}/adminstyle/flatadmin/auth/rs-logo.svg"></p>
            <h2>{$error.code}</h2>
            <small>{$error.comment}</small>

            <footer>
                <a href="{$Setup.FOLDER|default:"/"}{$Setup.ADMIN_SECTION}/"><i class="zmdi zmdi-home"></i></a>
            </footer>
        </div>
    </div>
</body>
</html>