let mmenu_event_click = ('ontouchstart' in document.documentElement && navigator.userAgent.match(/Mobi/)) ? 'touchstart' : 'click'; //Какое событие использовать
/**
 * Мобильное меню для модуля блок дизайнера
 */
class mobileMMenu{
    /**
     * Открытие самого меню
     * @param {MouseEvent} event - событие нажатия
     */
    openMenu(event)
    {
        let body = document.querySelector('body');
        body.insertAdjacentHTML("beforeend", `
            <div id="d-mobile-mmenu" class="d-mobile-mmenu">
                <div id="d-mobile-mmenu-header" class="d-mobile-mmenu-header"></div>
                <a  id="d-mobile-mmenu-close" class="d-mobile-mmenu-close"></a>
                <div class="d-mobile-mmenu-content"></div>
            </div>
            <div id="d-mobile-mmenu-fog" class="d-mobile-mmenu-fog"></div>
        `);
        body.classList.add('mmenu-hidden');
        let left_menu = document.querySelector("#d-mobile-mmenu");
        let menu_id = event.target.closest('a').dataset['id'];
        let menu_wrapper = document.querySelector(`[data-mmenu-id="${menu_id}"]`);

        left_menu.querySelector('.d-mobile-mmenu-content').insertAdjacentHTML("beforeend", menu_wrapper.innerHTML);
        left_menu.querySelector('.d-mobile-mmenu-header').innerHTML = menu_wrapper.dataset.title;
        mobileMMenu.addLevelEvents();
        setTimeout(() => {
            left_menu.classList.add('d-open');
        }, 200);
    }

    /**
     * Закрытие меню
     *
     * @param {MouseEvent} event - событие нажатия
     */
    closeMenu(event)
    {
        let id = event.target.getAttribute('id');
        let body = document.querySelector('body');
        if (id == 'd-mobile-mmenu-close' || id == 'd-mobile-mmenu-fog') { //Если это кнопка закрытия или подложка
            mobileMMenu.removeLevelEvents();
            let left_menu = document.querySelector("#d-mobile-mmenu");
            left_menu.classList.remove('d-open');

            body.classList.remove('mmenu-hidden');
            let event_click = new Event('click')
            let designer_close = document.querySelector('.design-menu-overflow .d-close');
            if (designer_close){
                designer_close.dispatchEvent(event_click);
            }
            setTimeout(() => { //Удалим всё из dom
                body.removeEventListener(mmenu_event_click, this.closeMenu);
                left_menu.remove();
                document.querySelector('#d-mobile-mmenu-fog').remove();
            }, 300);
        }
        let is_in_debug = body.classList.contains('debug-mode-blocks');
        let href = event.target.getAttribute('href');
        if (!is_in_debug){
            if (href && !href.length){
                event.stopPropagation();
                event.preventDefault();
            }
        }else{
            event.stopPropagation();
            event.preventDefault();
        }
    }

    /**
     * Открытие открытие следующего уровня меню
     *
     * @param {MouseEvent} event - событие нажатия
     */
    static openLevel(event)
    {
        if (!event.target.closest('li').classList.contains('d-mobile-mmenu-close-level')){
            let left_menu   = document.querySelector("#d-mobile-mmenu");
            let level_title = event.target.dataset['title'];
            let wrapper = event.target.closest('li');
            let ul = wrapper.querySelector(`ul`);
            if (ul){
                wrapper.querySelector(`ul`).classList.add('d-open');
            }
            left_menu.querySelector('#d-mobile-mmenu-header').innerHTML = level_title;
        }
    }

    /**
     * Закрытие текущего уровня меню
     *
     * @param {MouseEvent} event - событие нажатия
     */
    static closeLevel(event)
    {
        let left_menu = document.querySelector("#d-mobile-mmenu");
        let level_title = event.target.closest('li').dataset['title'];
        event.target.closest('.d-mobile-mmenu-level').classList.remove('d-open');
        left_menu.querySelector('#d-mobile-mmenu-header').innerHTML = level_title;
        event.stopPropagation();
        event.preventDefault();
    }

    /**
     * Добавляет все события из меню
     */
    static addLevelEvents()
    {
        let left_menu = document.querySelector("#d-mobile-mmenu");
        let items = left_menu.querySelectorAll('.d-mobile-mmenu-close-level');
        items.forEach((item) => {
            item.addEventListener(mmenu_event_click, mobileMMenu.closeLevel);
        });
        items = left_menu.querySelectorAll('.d-mobile-mmenu-open-level');
        if (items){ //Назначим событие, которое откроет следующий уровень
            items.forEach((item) => {
                item.addEventListener(mmenu_event_click, mobileMMenu.openLevel);
            });
        }
    }

    /**
     * Удаляет все события из меню
     */
    static removeLevelEvents()
    {
        let left_menu = document.querySelector("#d-mobile-mmenu");
        let items = left_menu.querySelectorAll('.d-mobile-mmenu-close-level');
        items.forEach((item) => {
            item.removeEventListener(mmenu_event_click, mobileMMenu.closeLevel);
        });
        items = left_menu.querySelectorAll('.d-mobile-mmenu-open-level');
        if (items){ //Назначим событие, которое откроет следующий уровень
            items.forEach((item) => {
                item.removeEventListener(mmenu_event_click, mobileMMenu.openLevel);
            });
        }
    }

    /**
     * Инициализация открытия мобильного меню
     */
    init()
    {
        let items = document.querySelectorAll('.designer-mmenu');
        if (items){
            items.forEach((menuLink) => {
                menuLink.removeEventListener(mmenu_event_click, this.openMenu);
                menuLink.addEventListener(mmenu_event_click, this.openMenu);
            });
        }
        let body = document.querySelector('body');
        body.removeEventListener(mmenu_event_click, this.closeMenu);
        body.addEventListener(mmenu_event_click, this.closeMenu);
    }
}

let mobileMMenuClass;
//Инициализиция открытия мобильного меню
document.addEventListener("DOMContentLoaded", function(event) {
    mobileMMenuClass = new mobileMMenu();
    mobileMMenuClass.init();
});

//Делаем событие для обновления
document.addEventListener("designer.init-mmenu", function(event) {
    if (!mobileMMenuClass){ //Если сущности ещё нет, т.к. она не отработала
        mobileMMenuClass = new mobileMMenu();
    }
    mobileMMenuClass.init();
});