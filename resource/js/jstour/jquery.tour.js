/**
 * Файл с описанием схемы интерактивного тура по административной панели ReadyScript
 *
 * @author RedyScript lab.
 */
$(function() {

    var isCategoryExpanded = function(element) {
        return (!element.closest('.left-up').length
        && window.matchMedia('(min-width: 992px)').matches)
    }

    /**
    * Тур по первичной настройке сайта
    */
    var tourTopics = {
        'base': lang.t('Базовые настройки'),
        'products': lang.t('Категории и Товары'),
        'menu': lang.t('Меню'),
        'article': lang.t('Новости'),
        'delivery': lang.t('Способы доставки'),
        'payment': lang.t('Способы оплаты'),
        'debug': lang.t('Правка информации на сайте')
    }

    var welcomeTour = {}

    welcomeTour.commonStart =  {
        'index': {
                url: '/',
                topic: tourTopics.base,
                type: 'dialog',
                message: lang.t(
                '<div class="tourIndexWelcome">Рады приветствовать Вас!</div>\
                    <div class="tourIndexBlock">\
                        <div class="tourBorder"></div>\
                        <p class="tourHello">Хотели бы Вы пройти<br> интерактивный курс обучения?</p>\
                        <div class="tourLegend">\
                            <a class="tourTop first indexTipToAdmin" data-step="index-tip-toadmin">Базовые настройки</a>\
                            <a class="adminCatalogAddInfo" data-step="admin-catalog-add-info">Категории<br> и Товары</a>\
                            <a class="tourTop menuCtrl" data-step="menu-ctrl">Текстовые<br> страницы<br> (Меню)</a>\
                            <a class="articleCtrl" data-step="article-ctrl">Новости</a>'+
                            (global.scriptType != 'Shop.Base' ? '<a class="tourTop shopDeliveryCtrl" data-step="shop-deliveryctrl">Способы доставки</a>\
                            <a class="shopPaymentCtrl" data-step="shop-paymentctrl">Способы оплаты</a>' : '') +
                            '<a class="tourTop debugIndex" data-step="debug-index">Правка информации на сайте</a>\
                        </div>\
                    </div>\
                </div>', null, 'tourWelcome'),
                buttons: {
                    yes: {
                        text: lang.t('Да, пройти курс с начала'),
                        step: 'index-tip-toadmin'
                    },
                    no: false
                }
            },
            
            'index-tip-toadmin': {
                url: '/',
                topic: tourTopics.base,            
                tip: {
                    element: '.header-panel .to-admin',
                    tipText: lang.t('Все настройки интернет-магазина располагаются в административной панели. Нажмите на кнопку быстрого перехода в панель администрирования.')
                }
            },
            
            'admin-index': {
                url: '%admin%/',
                topic: tourTopics.base,            
                type: 'info',
                message: lang.t('Это главный экран панели управления магазином. Здесь могут размещаться информационные виджеты с самой актуальной информацией по ключевым показателям магазина.'),
                tips: [
                    {
                        element: '.addwidget',
                        tipText: lang.t('Кнопка "Добавить виджет" откроет список имеющихся в системе виджетов'),
                        position: ['center', 'bottom'],  //Положение относительно element - [(left|center|right),(top|middle|bottom)]
                        fixed:true,
                        animation: 'bounceInDown'
                    },
                    {
                        element: '.action-zone .action.to-site',
                        tipText: lang.t('Быстрый переход на сайт'),
                        position: ['left', 'bottom'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.action-zone .action.clean-cache',
                        tipText: lang.t('Кнопка для очистки кэша системы'),
                        position: ['left', 'bottom'],
                        correctionY: 50,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.panel-menu .current',
                        tipText: lang.t('Показан текущий сайт. Если управление ведется несколькими сайтами, то при наведении будет показан список сайтов.'),
                        position: ['left', 'bottom'],
                        correctionY: 100,
                        css: {
                            width: 300
                        },
                        animation: 'bounceInDown'
                    }
                ]
            },
            
            'admin-index-to-siteoptions': {
                url: '%admin%/',
                topic: tourTopics.base,
                tip: {
                    element: ['a[data-url$="/menu-ctrl/"]', '#menu-trigger'],
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Настройка сайта</i>'),
                    correctionX: 40,
                    fixed: true
                },
                onStart: function() {
                    $('a[href$="/site-options/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('a[href$="/site-options/"]').removeClass('menuTipHover');
                }
            },
            
            'admin-siteoptions': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,            
                type: 'info',
                message: lang.t('В этом разделе необходимо настроить основные параметры текущего сайта, к которым относятся: '+
                '<ul><li>контактные данные администратора магазина (будут использоваться для уведомлений обо всех событиях в интернет-магазине);</li>'+
                '<li>реквизиты организации продавца (будут использоваться для формирования документов покупателям);</li>'+
                '<li>логотип интернет-магазина;</li>'+
                '<li>тема оформления сайта;</li>'+
                '<li>параметры писем, отправляемых интернет-магазином.</li></ul>', null, 'tourAdminSiteOptions'),
                tips:[
                    {
                        element: '.tab-nav li:eq(3)',
                        tipText: lang.t('Заполните сведения во всех вкладках. При наведении мыши на символ вопроса, расположенный справа от поля, отобразится подсказка по нзначению и заполнению поля.'),
                        position: ['center', 'bottom'],
                        correctionX:50,
                        css: {
                            width:300
                        },
                        animation: 'slideInDown'
                    }
                ],
                buttons: {
                    next: 'admin-siteoptions-save'
                }
            },
            
            'admin-siteoptions-save': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,            
                tip: {
                    element: '.btn.crud-form-apply',
                    tipText: lang.t('Заполните сведения во всех вкладках, расположенных выше. Далее, нажмите на зеленую кнопку, чтобы сохранить изменения.'),
                    correctionY: -15,
                    bottom: true,
                    css: {
                        position: 'fixed'
                    }
                },
                watch: {
                    element: '.btn.crud-form-apply',
                    event: 'click',
                    next:'admin-siteoptions-to-products'
                }
            },
            
            'admin-siteoptions-to-products': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,
                tip: {
                    element: ['a[data-url$="/catalog-ctrl/"]', '#menu-trigger'],
                    tipText: lang.t('Теперь необходимо добавить товары, для этого перейдите в раздел <i>Товары &rarr; Каталог товаров</i>'),
                    correctionX: 40,
                    css: {
                        zIndex: 50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/catalog-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/catalog-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'admin-catalog-add-info': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                type: 'info',
                message: lang.t('В этом разделе происходит управление товарами и категориями товаров. \
                            Обратите внимание на расположение кнопок создания объектов.\
                            <p>На следующем шаге мы попробуем создать, для примера, одну категорию и один товар. \
                            По аналогии вы сможете наполнить каталог собственными категориями и товарами.', null, 'tourAdminCatalogAddInfo'),
                tips: [
                    {
                        element: '.treehead .addspec',
                        tipText: lang.t('Создать спец.категорию <br>(например: новинки, лидеры продаж,...)'),
                        position:['left', 'bottom'],
                        whenUse: isCategoryExpanded,
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.treehead .add',
                        tipText: lang.t('Создать категорию товаров'),
                        whenUse: isCategoryExpanded,
                        position:['left', 'bottom'],
                        correctionY:60,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.c-head .btn-group:contains("'+lang.t("добавить товар")+'")',
                        tipText: lang.t('Создать товар'),
                        position:['left', 'middle'],
                        correctionX:-30,
                        animation: 'fadeInLeft'
                    },
                    {
                        element: '.c-head .btn-group:contains("'+lang.t("Импорт/Экспорт")+'")',
                        tipText: lang.t('Через эти инструменты можно массово загрузить товары, <br>категории в систему через CSV файлы. Подробности в <a target="_blank" href="http://readyscript.ru/manual/catalog_csv_import_export.html">документации</a>.'),
                        animation: 'slideInDown'
                        
                    }
                ],
                buttons: {
                    next: 'admin-siteoptions-save'
                }
            },
            
            'admin-catalog-add-dir': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: [
                        {
                            selector: '.treehead .add',
                            whenUse: isCategoryExpanded
                        },
                        {
                            selector: '.c-head .btn.btn-success',
                            left: true,
                            correctionX:-20
                        }],
                    tipText: lang.t('Перед добавлением товара нужно создать его категорию. Для примера, создадим тестовую категорию "<b>Холодильники</b>". Нажмите на кнопку <i>создать категорию</i> или найдите это действие в выпадающем списке зеленой кнопки вверху-справа.'),
                },
                watch: {
                    element: '.treehead .add, .c-head a:contains("добавить категорию")',
                    event: 'click',
                    next: 'admin-catalog-add-dir-form'
                }
            },
            
            //Шаги, связанные с добавлением категории
            
            'admin-catalog-add-dir-form': {
                url: '%admin%/catalog-ctrl/?pid=0&do=treeAdd',
                topic: tourTopics.products,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="name"]',
                        tipText: lang.t('Укажите название - <b>Холодильники</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите Псевдоним - это имя на английском языке, которое будет использоваться для построения URL-адреса страницы'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('Перейдите на вкладку <i>Характеристики</i>. Для примера создадим 1 характеристику (мощность), <br>\
                                  которая обязательно будет присутствовать у всех товаров создаваемой категории.'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains("'+lang.t('Характеристики')+'")',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-actions .add-property',
                        tipText: lang.t('Нажмите добавить характеристику'),
                        watch: {
                            event: 'click',
                        },
                        onStart: function() {
                            $('.frame[data-name="tab2"]').append('<div style="height:110px" id="tourPlaceholder1"></div>');
                        }
                    },
                    {
                        element: '.property-form .p-title',
                        tipText: lang.t('Укажите название - <b>Мощность</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.property-form .p-type',
                        tipText: lang.t('Укажите тип - <b>Список</b>, чтобы в дальнейшем включить фильтр по данной харктеристике'),
                        checkPattern: /^(list)$/gi
                    },
                    {
                        element: '.property-form .p-unit',
                        tipText: lang.t('Укажите единицу измерения - <b>Вт</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.property-form .p-new-value',
                        tipText: lang.t('Укажите возможное значение мощности - <b>1000</b> и нажмите справа <b>добавить</b>'),
                        watch: {
                            element: '.p-add-new-value',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-form .p-new-value',
                        tipText: lang.t('Укажите еще одно возможное значение мощности - <b>2000</b> и нажмите справа <b>добавить</b>'),
                        watch: {
                            element: '.p-add-new-value',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-form .add',
                        tipText: lang.t('<i>Добавьте</i> характеристику к категории'),
                        css: {
                            marginTop:46
                        },
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        waitNewContent: true,
                        element: '.property-container .property-item .h-public',
                        tipText: lang.t('Установите флажок <i>Отображать в поиске на сайте</i>, чтобы по данной характеристике можно было отфильтровать товары на сайте. Подробности в <a href="http://readyscript.ru/manual/catalog_categories.html#cat_tab_characteristics" target="_blank">документации</a>.'),
                        checkPattern: true,
                        correctionX: -230,
                        css: {
                            width:300
                        }
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('Нажмите на кнопку <i>Сохранить</i>, чтобы создать категорию'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'admin-catalog-add-product'
                        }
                    }
                ]
            },        
            
            'admin-catalog-add-product': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: '.c-head .btn.btn-success:contains("'+lang.t('добавить товар')+'")',
                    tipText: lang.t('Чтобы добавить товар, нажмите на зеленую кнопку <i>Добавить товар</i>'),
                },
                watch: {
                    element: '.c-head .btn.btn-success:contains("'+lang.t('добавить товар')+'")',
                    event: 'click',
                    next: 'admin-catalog-add-product-form'
                }
            },
            
            'admin-catalog-add-product-form': {
                url: '%admin%/catalog-ctrl/?dir=0&do=add',
                topic: tourTopics.products,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название товара - <b>Холодильник ТОМАС</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите любое URL имя на англ.языке. <br>Будет использовано для создания адреса страницы товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '#tinymce-description_parent',
                        tinymceTextarea: '#tinymce-description',
                        tipText: lang.t('Укажите описание товара'),
                        bottom:true,
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="barcode"]',
                        tipText: lang.t('Укажите артикул товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name^="excost"]:first',
                        tipText: lang.t('Укажите стоимость товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name^="xdir[]"]',
                        tipText: lang.t('Выберите категорию - <b>Холодильники</b>'),
                        checkSelectValue: /^.*$/gi,
                        correctionX:150,
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('Теперь добавим характеристику товару, для этого перейдите на вкладку <i>Характеристики</i>'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains('+lang.t('Характеристики')+')',
                            event: 'click',
                        }
                    },
                    {
                        ifTrue: function() {
                            return !$('.item-title:contains("' + lang.t('Мощность') + '")').length>0;
                        },
                        elseStep: 'myval_noajax',
                        element: '.property-actions .add-property',
                        tipText: lang.t('Нажмите <i>Добавить характеристику</i>'),
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        element: '.property-form .p-proplist',
                        tipText: lang.t('Выберите характеристику - <b>Мощность</b>'),
                        checkPattern: /^\d+$/gi
                    },
                    
                    {
                        element: '.property-form .add',
                        tipText: lang.t('<i>Добавьте</i> характеристику к товару'),
                        css: {
                            marginTop:46
                        },
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        label: 'myval_ajax',
                        waitNewContent: true,
                        ifTrue: function() {
                            //Если есть флажок - "задать персональное значение"
                            return $(lang.t('.property-item:contains("Мощность") .h-useval')).length>0;
                        },
                        element: lang.t('.property-item:contains("Мощность") .h-useval'),
                        tipText: lang.t('Отметьте флажок, чтобы задать индивидуальное значение характеристики для товара'),
                        checkPattern: true,
                        next: 'propval'
                    },
                                    
                    {
                        label: 'myval_noajax',
                        ifTrue: function() {
                            //Если есть флажок - "задать персональное значение"
                            return $('.property-item:contains("Мощность") .h-useval').length>0;
                        },
                        element: '.property-item:contains("Мощность") .h-useval',
                        tipText: lang.t('Отметьте флажок, чтобы задать индивидуальное значение характеристики для товара'),
                        checkPattern: true
                    },
                    {
                        label: 'propval',
                        element: '.property-item:contains("Мощность") .inline-item:contains("1000") input',
                        tipText: lang.t('Укажите, что мощность холодильника - 1000 Вт'),
                        checkPattern: true
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('На закладке <i>Комплектации</i> можно задать остатки, а также <a href="http://readyscript.ru/manual/catalog_products.html#catalog_products_tab_offers">вариации(комплектации)</a> товара.'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains('+lang.t("Комплектации")+')',
                            event: 'click',
                        }
                    },
                    {
                        element: '.crud-form [name^="offers[main][stock_num]"]:first',
                        tipText: lang.t('Укажите остаток товара на всех складах - <i>10</i>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('Загрузите фотографии на вкладке <i>Фото</i>'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains(' + lang.t("Фото") + ')',
                            event: 'click',
                        }
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('При желании вы можете заполнить сведения на оставшихся вкладках товара.<br> Затем нажмите на кнопку <i>Сохранить</i>, чтобы создать товар'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'admin-catalog'
                        },
                        
                    }
                ],
            },
            
            'admin-catalog': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                type: 'info',
                message: lang.t('Товар и категория добавлены. \
                          В дальнейшем Вы часто будете пользоваться данным разделом, чтобы корректировать описания товаров, цены, количество товаров, и т.д. \
                          Предлагаем ознакомиться с основными элементами управления, присутствующими на данной странице.'),
                tips: [
                    {
                        element: '.rs-table .options',
                        tipText: lang.t('Настройка состава колонок <br>таблицы и сортировки по-умолчанию'),
                        animation: 'slideInDown'
                    },
                    {
                        element: '.rs-table thead th:eq(4)',
                        tipText: lang.t('При нажатии на заголовок колонки <br>можно изменять сортировку данных в таблице'),
                        correctionY:70,
                        correctionX:40,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.right-column .bottom-toolbar .crud-multiedit',
                        tipText: lang.t('В нижней панели представлены действия (редактировать, удалить), <br>которые можно применить ко всем <br>отмеченным элементам (товарам или категориям).'),
                        position: ['right', 'top'],
                        animation: 'bounceInDown',
                        css: {
                            position:'fixed'
                        }
                    },
                    {
                        element: '.treehead .showchilds-on, .showchilds-off',
                        tipText: lang.t('Включить/выключить показ товаров из вложенных категорий'),
                        whenUse:isCategoryExpanded,
                        position:['right', 'top'],
                        correctionY:-20,
                        animation: 'rotateIn'
                    },                
                    {
                        element: '.rs-table .chk',
                        tipText: lang.t('Можно отметить товары как на одной,<br> так и на всех страницах'),
                        position:['right', 'bottom'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.treebody > li:eq(1) .move',
                        tipText: lang.t('Сортируйте категории с помощью перетаскивания'),
                        whenUse:isCategoryExpanded,
                        position:['right', 'bottom'],
                        animation: 'slideInDown'
                    }
                ],
                onStart: function() {
                    var act = function() {
                        $('.rs-table .chk').addClass('chk-over');
                        $('.treebody > li:eq(3)').addClass('over');
                        $('.treebody > li:eq(1)').addClass('drag');
                        $('.rs-table tbody tr:eq(7)').addClass('over');
                    }
                    
                    $('body').on('new-content.tour', act);
                    act();
                },
                onStop: function() {
                    $('.rs-table .chk').removeClass('chk-over');
                    $('.treebody > li:eq(3)').removeClass('over');
                    $('.treebody > li:eq(1)').removeClass('drag');
                    $('.rs-table tbody tr:eq(7)').removeClass('over');
                }
            },
            
            'to-menu-ctrl': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: 'a[data-url$="/menu-ctrl/"]',
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Меню</i>'),
                    correctionX: 40,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/menu-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/menu-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'menu-ctrl': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.menu,
                type: 'info',
                message: lang.t('В данном разделе можно создавать иерархию страниц сайта разных типов, которые могут быть доступны пользователям через меню. Каждой странице будет присвоен определенный URL адрес, по которому она будет доступна из браузера. \
                         <p>Например, если вы желаете: <ul>\
                         <li>создать страницу с какой-либо текстовой информацией, то необходимо создать пункт меню с типом "<b>Статья</b>".</li>\
                         <li>создать страницу, на которой должны быть представлены функциональные блоки с каким-либо более сложным поведением (например, форма обратной связи), то необходимо создать пункт меню с типом "<b>Страница</b>". \
                         Далее эту страницу можно будет настроить в разделе Веб-сайт &rarr; Конструктор сайта.</li>\
                         <li>создать простую ссылку в меню, то используйте тип "<b>Ссылка</b>" для такого пункта меню.</li>\
                         </ul><p>Ознакомьтесь с основными функциональными кнопками на данной странице. \
                         На следующем шаге, мы создадим для примера пункт меню с информацией о рекламной акции в интернет-магазине.', null, 'tourMenuCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn:contains("' + lang.t('добавить пункт меню') + '")',
                        tipText: lang.t('Создать новый пункт меню'),
                        animation: 'bounceInDown'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('Импорт/Экспорт') + '")',
                        tipText: lang.t('Через эти инструменты можно массово <br>загрузить пункты меню в систему через CSV файлы.'),
                        animation: 'slideInDown',
                        correctionY:60
                    },
                    {
                        element: '.activetree  .allplus',
                        tipText: lang.t('Развернуть отображение дерева пунктов меню'),
                        position:['right', 'bottom'],
                        animation: 'slideInLeft'
                        
                    },
                    {
                        element: '.activetree  .allminus',
                        tipText: lang.t('Свернуть отображение дерева пунктов меню'),
                        position:['right', 'middle'],
                        correctionX:40,
                        animation: 'slideInDown'
                    }
                ]
            },
            
            'menu-ctrl-add': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.menu,
                tip: {
                    element: '.c-head .btn:contains("' + lang.t("добавить пункт меню") + '")',
                    tipText: lang.t('Добавим на сайте раздел <b>Акция</b>, в котором будет представлена текстовая информация. \
                              Нажмите на кнопку <i>Добавить пункт меню</i>')
                },
                watch: {
                    element: '.c-head .btn:contains("' + lang.t("добавить пункт меню") + '")',
                    event: 'click',
                    next: 'menu-ctrl-add-form'
                }
            },
            
            'menu-ctrl-add-form': {
                url: '%admin%/menu-ctrl/?do=add',
                topic: tourTopics.menu,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название пункта меню - <b>Акция</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите любое название пункта меню на Англ. языке. <br>Оно будет использоваться для построения URL адреса раздела.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form #tinymce-content_ifr',
                        tinymceTextarea: '#tinymce-content',
                        tipText: lang.t('Укажите информацию об акции'),
                        bottom:true,
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.mce-ico.mce-i-image',
                        tipText: lang.t('Используйте кнопку с лупой, чтобы добавить изображения к тексту'),
                        correctionY:10,
                        correctionX:-50,
                        checkTimeout: 5000
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('После ввода всей необходимой текстовой информации, нажмите \
                                 <br>на кнопку <i>Сохранить</i>, чтобы создать раздел на сайте, который отобразится в меню'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-article-ctrl'
                        },
                        
                    }
                ]
            },
            
            'to-article-ctrl': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.article,
                tip: {
                    element: '.side-menu a:contains("'+lang.t('Веб-сайт')+'")',
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Контент</i>'),
                    correctionX: 50,
                    css: {
                        zIndex: 50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/article-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/article-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'article-ctrl': {
                url: '%admin%/article-ctrl/',
                topic: tourTopics.article,
                type: 'info',
                message: lang.t('На этой странице происходит управление списками текстовых материалов, например новостями.\
                         <p>Для добавления новости на сайте, достаточно создать статью в соответствующей категории.\
                         <p>Также в этом разделе административной панели могут размещаться статьи, используемые темой оформления на различных страницах.'),
                tips: [
                    {
                        element: '.treehead .add',
                        tipText: lang.t('Создать категорию статей'),
                        position:['right', 'top'],
                        whenUse: isCategoryExpanded,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('добавить статью') + '")',
                        tipText: lang.t('Создать статью'),
                        animation: 'slideInDown'
                    },
                    {
                        element: '.treebody > li:eq(0)',
                        tipText: lang.t('Категория статей'),
                        whenUse: isCategoryExpanded,
                        position:['right', 'middle'],
                        animation: 'slideInLeft',
                        correctionX:40
                    }
                ]
            }
    }

    welcomeTour.commonEnd = {
            'to-index': {
                url: global.scriptType != 'Shop.Base' ? '%admin%/shop-paymentctrl/' : '%admin%/article-ctrl/',
                topic: tourTopics.payment,
                tip: {
                    element: '.header-panel .to-site',
                    tipText: lang.t('Основные настройки в административной панели произведены. Желаете добавлять товары, категории, новости, и т.д., не заходя в панель администрирования? Нажмите на кнопку <i>Перейти на сайт</i>, чтобы узнать как.')
                },
                watch: {
                    element: '.header-panel .to-site',
                    event: 'click',
                    next: 'debug-index'
                }
            },
            
            'debug-index': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.debug-mode-switcher .rs-switch',
                    tipText: lang.t('Включите режим отладки, чтобы редактировать элементы прямо на странице'),
                    correctionY:40
                },
                whileTrue: function() {
                    return $('.debug-mode-switcher .rs-switch:not(.on)').length;
                }            
            },
            
            'debug-text': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.module-wrapper:has([data-debug-contextmenu]):first',
                    tipText: lang.t('Любой товар, категорию, пункт меню, и т.д. на данной странице можно отредактировать, удалить или создать, кликнув над ним правой кнопкой мыши и выбрав необходимое действие.'),
                    correctionY:10,
                    css: {
                        zIndex:3
                    },
                    notFound: 'finish'
                },
                watch: {
                    element: '',
                    event: 'showContextMenu',
                    next: 'debug-block-text'
                },
                checkTimeout: 15000
            },
            
            'debug-block-text': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.module-wrapper:eq(0) .debug-icon-blockoptions',
                    tipText: lang.t('Любой блок можно настроить, нажав на иконку с изображением гаечного ключа.'),
                    correctionY:10,
                    notFound: 'finish',
                    css: {
                        zIndex:3
                    }
                },
                onStart: function() {
                    $('.module-wrapper:eq(0)').addClass('over');
                },                
                onStop:  function() {
                    $('.module-wrapper:eq(0)').removeClass('over');
                },      
                watch: {
                    element: '.debug-icon-blockoptions',
                    event: 'click',
                    next: 'finish'
                },
                checkTimeout: 15000
            },
            
            'finish': {
                url: '/',
                topic: tourTopics.debug,
                type:'dialog',
                message: lang.t('<span class="finishText">Интерактивный курс по базовым настройкам<br> интернет-магазина успешно завершен.</span> <br>Более подробную информацию по возможностям платформы ReadyScript можно найти в <a href="http://readyscript.ru/manual/" target="_blank"><u>документации</u></a>.'),
                buttons: {
                    finish: {
                        text: lang.t('Закрыть окно'),
                        step: false                        
                    },
                    docs: {
                        text: lang.t('Документация'),
                        attr: {
                            href: 'http://readyscript.ru/manual/',
                            target: '_blank'
                        },
                        step:false
                    }
                }
            }
    }

    welcomeTour.shop = {
        'to-shop-deliveryctrl': {
                url: '%admin%/article-ctrl/',
                topic: tourTopics.article,            
                tip: {
                    element: '.side-menu > li > a[data-url$="/shop-orderctrl/"]',
                    tipText: lang.t('Теперь перейдем к настройке параметров, связанных с заказами. Перейдите в раздел <i>Магазин &rarr; Доставка &rarr; Способы доставки</i>'),
                    position:['right', 'top'],
                    bottom:true,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu a[href$="/shop-regionctrl/"]:first').addClass('menuTipHover');
                    $('.side-menu a[href$="/shop-deliveryctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu a[href$="/shop-regionctrl/"]:first').removeClass('menuTipHover');
                    $('.side-menu a[href$="/shop-deliveryctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'shop-deliveryctrl': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                type:'info',
                message: lang.t('В этом разделе необходимо произвести настройку способов доставок, которые будут\
                         предложены пользователю во время оформления заказа. \
                         <p>До настройки данного раздела, необходимо иметь представление о том, как вы будете доставлять товары вашим покупателям и по каким ценам.\
                         <p>Ознакомьтесь с основными инструментами представленными на данной странице.\
                         <p>На следующем шаге, создадим для примера, "доставку по городу", которая будет стоить 500 руб.', null, 'tourShopDeliveryCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn.btn-success',
                        tipText: lang.t('Добавить способ доставки'),
                        position:['center', 'top'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('Импорт/Экспорт') + '")',
                        tipText: lang.t('Через эти инструменты можно массово загрузить способы доставок через CSV файлы.'),
                        animation: 'slideInDown',
                        correctionY:60
                    },
                    {
                        element: '.rs-table .sortdot',
                        tipText: lang.t('Сортировать способы доставок можно с помощью перетаскивания'),
                        position: ['right', 'top'],
                        animation: 'slideInLeft'
                    }
                ]
            },
            
            'shop-deliveryctrl-add': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                tip: {
                    element: '.c-head .btn.btn-success',
                    tipText: lang.t('Добавим, для примера, способ доставки <b>по городу</b>. Нажмите на кнопку <i>Добавить способ доставки</i>')
                },
                watch: {
                    element: '.c-head .btn.btn-success',
                    event: 'click',
                    next: 'shop-deliveryctrl-add-form'
                }            
            },
            
            'shop-deliveryctrl-add-form': {
                url: '%admin%/shop-deliveryctrl/?do=add',
                topic: tourTopics.delivery,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название доставки - <b>по городу</b>. Будет отображено во время оформления заказа в списке возможных способов доставки.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="description"]',
                        tipText: lang.t('Укажите условия или подробности доставки, которые будут отображаться под названием'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="xzone[]"]',
                        tipText: lang.t('Выберите географические зоны или пункт <b>- все -</b>, <br>чтобы определить регионы пользователей, для которых <br>будет отображен данный способ доставки'),
                        checkPattern: /^(0)$/gi
                    },
                    {
                        element: '.crud-form [name="user_type"]',
                        tipText: lang.t('Выберите категорию пользователей, <br>для которых будет доступна доставка.'),
                        watch: {
                            event: 'click'
                        }
                    },
                    {
                        element: '.crud-form [name="class"]',
                        tipText: lang.t('Расчетный класс отвечает за то, какой модуль <br>будет расчитывать стоимость и обрабатывать доставку. \
                                  Выберите <b>Фиксированная цена</b>. Подробнее о других расчетных классах можно узнать <a href="http://readyscript.ru/manual/shop_delivery.html#shop_delivery_add" target="_blank">в документации</a>'),
                        watch: {
                            event: 'click'
                        }
                    },
                    {
                        element: '.crud-form [name="data[cost]"]',
                        tipText: lang.t('Укажите стоимость доставки по городу'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('После ввода всех необходимых параметров доставки, нажмите \
                                 <br>на кнопку <i>Сохранить</i>'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-shop-paymentctrl'
                        },
                        
                    }
                ]
            },
            
            'to-shop-paymentctrl': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                tip: {
                    element: '.side-menu > li > a[data-url$="/shop-orderctrl/"]',
                    tipText: lang.t('Перейдите в раздел <i>Магазин &rarr; Способы оплаты</i>'),
                    bottom:true,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu a[href$="/shop-paymentctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu a[href$="/shop-paymentctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'shop-paymentctrl': {
                url: '%admin%/shop-paymentctrl/',
                topic: tourTopics.payment,
                type: 'info',
                message: lang.t('Перед началом продаж следует настроить способы оплат, которые будут предложены пользователю во время оформления заказа.\
                          <p>Если Вы желаете добавить возможность оплачивать заказы с помощью электроных денег или карт Visa, Mastercard, и т.д., то\
                            Вам необходимо предварительно создать аккаунт магазина на одном из сервисов-агрегаторов платежей - Yandex.Касса, Robokassa, Assist, PayPal, ...\
                          <p>На следующем шаге, добавим для примера, способ оплаты "Безналичный расчет". Это будет означать, что покупатель сможет получить счет сразу после оформления заказа.', null, 'tourShopPaymentCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn.btn-success',
                        tipText: lang.t('Добавить способ оплаты'),
                        position:['center', 'top'],
                        animation: 'slideInDown'
                    },
                    {
                        element: '.rs-table .sortdot',
                        tipText: lang.t('Сортировать способы оплаты можно с помощью перетаскивания'),
                        position: ['right', 'top'],
                        animation: 'slideInLeft'
                    }
                ]
            },
            
            'shop-paymentctrl-add': {
                url: '%admin%/shop-paymentctrl/',
                topic: tourTopics.payment,
                tip: {
                    element: '.c-head .btn.btn-success',
                    tipText: lang.t('Добавим, для примера, способ оплаты <b>Безналичный расчет</b>. Нажмите на кнопку <i>Добавить способ оплаты</i>')
                },
                watch: {
                    element: '.c-head .btn.btn-success',
                    event: 'click',
                    next: 'shop-paymentctrl-add-form'
                }  
            },
            
            'shop-paymentctrl-add-form': {
                url: '%admin%/shop-paymentctrl/?do=add',
                topic: tourTopics.payment,
                type:'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название способа оплаты - <b>Безналичный расчет</b>. Будет отображено во время оформления заказа в списке возможных способов оплаты.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="description"]',
                        tipText: lang.t('Укажите условия или подробности оплаты. Будут отображены под названием'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="first_status"]',
                        tipText: lang.t('Счет будет доступен пользователю только если заказ находится в статусе <i>Ожидает оплату</i>, поэтому выберите стартовый статус <b>Ожидает оплату</b>'),
                        checkSelectValue: /^(Ожидает оплату)$/gi
                    },
                    {
                        element: '.crud-form [name="class"]',
                        tipText: lang.t('Расчетный класс отвечает за то, какой модуль будет обрабатывать платежи <br>или предоставлять документы на оплату пользователю. Выберите <b>Безналичный расчет</b>'),
                        checkPattern: /^(bill)$/gi
                    },
                    {
                        waitNewContent: true,
                        element: '.crud-form [name="data[use_site_company]"]',
                        tipText: lang.t('Установите флажок, чтобы использовать реквизиты, которые были заполнены раннее в разделе <i>Веб-сайт &rarr; Настройка сайта</i>.'),
                        checkPattern: true
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('После ввода всех необходимых параметров оплаты, нажмите \
                                 <br>на кнопку <i>Сохранить</i>'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-index'
                        },
                    }
                    
                ]
            }
     }

    var tours = 
    {
        'welcome': $.extend({}, 
                            welcomeTour.commonStart, 
                            global.scriptType != 'Shop.Base' ? welcomeTour.shop : {},
                            welcomeTour.commonEnd)
    };
    
    $.tour(tours, {
        baseUrl: global.folder+'/',
        folder: global.folder,
        adminSection: global.adminSection
    });
    
});