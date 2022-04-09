/**
 * Инициализирует работу выпадающего меню категорий в шапке сайта
 */
new class Category extends RsJsCore.classes.component {

    /**
     * Открывает выпадающий каталог
     */
    dropdownOpen()  {
        this.plugins.scroller.saveScroll();
        this.plugins.scroller.scroll(0, 0);

        const overlay = document.createElement('div');
        overlay.classList.add('dropdown-overlay');
        document.body.prepend(overlay);
        this.dropdownCatalog.classList.add('d-block');
        this.dropdownCatalog.style.top = this.head.clientHeight + 'px';
        overlay.addEventListener('click', this.dropdownCloseHandler);
        this.dropdownCatalogBtn.removeEventListener('click', this.dropdownOpenHandler);
        this.dropdownCatalogBtn.addEventListener('click', this.dropdownCloseHandler);
    };

    /**
     * Открывает выпадающий каталог
     */
    dropdownClose() {
        document.querySelector('.dropdown-overlay').remove();
        this.dropdownCatalog.classList.remove('d-block');
        this.dropdownCatalogBtn.removeEventListener('click', this.dropdownCloseHandler);
        this.dropdownCatalogBtn.addEventListener('click', this.dropdownOpenHandler);
        this.plugins.scroller.returnToPrevScroll();
    };

    /**
     * Активирует отображение подкатегорий в выпадающем каталоге
     *
     * @param links
     * @param subcategories
     * @param linkAct
     */
    dropdownBind(links, subcategories, linkAct, defaultSubCategory) {
        let dropdownChange = function(links, subcategories, linkAct) {
            links.forEach( (it) => it.classList.remove(linkAct));

            if (this.dataset.target) {
                subcategories.forEach( (it) => it.classList.remove('d-block'));

                let panel;
                let realPanel = document.getElementById(this.dataset.target);
                panel = realPanel || document.getElementById(defaultSubCategory);

                if (panel) {
                    panel.classList.add('d-block');
                }
                if (realPanel) {
                    this.classList.add(linkAct);
                }
            }
        };

        if (links.length) {
            links.forEach(function (it) {
                it.addEventListener('mouseover', dropdownChange.bind(it, links, subcategories, linkAct));
                it.addEventListener('touch', function (e) {
                    e.preventDefault();
                    dropdownChange.call(it, links, subcategories, linkAct);
                });
            });
        }
    };

    /**
     * Инициализирует выпадающий каталог
     */
    initDropdown() {
        this.dropdownOpenHandler = this.dropdownOpen.bind(this);
        this.dropdownCloseHandler = this.dropdownClose.bind(this);

        this.dropdownCatalogBtn = document.querySelector('.dropdown-catalog-btn');
        this.dropdownCatalog = document.querySelector('.head-dropdown-catalog');
        this.head = document.querySelector('.head');

        if (this.dropdownCatalogBtn) {
            this.dropdownCatalogBtn.addEventListener('click', this.dropdownOpenHandler);

            const dropdownLinks = document.querySelectorAll('.head-dropdown-catalog__category');
            const dropdownSubcategories = document.querySelectorAll('.head-dropdown-catalog__subcat');

            this.dropdownBind(dropdownLinks, dropdownSubcategories ,'head-dropdown-catalog__category_active', 'dropdown-subcat-0');

            const dropdownSubLinks = document.querySelectorAll('.head-dropdown-catalog__subcat-list-item');
            const dropdownSubSubcategories = document.querySelectorAll('.head-dropdown-catalog__subsubcat');

            this.dropdownBind(dropdownSubLinks, dropdownSubSubcategories ,'head-dropdown-catalog__subcat-list-item_active');
        }
    }

    onDocumentReady() {
        this.initDropdown();
    }

};