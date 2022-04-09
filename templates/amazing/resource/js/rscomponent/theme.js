/**
 * Инициализирует работу базовых возможностей темы оформления
 */
new class Theme extends RsJsCore.classes.component
{
    /**
     * Делает кликабельными ссылки, у которых URL записан в data-href атрибуте.
     */
    bindDataHrefLinks() {
        this.utils.on('click', 'a[data-redirect]:not(.rs-no-redirect)', (event) => {
            event.rsTarget.classList.add('rs-in-loading');
            location.href = event.rsTarget.dataset.redirect;
        });
    }

    /**
     * Активирует корректную работу переключателя Юридическое/Физическое лицо в форме регистрации
     */
    bindRegisterFields() {
        this.utils.on('change', 'input[name="is_company"]', (event) => {
            let context = event.rsTarget.closest('form');
            let companyFields = context && context.querySelector('.company-fields');
            if (companyFields) {
                if (event.rsTarget.value == 1) {
                    companyFields.classList.add('show');
                } else {
                    companyFields.classList.remove('show');
                }
            }
        });
    }


    /**
     * Привязывает шапку к верхней части
     */
    initStickyHeader() {
        let stickyType = document.body.dataset.stickyHeader;

        if (stickyType && stickyType !== 'none') {
            let head = document.querySelector(".head__inner");
            if (head) {
                let prevScroll = window.pageYOffset;
                let sticky = head.offsetTop;
                let stickyHeight = sticky + head.clientHeight;
                let windowWidth = window.innerWidth;
                head.parentNode.style.height = stickyHeight + 'px';

                window.addEventListener('resize', () => {
                    if (windowWidth != window.innerWidth) {
                        windowWidth = window.innerWidth;

                        let isSticky = head.classList.contains('head_sticky');
                        head.classList.remove('head_sticky');

                        stickyHeight = sticky + head.clientHeight;
                        head.parentNode.style.height = stickyHeight + 'px';

                        if (isSticky) {
                            head.classList.add('head_sticky');
                        }
                    }
                });

                if (prevScroll > stickyHeight) {
                    head.classList.add("head_sticky");
                    head.classList.add("animation-slide-top");
                }

                window.addEventListener('scroll',  () => {

                    if (stickyType == 'sticky_up') {
                        let currentScroll = window.pageYOffset;
                        if (prevScroll > currentScroll && currentScroll > stickyHeight) {
                            head.classList.add("head_sticky");
                            head.classList.add("animation-slide-top");
                        } else {
                            head.classList.remove("head_sticky");
                            head.classList.remove("animation-slide-top");
                        }
                        prevScroll = currentScroll;
                    }

                    else if (stickyType == 'sticky') {
                        if (window.pageYOffset > sticky) {
                            head.classList.add("head_sticky");
                        } else {
                            head.classList.remove("head_sticky");
                        }
                    }
                });
            }
        }
    }

    /**
     * Инициализирует работу sideBar'ов.
     * Зависит от RS-плагина scroller
     */
    initSideBars() {
        let closeOffcanvasMenus = () => {
            let overlay = document.querySelector('.offcanvas-overlay');
            overlay && overlay.remove();
            document.body.classList.remove('offcanvas-body');

            document.querySelectorAll('.offcanvas_active').forEach((it) => {
                it.addEventListener('transitionend', () => {
                    if (it.sourceElement) {
                        let from = it.destinationElement ? it.destinationElement.childNodes : it.childNodes;
                        while (from.length > 0) {
                            it.sourceElement.append(from[0]);
                        }
                    }
                    if (!it.id) {
                        it.remove();
                    }
                }, {once:true});
                it.classList.remove('offcanvas_active');
            });
            this.plugins.scroller.returnToPrevScroll();
        };

        let openOffcanvasMenu = async (event) => {
            let target = event.rsTarget;
            let id = target.dataset.id;
            let extraClass = target.dataset.extraClass;
            let sidebar = document.getElementById(id);

            let createDiv = () => {
                let sidebar = document.createElement('div');
                if (id) sidebar.id = id;
                if (extraClass) sidebar.className += extraClass;
                sidebar.classList.add('offcanvas');
                return sidebar;
            };

            if (target.dataset.loadUrl && (!id || !sidebar)) {
                //Необходимо предзагрузить данные для выезжающего блока
                let content = await this.utils.fetchJSON(target.dataset.loadUrl);
                if (content && content.html) {
                    sidebar = createDiv();
                    sidebar.innerHTML = content.html;
                    document.body.append(sidebar);
                    sidebar.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                    this.initMultilevelMenu();
                } else {
                    console.error(lang.t('Ответ на запрос не содержит JSON {html: "..."}'));
                }
            }
            else if (target.dataset.source) {
                //Необходимо переместить данные в sidebar
                let sourceElement = document.querySelector(target.dataset.source);
                if (!sidebar) {
                    sidebar = createDiv();
                }
                sidebar.sourceElement = sourceElement;
                let destination;
                if (target.dataset.destination) {
                    destination = sidebar.querySelector(target.dataset.destination);
                    sidebar.destinationElement = destination;
                } else {
                    destination = sidebar
                }
                while (sourceElement.childNodes.length > 0) {
                    destination.append(sourceElement.childNodes[0]);
                }

                document.body.append(sidebar);
            }

            this.plugins.scroller.saveScroll();
            const overlay = document.createElement('div');
            overlay.classList.add('offcanvas-overlay');
            document.body.classList.add('offcanvas-body');
            document.body.prepend(overlay);

            sidebar.classList.add('offcanvas_active');
            overlay.addEventListener('click', closeOffcanvasMenus);
        };

        this.utils.on('click', '.offcanvas-open', (event) => {
            event.preventDefault();
            openOffcanvasMenu(event);
        });

        this.utils.on('click', '.offcanvas-close', (event) => {
            closeOffcanvasMenus(event);
        });
    }

    /**
     * Инициализирует мультиуровневые меню
     */
    initMultilevelMenu() {
        document.querySelectorAll('.offcanvas-multilevel').forEach((multilevelOffcanvas) => {
            if (!multilevelOffcanvas.dataset.initializedMultilevel) {
                multilevelOffcanvas.dataset.initializedMultilevel = true;
                multilevelOffcanvas.querySelectorAll('.offcanvas__subnav')
                    .forEach((it) => {
                        it.parentElement.classList.add('offcanvas__has-subnav');

                        const backTrack = document.createElement('li');
                        backTrack.innerHTML = `<a class='offcanvas__back-track'>Назад</a>`;

                        const mainCategory = it.previousElementSibling.cloneNode(true);
                        mainCategory.className = '';
                        mainCategory.classList.add('offcanvas__main-category');
                        const mainCategoryWrap = document.createElement('li');
                        mainCategoryWrap.append(mainCategory);

                        it.prepend(mainCategoryWrap);
                        it.prepend(backTrack);

                        backTrack.addEventListener('click', function () {
                            it.classList.remove('offcanvas__subnav_active');
                            const prevList = it.previousElementSibling.closest('.offcanvas__list');
                            prevList.classList.remove('overflow-hidden');
                        });

                        it.previousElementSibling.addEventListener('click', function (e) {
                            e.preventDefault();
                            it.classList.add('offcanvas__subnav_active');
                            const prevList = this.closest('.offcanvas__list');
                            prevList.classList.add('overflow-hidden');
                            prevList.scrollTop = 0;
                        });
                    });
            }
        });
    }

    /**
     * Инициализирует числовые инпуты с +/- кнопками рядом
     */
    initNumberInputs(event) {
        event.target.querySelectorAll('.rs-number-input').forEach(element => {
            if (!element.rsInitialized) {
                let input = element.querySelector('input[type="number"]');
                let buttonUp = element.querySelector('.rs-number-up');
                let buttonDown = element.querySelector('.rs-number-down');

                buttonUp.disabled = input.disabled;
                buttonDown.disabled = input.disabled;

                buttonUp.addEventListener('click', () => {
                    let oldValue = parseFloat(input.value);
                    let step = parseFloat(input.step);
                    let min = parseFloat(input.min);
                    let max = parseFloat(input.max);
                    let breakPoint = parseFloat(input.dataset.breakPoint);

                    let newValue = Math.round((oldValue + step) * 1000) / 1000;
                    if (newValue < min) {
                        newValue = min;
                    }
                    if (oldValue < breakPoint && newValue > breakPoint) {
                        newValue = breakPoint;
                    }
                    if (max !== null && newValue > max) {
                        newValue = max;
                        element.dispatchEvent(new CustomEvent('max-limit', {bubbles:true}));
                    } else {
                        element.dispatchEvent(new CustomEvent('increase-amount', {bubbles:true}));
                    }
                    input.value = newValue;
                    input.dispatchEvent(new Event('change', {bubbles: true}));
                });
                buttonDown.addEventListener('click', () => {
                    let oldValue = parseFloat(input.value);
                    let step = parseFloat(input.step);
                    let min = parseFloat(input.min);
                    let breakPoint = parseFloat(input.dataset.breakPoint);

                    let newValue = Math.round((oldValue - step) * 1000) / 1000;
                    if (newValue < min) {
                        newValue = 0;
                    }
                    if (oldValue > breakPoint && newValue < breakPoint) {
                        newValue = breakPoint;
                    }
                    if (newValue != 0) {
                        element.dispatchEvent(new CustomEvent('decrease-amount', {bubbles:true}));
                        input.value = newValue;
                        input.dispatchEvent(new Event('change', {bubbles: true}));
                    }
                });

                element.rsInitialized = true;
            }

        });
    }

    /**
     * Инициализирует стилизованные Select'ы на некоторых страницах
     */
    initCatalogSelect() {
        let selectChange = (element) => {
            element.nextElementSibling.textContent = element.options[element.selectedIndex].textContent;
        };

        document.querySelectorAll('.catalog-select select')
            .forEach((it) => {
                if (!it.rsActivated) {
                    selectChange(it);
                    it.addEventListener('change', event => selectChange(event.target));
                    it.rsActivated = true;
                }
            });
    }

    /**
     * Инициализирует работу кнопки сворачивания/разворачивания товарных лейблов
     *
     * @param event
     */
    initProductLabels(event) {
        const labels = event.target.querySelectorAll('.js-product-labels');
        labels.forEach((it) => {
            if (it.querySelectorAll('li').length > 1) {
                const button = it.querySelector('button');
                button.addEventListener('click', function () {
                    it.classList.toggle('full-labels')
                });
                button.classList.remove('d-none');
            };
        });
    }

    /**
     * Инициализирует полифил для отложнной загрузки изображений
     *
     * @param event
     */
    runLazyImagePolyfill(event) {
        event.target.querySelectorAll('noscript.loading-lazy').forEach(element => {
            loadingAttributePolyfill.prepareElement(element);
        });
    }

    /**
     * Сдвигает футер к нижней части окна браузера, расширяя высоту блока
     *
     * @param event
     */
    initFullHeightBlock(event) {

        let setMinHeight = (element) => {
            let height = 0;
            for (let sibling of element.parentNode.children) {
                if (sibling !== element) {
                    height += parseInt(sibling.offsetHeight);
                }
            }

            element.style.minHeight = window.innerHeight - height + 'px';
        };

        let elements = event.target.getElementsByClassName('100vh');

        for(let i=0; i < elements.length; i++) {
            setMinHeight(elements[i]);
        }
    }

    /**
     * Инициализирует popOver'ы во всей теме оформления
     *
     * @param event
     */
    initPopOvers(event) {
        let popoverTriggerList = [].slice.call(event.target.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map((popoverTriggerEl) => {
            return new bootstrap.Popover(popoverTriggerEl)
        });
    }

    /**
     * Активирует кнопки "Вернуться назад" на разных страницах
     */
    initBackButton() {
        this.utils.on('click', '.rs-back-button', (event) => {
            if (window.history.length) {
                window.history.back();
            } else {
                location.href = event.rsTarget.dataset.rootUrl;
            }
        });
    }

    /**
     * Выполняется, когда весь DOM загружен
     */
    onDocumentReady() {
        this.bindDataHrefLinks();
        this.bindRegisterFields();
        this.initStickyHeader();
        this.initSideBars();
        this.initMultilevelMenu();
        this.initBackButton();
    }

    /**
     * Выполняется, когда на странице пояился новый контент
     *
     * @param event
     */
    onContentReady(event) {
        this.initNumberInputs(event);
        this.initProductLabels(event);
        this.initCatalogSelect();
        this.runLazyImagePolyfill(event);
        this.initFullHeightBlock(event);
        this.initPopOvers(event);
    }
};