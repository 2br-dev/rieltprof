//инициализация webp-hero
var webpMachine =  new webpHero.WebpMachine({webpSupport: Promise.resolve(false)});

var tags = ['img', 'a'];
var attributes = ['src', 'data-src', 'href'];

webpMachine.polyfillDocument({},tags,attributes);

//поиск картинок в новом контенте(поддержка AJAX)
$(document).on('new-content', function () {
    webpMachine.polyfillDocument({},tags,attributes);
});