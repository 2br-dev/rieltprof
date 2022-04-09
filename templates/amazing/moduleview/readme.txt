В данной папке можно переопределять TPL, CSS, JS файлы, которые находятся в папках /modules/*/view
Ссылка на полную документацию: https://readyscript.ru/dev-manual/dev_templates_extends.html

Пример 1:
Чтобы переопределить шаблон /modules/shop/view/cartpage.tpl, его нужно скопировать в папку
/templates/{папка темы}/moduleview/shop/cartpage.tpl, а затем модифицировать его.

Пример 2:
Чтобы переопределить CSS файл /modules/catalog/view/css/offer.css, его нужно скопировать в папку
/templates/{папка темы}/moduleview/catalog/css/offer.my.css, а затем модифицировать его.

Пример 3:
Чтобы переопределить JS файл /modules/users/view/js/rscomponent/verification.js, его нужно скопировать в папку
/templates/{папка темы}/moduleview/users/js/rscomponent/verification.my.js, а затем модифицировать его.