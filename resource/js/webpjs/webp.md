WebP формат
======
Требования:

- PHP >=7.1.0 
- PHP GD собранная с флагом  `--with-vpx-dir=`

Генерация webp-миниатюр создает файлы с расширением `<название_миниатюры>.webp` для корректной 
работы подсистемы изображений.



Поддержка WebP в браузерах
----
В системе предусмотрена конвертация WebP в PNG на стороне клиента в браузерах без данного формата.
Поддержку браузерами можете посмотреть на сайте [Сan I Use](https://caniuse.com/#search=webp)


Все подключаемые файлы находится в **resource/js/webpjs**

- **rs.webpcheck.js** - отвечает за проверку поддерживает ли браузер WebP и подключаение скриптов
- **polyfills.js** - отвечает за поддержку кода в браузерах не поддерживающих webp-формат
- **webp-hero.bundle.js** - отвечает за перекодирование картинки из WebP в PNG
- **webp-init.js** - инициализирует проверку страницы на наличие изображений в формате webp и их конвертацию в PNG.

Сумарно все скрипты реализуют один алгоритм: Ищутся все элементы переданные в `documentPolyfill()`, затем проверяется 
ссылка на файл в переданных атрибутах, если у нее есть расширение .webp, то картинка перекодируется в png и подставляется ее исходный код
 в нужный атрибут.

```plantuml
start
:Запуск rs.webpcheck.js;
if (Браузер поддерживает WebP?) then(Да)
  :подключение polyfills.js;
repeat
  :подключение webp-hero.bundle.js;
repeat while (Подключился?)
  :Подключение webp-init.js;
:Поиск всех заданных элементов;
while (Расширение картинки в переданных атрибутах .webp?)
  :Запускаем метод polyfillImage()\nкоторый заменяет ссылки в атрибутах \nна перекодированную картинку;
endwhile

else (Нет)
endif

stop
```


###Как настроить поддержку webp если картинки находятся не в стандартных блоках img?

В файле **webp-init.js**  Можно использовать функцию `webpMachine.polyfillDocument();`, которая ищет
 картинки на странице и запускает перекодировку всего документа в нужных тегах и атрибутах.
````javascript
async function polyfillDocument({document = window.document},tags, attributes){
		if (await this.webpSupport) return null;

        for(let i = 0; i<tags.length; i++) {
            for(const image of Array.from(document.querySelectorAll(tags[i]))){
                try {
                    await this.polyfillImage(image, attributes);
                }
                catch (error) {
                    error.name = WebpMachineError.name;
                    error.message = `webp image polyfill failed for image "${image}": ${error}`;
                    throw error;
                }
            }
        }
	}
````

Функция принимает на вход 3 параметра:
 
 - `{}` - обязательный параметр (по умолчанию присваивается document)
 - `tags: Array<sting>` - массив строк тегов для поиска
 - `attributes: Array<string>` - массив строк атрибутов в которых возможно содеражание ссылки на картинку
 
 Или функцию `webpMachine.polyfillImage();`, которая перекодирует заданные атрибуты определенного элемента.
 ````javascript
async function polyfillImage(image, attributes) {
        if (await this.webpSupport) return

        for(let i = 0; i < attributes.length; i++) {
            const src = image.getAttribute(attributes[i]);

            if (/\.webp$/i.test(src)) {
                if (this.cache[src]) {
                    image.setAttribute(attributes[i],this.cache[src]);
                    return
                }
                try {
                    const webpData = await loadBinaryData(src);
                    const pngData = await this.decode(webpData);
                    this.cache[src] = pngData;
                    image.setAttribute(attributes[i], pngData);
                }
                catch (error) {
                    error.name = WebpMachineError.name;
                    error.message = `failed to polyfill image "${src}": ${error.message}`;
                    throw error;
                }
            }
        }
    }
````
Функция принимает на вход 2 параметра:

- `image: Element` - елемент html страницы в котором будут искаться атрибуты
- `attributes: Array<string>` - массив строк, атрибуты в которых будет искаться ссылка на картинку

####Пример кода с использованием функций:

Пример с использованием `polyfillDocument()`
```javascript
//инициализация webp-hero
var webpMachine =  new webpHero.WebpMachine({webpSupport: Promise.resolve(false)});

//Объявление тегов
var tags = ['img', 'a'];
//Объявление атрибутов в которых может быть ссылка на картинку
var attributes = ['src', 'data-src', 'href'];

//Запуск перекодировки
webpMachine.polyfillDocument({},tags,attributes);

//поиск картинок в новом контенте(поддержка AJAX)
$(document).on('new-content', function () {
    webpMachine.polyfillDocument({},tags,attributes);
});
```

Пример с использованием `polyfillImage()`
```javascript
//инициализация webp-hero
var webpMachine =  new webpHero.WebpMachine({webpSupport: Promise.resolve(false)});

/Собственная реализация поиска тегов
async function processImages() {
    for (const image of Array.from(document.getElementsByTagName("div"))) {
            // console.log('Обрабатываю картинку', image);
            try {
                await webpMachine.polyfillImage(image, ['data-src']);
            } catch(e) {
                console.log(e);
            }
    }
};

//поиск картинок и их замена
processImages();

//поиск картинок в новом контенте(поддержка AJAX)
$(document).on('new-content', function () {
    processImages();
});
```


 
 
Данный код - дополненный и пересобранный в bundle webp-hero [**GitHub**](https://github.com/chase-moskal/webp-hero)

