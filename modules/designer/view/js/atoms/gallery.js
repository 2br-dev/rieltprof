/**
 * Инициализация настрек галереи
 *
 * @param {Element} wrapper - обёртка галереи
 */
function initGallery(wrapper) {
    let atom_wrapper = wrapper.querySelector(".d-atom-item");
    let gallery_set = atom_wrapper.dataset;
    let gallery_id  = atom_wrapper.id;
    let int = setInterval(() => {
            if (lightGallery){
                //Создаём галлерею
                clearInterval(int);
                lightGallery(document.querySelector("#" + gallery_id), {
                    thumbnail: true,
                    selector: 'a'
                });
            }
    }, 100);
}

/**
 * Инициализация галереи
 */
document.addEventListener('DOMContentLoaded', (e) => {
    document.querySelectorAll('.d-atom-gallery').forEach((gallery_wrapper) => initGallery(gallery_wrapper));
});
