/**
 * Инициализирует работу страницы сравнения товаров
 */
new class CompareShow extends RsJsCore.classes.component {

    constructor(settings)
    {
        super();
        let defaults = {
            showDifferentCheckbox: '#compare-different',
            comparePage: '.rs-compare-page',
            removeButton: '.rs-remove',
            backButton: '.rs-back-button',
            compareButton:'.rs-compare',

            hideIdenticalClass: 'hide-identical',

            swiperCompareTable: '.swiper-compare-table',
            swiperCompareProducts: '.swiper-compare-products',
            swiperButtonNext: '.swiper-button-next',
            swiperButtonPrev: '.swiper-button-prev',

            compareProducts: '.compare-products',
            compareProductsStickyclass: 'compare-products_sticky'
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
        this.sticky = 0;
    }

    /**
     * Инициализирует подписку на события
     */
    init() {
        this.utils.on('click', this.settings.backButton, event => this.close());
        this.utils.on('click', this.settings.compareButton, event => this.remove(event))
        this.utils.on('change', this.settings.showDifferentCheckbox, event => this.showDifferent(event));
        this.initHeight();
        this.initSliders();

        window.addEventListener('scroll', event => this.switchSticky(event));
    }

    /**
     * Переклчает шапку в позицию sticky, в зависимости от скрола
     */
    switchSticky () {
        let compareProducts = document.querySelector(this.settings.compareProducts);
        if (compareProducts) {
            if (window.pageYOffset > this.sticky) {
                compareProducts.classList.add(this.settings.compareProductsStickyclass);
            } else {
                compareProducts.classList.remove(this.settings.compareProductsStickyclass);
            }
        }
    }

    /**
     * Обрабатывает переключатель "Показывать только различные"
     * @param event
     */
    showDifferent(event) {
        this.plugins.cookie.setCookie('compareShowOnlyDifferent', +event.rsTarget.checked);

        if (event.rsTarget.checked) {
            document.querySelector(this.settings.swiperCompareTable).classList.add(this.settings.hideIdenticalClass);
        } else {
            document.querySelector(this.settings.swiperCompareTable).classList.remove(this.settings.hideIdenticalClass);
            window.dispatchEvent(new Event('resize')); //пересчитываем размер swiper
        }
    }

    /**
     * Просчитывает высоту блоков
     */
    initHeight() {
        let params = document.querySelectorAll('.compare-columns-title');
        let slides = document.querySelectorAll('.compare-product-param');

        if (params.length) {
            function comparelinesheight() {
                var params_num = params.length;
                var slides_num = slides.length / params_num;

                slides.forEach(function(it) {
                    it.style.height = 'auto';
                });

                params.forEach(function(it) {
                    it.style.height = 'auto';
                });

                params.forEach(function(it, index) {
                    var max_height = it.clientHeight;
                    var i = 0;
                    while (i < slides_num) {
                        var param_index = index + i * params_num;
                        var height = slides[param_index].clientHeight;
                        if (parseInt(max_height) < parseInt(height)) {
                            max_height = height;
                        }
                        i++;
                    }
                    i = 0;
                    while (i < slides_num) {
                        var param_index = index + i * params_num;
                        slides[param_index].style.height = max_height + 'px';
                        slides[param_index].style.marginTop = it.clientHeight + 'px';
                        i++;
                    }
                    it.style.height = 'auto';
                    it.style.marginBottom = max_height + 'px';

                });
            }
            comparelinesheight();
            window.addEventListener('resize', comparelinesheight)
        }

        let compareProducts = document.querySelector(this.settings.compareProducts);
        if (compareProducts) {
            this.sticky = compareProducts.offsetTop + compareProducts.clientHeight;
        }
    }

    /**
     * Инициализирует слайдеры
     */
    initSliders() {
        this.compareTable = new Swiper(this.settings.swiperCompareTable, {
            spaceBetween: 24,
            slidesPerView: 2,
            allowTouchMove: false,
            breakpoints: {
                1400: {
                    slidesPerView: 5,
                },
                1200: {
                    slidesPerView: 4,
                },
                768: {
                    slidesPerView: 3,
                },
            }
        });

        this.compareProducts = new Swiper(this.settings.swiperCompareProducts, {
            slidesPerView: 2,
            spaceBetween: 0,
            controller: {
                control: this.compareTable,
            },
            navigation: {
                nextEl: this.settings.swiperButtonNext,
                prevEl: this.settings.swiperButtonPrev,
            },
            breakpoints: {
                1400: {
                    slidesPerView: 5,
                    spaceBetween: 24
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 24
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 24
                },
            },
        });
    }

    /**
     * Удаляет товар из сравнения
     *
     * @param event
     */
    remove(event) {
        let context = event.rsTarget.closest('[data-id]');
        if (context) {
            let productId = context.dataset.id;

            context.style.opacity = 0.5;
            let compareUrls = JSON.parse(document.querySelector('[data-compare-url]').dataset.compareUrl);

            let data = new FormData();
            data.append('id', productId);

            this.utils.fetchJSON(compareUrls.remove, {
                method: 'POST',
                body: data
            }).then((response) => {
                if (response.success) {
                    this.updateBody();

                    try {
                        window.opener.RsJsCore.components.compare.checkActive(response.total, productId, false);
                    } catch(e) {}
                }
            });
        } else {
            console.error(lang.t('Не найден элемент [data-id], содержащий id товара, вокруг нажатой кнопки'));
        }

    }

    /**
     * Обновляет содержимое страницы через AJAX
     */
    updateBody() {
        this.utils.fetchJSON(window.location.href)
            .then((response) => {
                let element = document.querySelector(this.settings.comparePage);
                if (element) {
                    let parent = element.parentNode;
                    this.compareTable.destroy();
                    this.compareProducts.destroy();

                    element.insertAdjacentHTML('afterend', response.html);
                    element.remove();
                    parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                    this.initHeight();
                    this.initSliders();
                }
            });
    }

    /**
     * Закрывает окно
     */
    close() {
        window.close();
    }

    onDocumentReady() {
        this.init();
    }
};