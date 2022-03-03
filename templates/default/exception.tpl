<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ой, ошибочка {$error.code}</title>
<!--[if lt IE 9]>
<script src="res/js/html5shiv.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="{$THEME_CSS}/960gs/reset.css">
<link rel="stylesheet" type="text/css" href="{$THEME_CSS}/style.css">
</head>
<body>
    <header class="headerContainer header-exception">
        <a href="" class="logo"><img src="{$site_config.__logo->getUrl(206, 46)}"></a>
    </header> <!-- .headerContainer -->
    
    <section class="exceptionBlock">
        <div class="code">{$error.code}</div>
        <div class="message">{$error.comment}</div>
        <a href="{$site->getRootUrl()}" class="gomain">перейти на главную</a>
    </section>
</body>
</html>