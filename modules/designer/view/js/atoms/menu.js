/**
 * Инициалищация слайдера
 * @param {HTMLElement} menu_wrapper - элемент меню
 */
function initTogglerMenu(menu_wrapper) {
    let element = menu_wrapper.querySelector(".d-atom-item").dataset;
    menu_wrapper.querySelectorAll('.d-atom-menu-down').forEach(menuDown => {
        menuDown.addEventListener('click', (e) => {
           let down = e.target;
            e.target.classList.toggle('d-open');
            down.closest('li').classList.toggle('d-open');
            e.preventDefault();
            e.stopPropagation();
        });
    });
    menu_wrapper.querySelectorAll('.d-atom-sub-menu-down').forEach(menuDown => {
        menuDown.addEventListener('click', (e) => {
           let down = e.target;
            e.target.classList.toggle('d-open');
            down.closest('li').classList.toggle('d-open');
            e.preventDefault();
            e.stopPropagation();
        });
    });
}

document.addEventListener('DOMContentLoaded', (e) => {
    document.querySelectorAll('.d-atom-menu').forEach((menu_wrapper) => {
        initTogglerMenu(menu_wrapper);
    });
});