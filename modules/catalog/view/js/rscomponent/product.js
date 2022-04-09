/**
 * Скрипт обеспечивает корректную работу карточки товара
 */
new class Product extends RsJsCore.classes.component {

    constructor(settings) {
        super();
        let defaults = {
            galleryThumbs: '.product-gallery-thumbs',
            galleryThumbsPrev: '.product-gallery-thumbs-wrap .swiper-button-prev',
            galleryThumbsNext: '.product-gallery-thumbs-wrap .swiper-button-next',
            galleryImages: '.product-gallery-top',

            swiperAccessories: '.swiper-accessories',
            swiperAccessoriesNext: '.product-accessories .swiper-button-next',
            swiperAccessoriesPrev: '.product-accessories .swiper-button-prev',
            product: '.rs-product',

            concomitantBlock: '.rs-product-concomitant',
            concomitantInput: '[name^="concomitant"]',
            concomitantPrice: '.rs-concomitant-price',
            concomitantPriceValue: '.rs-concomitant-price .rs-value',

            hiddenClass: 'd-none'
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
        this.galleryThumbs = null;
        this.galleryThumbsSlides = null;
        this.galleryTop = null;
        this.galleryTopSlides = null;
    }

    initGallerySliders() {
        let swiperThumbs = document.querySelector(this.settings.galleryThumbs);
        let product = document.querySelector(this.settings.product);
        let zoom = product && product.classList.contains('rs-zoom');

        let direction;
        if (swiperThumbs && swiperThumbs.dataset.swiperDirection) {
            direction = {
                breakpoints: {
                    992: {
                        direction: 'vertical',
                        spaceBetween: 16,
                    }
                }
            };
        } else {
            direction = {};
        }

        let p1 = new Promise((resolve) => {
            this.galleryThumbs = new Swiper(this.settings.galleryThumbs, {
                spaceBetween: 8,
                slidesPerView: 'auto',
                freeMode: true,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
                navigation: {
                    nextEl: this.settings.galleryThumbsNext,
                    prevEl: this.settings.galleryThumbsPrev,
                },
                on: {
                    imagesReady: (e) => {
                        this.galleryThumbsSlides = Object.assign({}, e.slides);
                        resolve();
                    }
                },
                ...direction
            });
        });

        let p2 = new Promise((resolve) => {
            this.galleryTop = new Swiper(this.settings.galleryImages, {
                spaceBetween: 16,
                zoom: !!zoom,
                thumbs: {
                    swiper: this.galleryThumbs
                },
                breakpoints: {
                    992: {
                        allowTouchMove: false,
                    }
                },
                on: {
                    imagesReady: (e) => {
                        this.galleryTopSlides = Object.assign({}, e.slides);
                        resolve();
                    }
                }
            });
        });


        this.whenImagesReady = Promise.all([p1, p2]);
    }

    initConcomitantSliders() {
        var galleryThumbs = new Swiper(this.settings.swiperAccessories, {
            slidesPerView: 'auto',
            navigation: {
                nextEl: this.settings.swiperAccessoriesNext,
                prevEl: this.settings.swiperAccessoriesPrev,
            },
            autoHeight: true,
            breakpoints: {
                1200: {
                    autoHeight: false,
                    direction: 'vertical',
                }
            }
        });
    }

    initCurrentTab() {
        let tab = location.hash.match(/#tab-(.+)$/);
        if (tab) {
            let someTab = document.querySelector('[data-bs-target="#tab-' + tab[1] + '"]');
            if (someTab) {
                let onePane = new bootstrap.Tab(someTab);
                onePane.show();
            }
        }
    }

    initEvents() {
        this.utils.on( 'click', '.rs-go-to-tab', (event) => {
            let tab = event.rsTarget.getAttribute('href').match(/#tab-(.+)$/);
            if (tab) {
                let someTab = document.querySelector('[data-tab-id="' + tab[1] +'"]');
                someTab.click();
                someTab.scrollIntoView();
            }
            event.preventDefault();
        });

        //Изменяем состав отображаемых изображений товаров
        this.utils.on('offer-photo-changed', this.settings.product, (event) => {
            this.whenImagesReady.then(() => {
                this.galleryThumbs.removeAllSlides();
                this.galleryTop.removeAllSlides();

                for (let [index, element] of Object.entries(this.galleryThumbsSlides)) {
                    if (!event.detail.images.length || event.detail.images.indexOf(element.dataset.imageId) > -1) {
                        this.galleryThumbs.appendSlide(element);
                    }
                }

                for (let [index, element] of Object.entries(this.galleryTopSlides)) {
                    if (!event.detail.images.length || event.detail.images.indexOf(element.dataset.imageId) > -1) {
                        this.galleryTop.appendSlide(element);
                    }
                }

                this.galleryThumbs.update();
                this.galleryTop.update();
            });
        });
    }

    initVideoModal() {
        this.utils.on('shown.bs.modal', '#modal-video', (event) => {
            let element = event.rsTarget.querySelector('iframe.youtube-player');
            element && element.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
        });

        this.utils.on('hidden.bs.modal', '#modal-video', (event) => {
            event.rsTarget.querySelectorAll('iframe.youtube-player').forEach(element => {
                element.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');
            });
        });
    }

    /**
     * Устанавливает комплектацию по умолчанию
     */
    setDefaultOffer() {
        let product = document.querySelector(this.settings.product);
        if (product && product.changeOffer) {
            let offerIdMatch = location.hash.match(/#(\d+)/);
            if (offerIdMatch) {
                product.changeOffer.setOffer(offerIdMatch[1]);
            }
        }
    }

    /**
     * Инициализирует подсказку итоговой стоимости сопутствующих товаров
     */
    initConcomitantCalculate() {
        let recalculate = () => {
            let concomitantBlock = document.querySelector(this.settings.concomitantBlock);
            if (concomitantBlock) {
                let sum = 0;
                document.querySelectorAll(this.settings.concomitantInput + ':checked').forEach((element) => {
                    sum += parseFloat(element.dataset.price);
                });

                let Format = wNumb({
                    thousand: ' '
                });

                let currency = concomitantBlock && concomitantBlock.dataset.currency;
                let element = document.querySelector(this.settings.concomitantPrice);
                let value = element.querySelector(this.settings.concomitantPriceValue);
                value.innerHTML = '+' + Format.to(sum) + ' ' + currency;
                if (sum > 0) {
                    element.classList.remove(this.settings.hiddenClass);
                } else {
                    element.classList.add(this.settings.hiddenClass);
                }
            }
        };

        this.utils.on('change', '[name^="concomitant"]', (element) => {
            recalculate();
        });

        recalculate();
    }

    onDocumentReady() {
        this.initEvents();
        this.initVideoModal();
        this.initGallerySliders();
        this.initConcomitantSliders();
        this.initCurrentTab();
        this.initConcomitantCalculate();
    }

    onAfterDocumentReady() {
        this.setDefaultOffer();
    }
};