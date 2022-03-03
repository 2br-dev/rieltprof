/**
 * Инициализация открытия вопросника
 *
 * @param {Element} wrapper - обёртка товара
 */
function initList(wrapper)
{
    let atom_wrapper     = wrapper.querySelector(".d-atom-item");
    let photo_settings   = atom_wrapper.dataset;

    //Навешаем события переключения
    wrapper.querySelectorAll('a.d-atom-list-toggler').forEach((item)=> {
        item.addEventListener('click', (e) => {
            let this_item = e.target.closest('.d-atom-list-toggler');
            let wrapper = this_item.closest('.d-atom-list-info');
            let image   = wrapper.querySelector('.d-atom-list-toggler-image');
            let desc    = wrapper.querySelector('.d-atom-list-desc');
            let item    = e.target.closest('.d-atom-list-item');

            if (image){
                image.classList.toggle('d-open');
            }
            if (desc){
                desc.classList.toggle('d-open');
            }
            if (item){
                item.classList.toggle('d-open');
            }
        });
    });
}

/**
 * Инициализация товаров
 */
document.addEventListener('DOMContentLoaded', (e) => {
    document.querySelectorAll('.d-atom-dlist').forEach((list_wrapper) => {
        initList(list_wrapper);
    });
});