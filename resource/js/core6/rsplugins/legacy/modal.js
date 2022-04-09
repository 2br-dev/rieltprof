/**
 * Открывает модальное окно с заданным содержимым.
 * Обеспечивает мост между новыми плагинами и старыми темами оформления
 */
new class Modal extends RsJsCore.classes.plugin {
    /**
     * Открывает модальное окно
     * @param html Содержимое окна
     * @param onOpen callback, вызываемый после открытия окна
     * @param options объект, любые дополнительные свойства
     */
    open(html, onOpen, options) {
        $.rsAbstractDialogModule.open(html, onOpen, options);
    }

    /**
     * Закрывает модальное окно
     */
    close() {
        $.rsAbstractDialogModule.close();
    }

    /**
     * Возвращает true, если модальное окно сейчас открыто
     *
     * @returns boolean
     */
    isOpen() {
        let selector = $.rsAbstractDialogModule.getDialogRootClass();
        return document.querySelector(selector) !== undefined;
    }

    /**
     * Отображает лоадер внутри диалогового окна
     */
    showLoader() {
    }

    /**
     * Скрывает лоадер внутри диалогового окна
     */
    hideLoader() {
    }
};