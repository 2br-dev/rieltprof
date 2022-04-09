/**
 * Инициализирует работу отзывов
 */
new class Comments extends RsJsCore.classes.component {

    constructor(settings) {
        super();

        let defaults = {
            context: '.rs-comments',
            stars: '.rs-stars li',
            rate: '.rs-rate',
            inputRate: '.inp_rate',
            rateDescr: '.rs-rate-descr',
            activeClass: 'active',
            rateText: [
                lang.t('нет оценки'),
                lang.t('ужасно'),
                lang.t('плохо'),
                lang.t('нормально'),
                lang.t('хорошо'),
                lang.t('отлично')
            ]
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    /**
     * Инициаизирует выбор оценки для отзыва
     *
     * @param event
     */
    initStarsSelect(event) {
        this.context = event.target.querySelector(this.settings.context);

        if (this.context) {
            this.context.querySelectorAll(this.settings.stars).forEach((element) => {
                element.addEventListener('mouseover', event => this.overStar(event));
                element.addEventListener('mouseout', event => this.restoreStars(event));
                element.addEventListener('click', event => this.setMark(event));
            });

            this.restoreStars();
        }
    }

    /**
     * Возвращает порядковый номер элемента среди братьев в DOM
     *
     * @param element
     * @returns {number}
     */
    getNodeIndex(element) {
        return [...element.parentNode.children].indexOf(element);
    }

    /**
     * Обработчик наведения мыши на звезду
     *
     * @param event
     */
    overStar(event) {
        this.selectStars(this.getNodeIndex(event.target)+1);
    }

    /**
     * Подсвечивает нужную оценку на звездах
     *
     * @param index
     */
    selectStars(index) {
        let allStars = this.context.querySelectorAll(this.settings.stars);
        allStars.forEach(element => {
            element.classList.remove(this.settings.activeClass);
        });

        for(let i=0; i <= index - 1; i++) {
            allStars[i].classList.add(this.settings.activeClass);
        }

        let description = this.context.querySelector(this.settings.rateDescr);
        description && (description.innerText = this.settings.rateText[index]);
    }

    /**
     * Восстанавливает визуальное отображение оценки к предыдущему закрепленному значению
     */
    restoreStars() {
        this.selectStars( this.context.querySelector(this.settings.inputRate).value );
    }

    /**
     * Фиксирует новую оценку
     *
     * @param event
     */
    setMark(event) {
        this.context.querySelector(this.settings.inputRate).value = this.getNodeIndex(event.target) + 1;
    }

    /**
     * Обработчик document.ready
     */
    onDocumentReady() {
        //Активируем все ajax пагинаторы
        this.plugins.ajaxPaginator.init('.rs-ajax-paginator');
    }

    /**
     * Обработчик события нового контента на странице
     *
     * @param event
     */
    onContentReady(event) {
        this.initStarsSelect(event);
    }
};