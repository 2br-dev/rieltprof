/**
 * Скрипт инициализирует работу страницы просмотра каталога товаров
 */
new class ListProducts extends RsJsCore.classes.component {

    /**
     * Активирует переключатель сортировки товаров
     */
    initChangeSortListProducts() {
        this.utils.on('change', 'select.rs-list-sort-change',(event) => {
            let sort = event.rsTarget.value;
            let nsort = event.rsTarget.options[event.rsTarget.selectedIndex].dataset.nsort;
            this.plugins.cookie.setCookie('sort', sort);
            this.plugins.cookie.setCookie('nsort', nsort);
            location.replace( location.href );
        });
    }

    /**
     * Активирует переключатель вида отображения товаров - блочный вид/табличный вид
     */
    initChangeViewListProducts() {
        this.utils.on('click', '.rs-list-view-change',(event) => {
            event.rsTarget.closest('ul').querySelectorAll('.view-as_active').forEach(element => {
                element.classList.remove('view-as_active');
            });
            event.rsTarget.classList.add('view-as_active');

            let value = event.rsTarget.dataset.view;
            this.plugins.cookie.setCookie('viewAs', value);
            location.replace( location.href );
        });
    }

    /**
     * Актиирует переключатель количества элементов на странице
     */
    initChangePageSizeListProducts() {
        this.utils.on('change', 'select.rs-list-pagesize-change',(event) => {

            let value = event.rsTarget.value;
            this.plugins.cookie.setCookie('pageSize', value);
            location.replace( location.href );
        });
    }

    /**
     * Выполняется при загрузке DOM документа
     */
    onDocumentReady() {
        this.initChangeSortListProducts();
        this.initChangeViewListProducts();
        this.initChangePageSizeListProducts();
    }

    /**
     * Выполняется при обновлении контента на странице
     */
    onContentReady() {
        //Активируем все ajax пагинаторы
        if (this.plugins.ajaxPaginator) {
            this.plugins.ajaxPaginator.init('.rs-ajax-paginator');
        }
    }
};