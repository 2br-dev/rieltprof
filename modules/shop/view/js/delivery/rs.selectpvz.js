class SelectPvz {
    constructor(element) {
        this.selector = {
            yandexMap: '.rs-selectPvz_yandexMap',
            pvzListItem: '.rs-selectPvz_pvzListItem',
            pvzSearchInput: '.rs-selectPvz_pvzSearchInput',
        };
        this.class = {
            pvzMapButton: 'selectPvz_pvzMapButton',
        };
        this.options = {
            adminMode: false,
            dispatchEventTarget: undefined,
        };
        this.pvzPoints = {};

        this.owner = element;

        if (this.owner.dataset.SelectPvzOptions) {
            this.options = Object.assign(this.options, JSON.parse(this.owner.dataset.SelectPvzOptions));
        }

        this.owner.addEventListener('map.buttonClick', (event) => {
            this.pvzSelected(event.detail);
        });

        this.owner.querySelector(this.selector.pvzSearchInput).addEventListener('input', (event) => {
            this.pvzSearch(event.target.value);
        });

        this.owner.querySelectorAll(this.selector.pvzListItem).forEach((element) => {
            element.addEventListener('click', () => {
                this.map.pointOpenBalloon(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
            });
        });

        let mapElement = this.owner.querySelector(this.selector.yandexMap);
        RsJsCore.plugins.mapManager.map.init(mapElement, (map) => {
            this.map = map;
            map.setDispatchEventTarget(this.owner);

            let pvz_list = JSON.parse(this.owner.dataset.pvzJson);
            let coords_x = [];
            let coords_y = [];

            for (let deliveryId in pvz_list) {
                pvz_list[deliveryId].forEach((item) => {
                    if (this.pvzPoints[deliveryId] == undefined) {
                        this.pvzPoints[deliveryId] = {};
                    }

                    let balloonBody = lang.t('Адрес: ') + item.address;
                    if (item.phone) {
                        balloonBody = balloonBody + '<br>' + lang.t('Телефон: ') + item.phone;
                    }
                    if (item.worktime) {
                        balloonBody = balloonBody + '<br>' + lang.t('Время работы: ') + item.worktime;
                    }
                    balloonBody = balloonBody + '<br>' + lang.t('Возможность оплаты картой: ');
                    if (item.payment_by_cards) {
                        balloonBody = balloonBody + lang.t('есть');
                    } else {
                        balloonBody = balloonBody + lang.t('нет');
                    }
                    if (item.note) {
                        balloonBody = balloonBody + '<br>' + lang.t('Заметки: ') + item.note;
                    }

                    let button_data = {
                        delivery: deliveryId,
                        pvz: item,
                    };

                    this.pvzPoints[deliveryId][item.code] = map.createPoint(item.coord_y, item.coord_x);
                    this.map.pointSetBalloon(this.pvzPoints[deliveryId][item.code], item.title, balloonBody, map.htmlButton(button_data, lang.t('Выбрать этот ПВЗ'), this.class.pvzMapButton));
                    this.map.addPoint(this.pvzPoints[deliveryId][item.code]);

                    coords_x.push(parseFloat(item.coord_x));
                    coords_y.push(parseFloat(item.coord_y));
                });
            }

            map.setBounds(Math.max.apply(Math, coords_y), Math.min.apply(Math, coords_y), Math.min.apply(Math, coords_x), Math.max.apply(Math, coords_x));
        });
    }

    /**
     * Поиск ПВЗ
     *
     * @param {string} searchQuery - поисковый запрос
     */
    pvzSearch(searchQuery) {
        let search = searchQuery.toLowerCase();
        this.owner.querySelectorAll(this.selector.pvzListItem).forEach((element) => {
            if (search != '' && element.dataset.searchString.toLowerCase().indexOf(search) == -1) {
                if (!element.hidden) {
                    element.hidden = true;
                    this.map.removePoint(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
                }
            } else {
                if (element.hidden) {
                    element.hidden = false;
                    this.map.addPoint(this.pvzPoints[element.dataset.deliveryId][element.dataset.pvzCode]);
                }
            }
        });
    }

    /**
     * Обрабатывает событие выбора ПВЗ на карте
     *
     * @param {array} eventDetail - данные выбранного ПВЗ
     */
    pvzSelected(eventDetail) {
        // todo это приватный метод, но js такого пока не умеет
        let eventProperties = {
            detail: eventDetail,
            cancelable: true,
        };
        if (!this.options.dispatchEventTarget) {
            eventProperties['bubbles'] = true;
        }
        let newEvent = new CustomEvent('pvzSelected', eventProperties);

        if (this.options.dispatchEventTarget) {
            this.options.dispatchEventTarget.dispatchEvent(newEvent);
        } else {
            document.dispatchEvent(newEvent);
        }

        // todo кусочек jQuery в нативном классе
        if (this.options.adminMode) {
            $(this.owner.closest('.dialog-window')).dialog('close');
        } else {
            if ($.rsAbstractDialogModule) {
                $.rsAbstractDialogModule.close();
            }
            if ($.colorbox) {
                $.colorbox.close();
            }
        }
    }

    /**
     * Устанавливает цель событий выбора ПВЗ
     *
     * @param {element} element - цель событий
     */
    setDispatchEventTarget(element) {
        this.options.dispatchEventTarget = element;
    }

    /**
     * Устанавливает контекст запуска "в админ. панели"
     *
     * @param {bool} value
     */
    setAdminMode(value) {
        this.options.adminMode = value;
    }

    static init(selector)
    {
        document.querySelectorAll(selector).forEach((element) => {
            if (element.clientWidth) {
                if (!element.selectPvz) {
                    element.selectPvz = new SelectPvz(element);
                }
            }
        });
    }

    static initListeners() {
        let elementSelector = '.rs-selectPvz';

        document.addEventListener('DOMContentLoaded', () => {
            SelectPvz.init(elementSelector);
        });

        // todo кусочек jQuery в нативном классе
        if ($.contentReady) {
            $.contentReady(() => {
                SelectPvz.init(elementSelector);
            });
        } else {
            $(document).on('new-content', () => {
                SelectPvz.init(elementSelector);
            });
        }

        SelectPvz.init(elementSelector);
    }
}

SelectPvz.initListeners();
