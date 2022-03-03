/**
 * Инициалищация слайдера
 * @param {HTMLElement} slider_wrapper - элемент слайдера
 */
function initDSlider(slider_wrapper) {
    let element   = slider_wrapper.querySelector(".d-atom-item").dataset;
    let id_slider = slider_wrapper.querySelector(".swiper-container").id;
    let id_next   = slider_wrapper.querySelector(".swiper-button-next").id;
    let id_prev   = slider_wrapper.querySelector(".swiper-button-prev").id;
    let id_pag    = slider_wrapper.querySelector(".swiper-pagination").id;

    let options = {
        lazy: true,
        autoHeight: true, //enable auto height
        grabCursor: true,
        slidesPerView: element.itemsCount,
        freeMode: true,
    };

    if (element.showLoop) {
        options['loop'] = true;
    }

    if (element.showArrows) {
        options['navigation'] = {
            nextEl: '#' + id_next,
            prevEl: '#' + id_prev
        }
    }

    if (element.showPagination) {
        options['pagination'] = {
            el: '#' + id_pag,
            clickable: true
        };
    }

    if (element.autoplaySpeed) {
        options['autoplay'] = {
            delay: element.autoplaySpeed,
            disableOnInteraction: false
        }
    }
    let swiperAtomId = "#" + id_slider;

    let thumbsOptions;
    let show_thumbs = element['showThumbs'];
    if (show_thumbs){
        let thumbsContainer = slider_wrapper.querySelector(".swiper-wrapper-thumbs");
        if (thumbsContainer){

            let thumb_count = element['thumbCount'];
            if (window.screen.width <= 767){
                let thumb_width = element['thumbWidth'];
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

            if (element['showThumbArrows']){
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

    let slider =  new Swiper(swiperAtomId, options);   
}


document.addEventListener('DOMContentLoaded', (e) => {
    document.querySelectorAll('.d-atom-slider').forEach((slider_wrapper) => {
        initDSlider(slider_wrapper);
    });
});