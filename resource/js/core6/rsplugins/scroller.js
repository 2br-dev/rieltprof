/**
 * Плагин, позволяет сохранять и возвращаться к предыдущему состоянию прокрутки (Scroll)
 */
new class Scroller extends RsJsCore.classes.plugin {

    constructor() {
        super();
        this.prevScroll = 0;
    }

    /**
     * Сохраняет текущее состояние прокрутки
     * и при заданных newX или newY перемещает на новое состояние
     *
     * @param newX
     * @param newY
     */
    saveScroll(newX, newY) {
        this.prevScroll = window.scrollY;
        if (newX || newY) {
            window.scroll(newX, newY);
        }
    }

    /**
     * Возвращает скролл к предыдущему сохраненному состоянию
     */
    returnToPrevScroll() {
        window.scrollTo(0, this.prevScroll);
    }

    /**
     * Перемещает скрол на нужную позицию
     */
    scroll(x, y) {
        window.scroll(x, y);
    }
};