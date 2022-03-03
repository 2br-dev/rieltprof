(function() {
    /**
     * Успешно разрешает Promise, если браузер поддерживает формат webP,
     * в противном случает отклоняет его
     *
     * @return Promise
     */
    function supportsWebp() {
        return new Promise(function(resolve, reject) {
            if (!self.createImageBitmap) {
                reject();
            } else {
                var webpData = 'data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=';

                fetch(webpData)
                    .then(function(response) {
                        return response.blob();
                    })
                    .then(function(blobData) {
                        return createImageBitmap(blobData).then(function() {
                            resolve();
                        }, function() {
                            reject();
                        });
                    }, function() {
                            reject();
                });
            }
        });
    }

    /**
     * Загружает скрипт и вызывает callback, по окончании загрузки
     *
     * @param url
     * @param callback
     * @return void
     */
    function loadScript(url, callback)
    {
        // Добавляем тег сценария в head
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;

        // Затем связываем событие и функцию обратного вызова.
        // Для поддержки большинства обозревателей используется несколько событий.
        script.onreadystatechange = callback;
        script.onload = callback;

        // Начинаем загрузку
        head.appendChild(script);
    }

    /**
     * Проверяет поддержку webP и в случае необходимости начинает загрузку polyfills для webP
     *
     * @return void
     */
    function start()
    {
        supportsWebp().catch(function() {

            var script = document.createElement('script');
            script.src = global.folder + '/resource/js/webpjs/polyfills.js';
            document.head.appendChild(script);

            var loadWebpJS = function() {
                $(document).ready(function(){
                    var script = document.createElement('script');
                    script.src = global.folder + '/resource/js/webpjs/webp-init.js';
                    script.defer = true;
                    document.body.appendChild(script);
                });
            };
            loadScript(global.folder + "/resource/js/webpjs/webp-hero.bundle.js", loadWebpJS);
        });
    }

    /**
     * Проверяет, поддерживаются ли Promise в браузере, если нет, то загружает
     * сперва Polyfill для Promise и только потом запускает этап проверки webP
     *
     * @return void
     */
    function init()
    {
        if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) {
            //Если это IE, то загружаем polyfill для Promise
            loadScript(global.folder + "/resource/js/webpjs/bluebird.core.min.js", function() {
                start();
            });
        } else {
            start();
        }
    }

    init(); //Запускаем процесс
})();