/**
 * Функция позволяет автоматически подгрудаеть контент по нажатию кнопки или прокрутке окна браузера если это необходимо.
 * Альтернатива rs.ajaxpagination.js
 */

//Значения по умолчанию
const ajaxPaginationDefaults = {
    method: 'get',      //каким методом делать запрос на сервер
    appendElement: '',  //какой элемент DOM дополнять новыми значениями
    findElement: null,  //в каком элементе DOM из response искать данные для вставки. Задайте false, чтобы вставлять весь response
    clickOnScroll: false,            //Автоматически выполнять подгрузку при попадании в зону видимости
    context: 'body',                //Ограничивает работу пагина данным элементом. Применяется когда на странице несколько списков с загрузкой
    loaderElement: '.rs-ajax-paginator',    //какой элемент DOM в response считать новым elseLoader'ом (если не будет найден, то elseLoader пропадет)
    loadingClass: 'inloading',          //класс, который добавляется кнопке "показать еще" во время загрузки контента
    scrollDisance: 100,                 //расстояние до кнопки "показать еще" в px, на котором уже начинает загрузка данных
    replaceBrowserUrl: false // подменять адрес в адресной строке браузера
};

var ajaxPaginationPlugin = {

    /**
     * Иницирует подгрузку контента при скроллинге
     *
     * @param {Event} event - событие прокрутки
     */
    loadContentOnScroll: function(event) {
        var items_with_scroll = document.querySelectorAll('.rs-ajax-paginator[data-click-on-scroll]');

        if (items_with_scroll){
            items_with_scroll.forEach(function(button) {
                var settings = Object.assign({}, ajaxPaginationDefaults, button.dataset); //Настройки

                if (settings.clickOnScroll){
                    var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
                    var height    = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                    var bottom    = scrollTop + height + settings.scrollDisance;

                    if (!button.classList.contains(settings.loadingClass) && bottom > button.getBoundingClientRect().top) {
                        ajaxPaginationPlugin.loadContent({
                            target: button
                        });
                    }
                }
            });
        }
    },

    /**
     * Подгрузка контента на одном элементе
     *
     * @param {Event} event - один элемент с параметрами для подгрузки
     */
    loadContent: function(event) {
        var button   = event.target;
        var settings = Object.assign({}, ajaxPaginationDefaults, button.dataset); //Настройки

        if (button.classList.contains(settings.loadingClass)){
            return false;
        }

        if (settings.findElement === null) {
            settings.findElement = settings.appendElement;
        }

        var href = button.href ? button.href : button.dataset['url'];
        button.classList.add(settings.loadingClass);

        fetch(href, {
            method: settings.method
        }).then(function(response){
            return response.json();
        }).then(function(response){
            var parser = new DOMParser();
            var parsed = parser.parseFromString(response.html, 'text/html');

            var appendData    = parsed.querySelector(settings.findElement).innerHTML;
            var context       = document.querySelector(settings.context);
            var appendElement = context.querySelector(settings.appendElement);

            //Вставим содержимое
            if (appendElement){
                appendElement.insertAdjacentHTML('beforeend', appendData);
            }else{
                console.error(t.lang('Не найден элемент (' + settings.appendElement + ') для побавления ответа от сервера'));
            }

            //Обновляем элемент "показать еще"
            var new_loader = parsed.querySelector(settings.loaderElement);
            if (new_loader){
                button.outerHTML = new_loader.outerHTML;
            }else{
                button.remove();
            }

            if (settings.replaceBrowserUrl) { //Запишем в историю браузера
                history.pushState(null, null, href);
            }

            ajaxPaginationPlugin.bindEvents(); //Перепривяжем события
            //Вызовем событие для обновления
            var event = new CustomEvent('new-content');
            document.querySelector('body').dispatchEvent(event);
        });
        return false;
    },

    /**
     * Инициализуем прослушивание событий
     */
    bindEvents: function(){
        document.querySelectorAll('.rs-ajax-paginator').forEach(function (button){
            button.removeEventListener('click', ajaxPaginationPlugin.loadContent);
            button.addEventListener('click', ajaxPaginationPlugin.loadContent);
        });

        var items_with_scroll = document.querySelectorAll('.rs-ajax-paginator[data-click-on-scroll]');
        if (items_with_scroll){
            window.removeEventListener('scroll', ajaxPaginationPlugin.loadContentOnScroll);
            window.addEventListener('scroll', ajaxPaginationPlugin.loadContentOnScroll);
        }
    },

    /**
     * Старт работы плагина
     */
    start: function() {
        document.addEventListener('DOMContentLoaded', function () {
            ajaxPaginationPlugin.bindEvents();
        });

        ajaxPaginationPlugin.bindEvents(); //Запустим, если у нас дом дерево уже было готово
    }
};

var ie = 0; //Посмотрим версию браузера, если это ie
try {
    var mIE = navigator.userAgent.match( /(MSIE |Trident.*rv[ :])([0-9]+)/ );
    if (mIE){
        ie = mIE[ 2 ];
    }

    if (ie > 0 && ie < 12){ //Если это старый Internet Explorer
        document.addEventListener('ie-polyfill-ready', function() {
            ajaxPaginationPlugin.start();
        });

        //Подгрузим полифил.
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.src  = '/resource/old-ie-polyfill.js';
        document.body.appendChild(s);
    }else{
        ajaxPaginationPlugin.start();
    }
} catch(e){
    console.error(e);
}

