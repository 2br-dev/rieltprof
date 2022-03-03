/**
 * Инициализация слайдера в товаре
 *
 * @param {Element} wrapper - обёртка товара
 */
function initDProductSlider(wrapper)
{
    let swiper_container = wrapper.querySelector(".swiper-container");
    if (!swiper_container){ //Если вдруг скрыто в настройках атома
        return;
    }
    let atom_wrapper     = wrapper.querySelector(".d-atom-item");
    let photo_settings   = atom_wrapper.dataset;

    let options =  {
        lazy: true,
        effect: photo_settings['photoEffect'],
        grabCursor: true
    };
    if (swiper_container.querySelectorAll('img').length > 1){
        let arrows = swiper_container.querySelectorAll('.d-swiper-arrow');
        options['navigation'] = {
            nextEl: "#" + arrows[0].id,
            prevEl: "#" + arrows[1].id,
        }
    }
    if (photo_settings['photoShowLoop']){
        options['loop'] = true;
    }

    if (photo_settings['photoShowPagination']){
        options['pagination'] = {
            el: "#" + swiper_container.querySelector('.swiper-pagination').id,
            clickable: true
        };
    }

    if (photo_settings['photoAutoplay']){
        options['autoplay'] = {
            delay: photo_settings['photoAutoplaySpeed'],
            disableOnInteraction: false
        }
    }

    let thumbsOptions;
    let show_thumbs = photo_settings['showThumbs'];
    if (show_thumbs){
        let thumbsContainer = wrapper.querySelector(".swiper-wrapper-thumbs");
        if (thumbsContainer){

            let thumb_count = photo_settings['thumbCount'];
            if (window.screen.width <= 767){
                let thumb_width = photo_settings['thumbWidth'];
                let wrapper_width = window.screen.width;
                thumb_count = Math.floor((wrapper_width - 40) / parseInt(thumb_width, 10));
            }


            thumbsOptions = {
                grabCursor: true,
                autoHeight: true,
                slidesPerView: thumb_count,
                loopedSlides: thumb_count,
                freeMode: true,
                watchSlidesVisibility: true,
                watchSlidesProgress: true
            };

            if (photo_settings['showThumbArrows']){
                let arrows = thumbsContainer.querySelectorAll('.d-swiper-arrow');
                if (arrows.length){
                    thumbsOptions['navigation'] = {
                        nextEl: "#" + arrows[0].id,
                        prevEl: "#" + arrows[1].id,
                    };
                }

            }
            options['loopedSlides'] = thumb_count;

            let sliderThumbs = new Swiper("#" + thumbsContainer.id, thumbsOptions);
            options['thumbs'] = {
                swiper: sliderThumbs
            };
        }
    }

    let slider = new Swiper("#" + swiper_container.id, options);
}

/**
 * Инициализация галлерею на больших фото в товаре
 *
 * @param {Element} wrapper - обёртка товара
 */
function initDProductLightGallery(wrapper)
{
    let photos_wrapper = wrapper.querySelector(".d-top-swiper");

    lightGallery(photos_wrapper, {
        selector: '.swiper-slide:not(.swiper-slide-duplicate) a',
        thumbnail: true
    });
}


/**
 * Инициализация товаров
 */
document.addEventListener('DOMContentLoaded', (e) => {
    document.querySelectorAll('.d-atom-product').forEach((product_wrapper) => {
        initDProductSlider(product_wrapper);
        initDProductLightGallery(product_wrapper);
    });
    window.addEventListener('resize', () => {
        document.querySelectorAll('.d-atom-product').forEach((product_wrapper) => {
            initDProductSlider(product_wrapper);
        });
    })
});
