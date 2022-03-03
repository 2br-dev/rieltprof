/**
 * Инициализирует функцию добавления товаров в корзину, отображение общего числа товаров в корзине
 * Инициализируется, если на экране присутствует блок "Корзина". (элемент #cart)
 */
$.cart = function( method ) {
    var defaults = {
            cartActiveClass: 'active',
            fixedLine: '.rs-cart-line',
            fixedLineActiveClass: 'open',
            amountTextField: '.rs-num',
            closeDlg: '.rs-close-dlg',
            popupCartId:'rs-popup-cart',
            checkoutButton: '#checkout',
            checkoutButtonActiveClass: 'active',
            cartTotalPrice: '.cost .value',
            cartTotalItems: '.rs-cart-items-count',
            cartPriceBlock: false,
            productWrapper: '.rs-product-item',
            update: '.rs-recalc',
            inLoadingClass: 'inloading',
            saveScroll: '.rs-save-scroll',
            popupCartContainer: '.rs-popup-cart',

            addToCart: '.rs-to-cart',
            noShowCart: false,
            noShowCartClass: 'rs-no-show-cart',
            applyCoupon: '.rs-apply-coupon',
            cartBlock: '#rs-cart-items',
            cartForm: '#rs-cart-form',
            cartItem: '.rs-cart-item',
            amountWraper: '.rs-amount', //Оборачивающий контейнер
            amountField: '.rs-field-amount',

            showCart: '.rs-show-cart',
            checkout: '.rs-submit',
            clearCart: '.rs-clear-cart',
            offerField: '.rs-offer',
            cartItemInc: '.rs-inc',
            cartItemDec: '.rs-dec',
            cartItemRemove: '.rs-remove',
            removeCoupon: '.rs-lines-container .rs-remove',
            context: '[data-id]',
            offerFormName: 'offer',
            multiOffersName: 'multioffers[items]',    //атрибут name для многомерных компл.
            multiofferFormName: '[multioffers][',     //атрибут name многомерной комплектации
            multiofferHidden: '[name^="hidden_offers"]',      //атрибут name у спрятанных полей со сведения о компл.
            amountFormName: 'amount',
            alreadyInCartClass: 'added',
            alreadyInCartClassTimeout:5,
            alreadyInCartBlock: false,
            alreadyInCartBlockTimeout:5,
            concomitantCheckboxName: 'concomitant[',
            concomitantField: '.rs-field-concomitant',
            concomitantAmountClass: 'rs-concomitant', //Класс на вводе количества сопутствующих товаров
            noMobileDetected: false,

            toggleOneClickCart: '.rs-toggle-one-click-cart', //Класс кнопки для оформления заказа по телефону
            continueButton: '.rs-continue'
        },
        $this = $('#rs-cart'),
        hasCart = true;

    if (!$this.length) {
        $this = $('body');
        hasCart = false;
    }

    var data = $this.data('cart');
    if (!data) { //Инициализация
        data = { options: defaults };
        $this.data('cart', data);
    }

    //public
    var methods = {
        init: function(initoptions) {
            data.options = $.extend(data.options, initoptions);

            $('body').on('click.cart', data.options.addToCart, addToCart);
            $(data.options.showCart).click(function() {
                var href = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
                return methods.add(href);
            });
            $(data.options.checkoutButton).click(function() {
                if (!$(this).hasClass(data.options.checkoutButtonActiveClass)) return false;
            });
            initCart(); //Вызываем на случай, если находимся на странице с корзиной
        },

        /**
         * Добавляет товар в корзину
         *
         * @param url - ссылка на добавление товара в корзину
         * @param offer - номер комплектации 0,1,2...
         * @param multioffers - массив многомерных комплектаций
         * @param concomitants - массив сопутствующих товаров
         * @param amount - количество товаров, которое необходимо добавить
         * @param noShowCartDialog - не показывать корзину пользователя
         */
        add: function(url, offer, multioffers, concomitants, amount, noShowCartDialog) {

            //if ($.detectMedia && ($.detectMedia('mobile') || $.detectMedia('portrait')) ) return true; //Не открываем окна в мобильной версии

            var params = {};
            if (offer) params.offer = offer;
            if (amount) params.amount = amount;
            if (multioffers) params = $.extend(params, multioffers); // Многомерных комплектаций
            if (concomitants) params = $.extend(params, concomitants);  // Сопутствующие товары

            let target = $(this);
            target.trigger('product.addBefore', {
                url:url,
                offer:offer,
                multioffers:multioffers,
                amount:amount,
                noShowCartDialog:noShowCartDialog
            });

            if (!noShowCartDialog) {
                methods.showCart(url, params);
            } else {
                $.post(url, params, function(response) {
                    try {
                        //Обновляем сведения в основном окне, если действие происходит во всплывающем
                        window.opener.jQuery.cart('updateCartBlock', response);
                    } catch (e) {
                        $.cart('updateCartBlock', response);
                    } finally {
                        target.trigger('product.add', {
                            url:url,
                            offer:offer,
                            multioffers:multioffers,
                            amount:amount,
                            noShowCartDialog:noShowCartDialog
                        });
                    }
                }, 'json');
            }

            return false;
        },

        updateCartBlock: function(serverData) {
            if (serverData) {
                $(data.options.cartTotalItems, $this).text(serverData.cart.items_count);
                $(data.options.cartTotalPrice, $this).text(serverData.cart.total_price);
                $(data.options.cartPriceBlock).toggle(parseInt(serverData.cart.total_price)>0);
                $(data.options.checkoutButton).toggleClass(data.options.checkoutButtonActiveClass, serverData.cart.can_checkout && parseInt(serverData.cart.items_count)>0 );
                $this.toggleClass(data.options.cartActiveClass, parseFloat(serverData.cart.items_count)>0);
            }
        },

        /**
         * Обновляет корзину
         */
        refresh: function(url, callback) {
            var params = $(data.options.cartForm).serializeArray();
            if (!url) url = $(data.options.cartForm).attr('action');
            showLoading();//Показ загрузки
            $("input, select", data.cartBlock).prop('disabled', true); //Закроем на время ввод, пока грузится информация
            $.ajax({
                url: url,
                data: params,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.redirect) {
                        location.href = response.redirect;
                    }

                    var scrollElement = $(data.options.saveScroll, data.cartBlock),
                        saveScroll = scrollElement.scrollTop();

                    data.cartBlock.replaceWith(response.html);
                    initCart(response);
                    $(data.options.saveScroll, data.cartBlock).scrollTop(saveScroll);
                    if (callback) callback();
                    $('body').trigger('new-content');
                }
            });
        },

        showCart: function(url, params) {
            if ($.detectMedia('mobile')) {
                location.href=url;
            } else {
                params.floatCart = 1;
                $.ajax({
                    url: url,
                    data: params,
                    dataType: 'json',
                    success: function (response) {
                        $('#' + data.options.popupCartId).remove();
                        $('<div id="' + data.options.popupCartId + '" />').append(response.html).appendTo(data.options.popupCartContainer);

                        $(data.options.fixedLine).addClass(data.options.fixedLineActiveClass);
                        initCart(response);
                    }
                });
            }
        }
    };

    //private
    var addToCart = function() {
            var _this = this;
            $(this).addClass(data.options.alreadyInCartClass);
            if (!$(this).data('storedText') && $(this).data('addText')) {
                $(this).data('storedText', $(this).html());
                $(this).html($(this).data('addText'));
            }

            var reset = function() {
                $(_this).removeClass(data.options.alreadyInCartClass);
                if ($(_this).data('storedText')) {
                    $(_this).html($(_this).data('storedText'));
                    $(_this).data('storedText', null);
                }
            };

            if (data.options.alreadyInCartClassTimeout) {
                setTimeout(reset, data.options.alreadyInCartClassTimeout * 1000);
            }
            //Отображает всплывающий блок возле корзины
            if (data.options.alreadyInCartBlock) {
                var $block = $(data.options.alreadyInCartBlock);
                clearTimeout($block.data('timer'));
                var closeBlock = function(e) {
                    if (e && $(e.target).is(data.options.addToCart)) return;
                    $block.fadeOut();
                };

                $block.fadeIn().click(function(e) {
                    e.stopPropagation();
                });
                $('body').one('click', closeBlock);

                $block.data('timer', setTimeout(closeBlock, data.options.alreadyInCartClassTimeout * 1000));
            }

            var context = $(this).closest(data.options.context);
            if ($('[name="'+data.options.offerFormName+'"]:checked', context).length) {
                var offer = $('[name="'+data.options.offerFormName+'"]:checked', context).val();
            } else {
                var offer = $('[name="'+data.options.offerFormName+'"]', context).val();
            }
            var amount = $('[name="'+data.options.amountFormName+'"]', context).val();

            //Многомерные комплектациями
            if ($('[name^="multioffers["]',context).length){
                var multioffers = {};
                $('[name^="multioffers["]',context).each(function(i){
                    if (!$(this).is(':radio') || $(this).is(':checked')) {
                        pid              = $(this).attr('name');
                        multioffers[pid] = $(this).val();
                    }
                });
            }

            //Сопутствующие товары
            if ($('[name^="'+data.options.concomitantCheckboxName+'"]:checked', context).length){
                var concomitants = {};
                $('[name^="'+data.options.concomitantCheckboxName+'"]:checked',context).each(function(i){
                    var cname = 'concomitant[' + i + ']';
                    concomitants[cname] = $(this).val();
                });
            }

            var noShowCart = $(this).hasClass(data.options.noShowCartClass) || data.options.noShowCart;
            var url = $(this).data('url') ? $(this).data('url') : $(this).attr('href');

            var amountValue = amount ? amount : 1;
            var offerValue = (offer) ? offer : 0;
            var multioffersValues = (multioffers) ? multioffers : false;
            var concomitantValues = (concomitants) ? concomitants : false;

            //Не открываем окна в мобильной версии

            if ($.detectMedia && ($.detectMedia('mobile') || $.detectMedia('portrait'))) {
                var params = {
                    amount:amountValue,
                    offer:offerValue,
                };

                //Добавим многомерные комплектации к запросу
                if (multioffersValues) {
                    $.extend(params, multioffersValues);
                }
                //Добавим сопутствующие товары к запросу
                if (concomitantValues) {
                    $.extend(params, concomitantValues);
                }

                location.href = url + (url.indexOf('?') == -1 ? '?' : '&') + $.param(params);
                return false;
            }

            closeDlg();
            return methods['add'].call(this, url, offerValue, multioffersValues, concomitantValues, amount, noShowCart);
        },

        /**
         * Инициализация страницы корзины и навешивание событий на действия в ней
         *
         * @param serverData - данные от сервера
         */
        initCart = function(serverData) {
            data.cartBlock = $(data.options.cartBlock);

            if (data.cartBlock.length) {
                data.cartBlock
                    .on('click', data.options.cartItemInc, incProduct)
                    .on('click', data.options.cartItemDec, decProduct)
                    .on('blur', data.options.amountField, function() {
                        var val = (+$(this).val());
                        if (val < $(this).attr('step')) $(this).val($(this).attr('step')).keyup();
                    })
                    .on('focus', data.options.amountField, function(e, noSelect) {
                        if (!noSelect) {
                            this.select();
                        }
                    })
                    .on('keyup', data.options.amountField, updateAmount)
                    .on('change', data.options.concomitantField, updateAmount)
                    .on('change', data.options.offerField, function() {
                        methods.refresh();
                    })
                    .on('change', '[name*="'+data.options.multiofferFormName+'"]', changeMultiOffers)
                    .on('click', data.options.continueButton, function() {
                        history.back();
                        return false;
                    })
                    .on('click', data.options.toggleOneClickCart, function() {
                        $.oneClickCart('triggerBlockTrigger'); //Откроем блок
                        return false;
                    })
                    .on('click', data.options.cartItemRemove, removeProduct)
                    .on('click', data.options.clearCart, clearCart)
                    .on('click', data.options.applyCoupon, applyCoupon)
                    .on('click', data.options.checkout, checkout)
                    .on('click', data.options.closeDlg, closeDlg);
                    //.bind('click', function(e) { e.stopPropagation(); });

                data.cartBlock.trigger('new-content');

                $(data.options.cartForm, data.cartBlock).submit(function() {
                    clearTimeout(data.cartBlock.data('changeTimer'));
                    methods.refresh();
                    return false;
                });

                $(data.options.update, data.cartBlock).hide();
            }

            methods.updateCartBlock(serverData);
        },
        /**
         * Смена(Выбор) многомерных комплектаций в корзине
         *
         */
        changeMultiOffers = function() {
            var context    = $(this).closest(data.options.context);    // Оборачивающий контейнер
            var offersList = $(data.options.multiofferHidden,context); // Список скрытых полей со значениями

            if (offersList.length>0){ //Если комплектции используются
                var offer   = $('[name*="['+data.options.offerFormName+']"]',context); // Скрытое поле - текущая комплектация
                var offers_info = [];
                //Соберём информацию о том, что выбрано для товара
                var selected = $('[name*="'+data.options.multiofferFormName+'"]',context);

                //Соберём информацию о комплектациях для одного товара
                offersList.each(function(i){
                    offers_info[i]          = {};
                    offers_info[i]['id']    = this;
                    offers_info[i]['info']  = $(this).data('info');
                    offers_info[i]['num']   = $(this).data('num');
                    offers_info[i]['value'] = $(this).val();
                });
                //Соберём информацию, что изменилось
                $(selected).each(function(i){
                    selected[i]          = {};
                    selected[i]['title'] = $(this).data('prop-title');
                    selected[i]['value'] = $(this).val();
                });

                var sel_offer = false;
                //Отметим, что мы выбрали
                for(var j=0;j<offers_info.length;j++){
                    var info = offers_info[j]['info']; //Группа с информацией

                    var found = 0;                //Флаг, что найдены все совпадения
                    for(var m=0;m<info.length;m++){
                        for(var i=0;i<selected.length;i++){
                            if ((selected[i]['title']==info[m][0])&&(selected[i]['value']==info[m][1])){
                                found++;
                            }
                        }
                        if (found==selected.length){ //Если удалось найди совпадение, то выходим
                            sel_offer = offers_info[j];
                            break;
                        }
                    }
                }

                $(context).removeClass('notAvaliable');

                if (sel_offer){ //Если комплектация найдена
                    offer.val(sel_offer.value);
                }else{ //Если не найдена комплектация, выберем нулевую
                    offer.val(0);
                }
            }

            methods.refresh();
        },
        /**
         * Увеличение количества одного товара в корзине
         *
         */
        incProduct = function() {
            if (!data.cartBlock.hasClass(data.options.inLoadingClass)){
                let amountField = $(this).closest(data.options.amountWraper).find(data.options.amountField);
                let amountTextField = $(this).closest(data.options.amountWraper).find(data.options.amountTextField);
                let breakPoint = amountField.data('breakPoint');

                let delta = 1;
                if ($(this).data('amount-step')) {
                    delta = $(this).data('amount-step') - 0;
                }
                let oldValue = (+amountField.val());
                let newValue = Math.round((oldValue + delta) * 1000) / 1000;
                if (oldValue < breakPoint && newValue > breakPoint) {
                    newValue = breakPoint;
                }

                amountField.val(newValue).keyup();
                amountTextField.text(newValue);
            }

            return false;
        },

        /**
         * Уменьшение количества одного товара в корзине
         *
         */
        decProduct = function() {
            if (!data.cartBlock.hasClass(data.options.inLoadingClass)){
                let amountField = $(this).closest(data.options.amountWraper).find(data.options.amountField);
                let amountTextField = $(this).closest(data.options.amountWraper).find(data.options.amountTextField);
                let breakPoint = amountField.data('breakPoint');

                let delta = 1;
                if ($(this).data('amount-step')) {
                    delta = $(this).data('amount-step') - 0;
                }
                let oldValue = (+amountField.val());
                let newValue = Math.round((oldValue - delta) * 1000) / 1000;
                if (oldValue > breakPoint && newValue < breakPoint) {
                    newValue = breakPoint;
                }

                if (newValue > 0) {
                    amountField.val(newValue).keyup();
                    amountTextField.text(newValue);
                }
            }
            return false;
        },

        /**
         * Закрытие диалогового окна
         */
        closeDlg = function() {
            $('#'+data.options.popupCartId).remove();
            $(data.options.fixedLine).removeClass(data.options.fixedLineActiveClass);
        },

        /**
         * Удаление товара из корзины
         *
         */
        removeProduct = function() {
            if (!data.cartBlock.hasClass(data.options.inLoadingClass)){
                var cartItem = $(this).closest(data.options.cartItem);
                cartItem.css('opacity', 0.5).addClass('removing');
                var other = cartItem.siblings('[data-product-id="'+cartItem.data('productId')+'"]');
                if (!other.length) {
                    //Удаляем пометку, что товар добавлен в корзину
                    var context = $(data.options.productWrapper+'[data-id="'+cartItem.data('productId')+'"]')
                    $(data.options.addToCart, context).removeClass(data.options.alreadyInCartClass);
                }

                methods.refresh( $(this).attr('href') );
            }
            return false;
        },

        /**
         * Показ загрузки
         *
         */
        showLoading = function() {
            data.cartBlock.addClass(data.options.inLoadingClass);
        },

        /**
         * Скрытие загрузки
         *
         */
        hideLoading = function() {
            data.cartBlock.removeClass(data.options.inLoadingClass);
        },

        /**
         * Обновление количества в корзине
         * @param e
         */
        updateAmount = function(e) {
            if (e.keyCode != 13) {
                var _this = $(this);

                var n = _this.closest(data.options.cartItem).index();
                clearTimeout(data.cartBlock.data('changeTimer'));
                data.cartBlock.data('changeTimer', setTimeout(function() {
                    if (_this.val() != '') {
                        methods.refresh(null, function() {
                            //Восстанавливаем фокус
                            $(data.options.cartItem, data.cartBlock).eq(n).find(data.options.amountField).each(function() {
                                $(this).trigger('focus', true);
                                var value = $(this).val();
                                $(this).val('').val(value);
                            })
                        });
                    }
                }, 500));
            }
        },
        /**
         * Очистить корзину
         *
         */
        clearCart = function() {
            var context = $(data.options.productWrapper+'[data-id]');
            $(data.options.addToCart, context).removeClass(data.options.alreadyInCartClass);

            return refreshByHref.call(this);
        },
        /**
         * Обновление по адресу
         * @return {boolean}
         */
        refreshByHref = function() {
            methods.refresh( $(this).attr('href') );
            return false;
        },
        /**
         * Применить купон
         *
         */
        applyCoupon = function() {
            methods.refresh();
        },

        checkout = function(e) {
            var url = $(data.options.cartForm).attr('action');
            var param = (url.indexOf('?') > -1 ? '&' : '?') + 'checkout=1';
            methods.refresh(url + param);
            return false
        };

    if ( methods[method] ) {
        methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
        return methods.init.apply( this, arguments );
    }
};