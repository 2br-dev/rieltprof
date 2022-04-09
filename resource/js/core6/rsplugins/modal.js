/**
 * Открывает модальное окно с заданным содержимым.
 * Зависит от bootstrap.Modal
 */
new class Modal extends RsJsCore.classes.plugin {
    /**
     * Открывает модальное окно
     * @param html Содержимое окна
     * @param onOpen callback, вызываемый после открытия окна
     * @param options объект, любые дополнительные свойства
     */
    open(html, onOpen, options) {
        let isReopen = this.isOpen();
        if (this.modal) {
            //При повторном открытии без анимации закрываем текущее окно
            this.modal._element.classList.remove('fade');
            this.close();
        }

        let element = document.createElement('div');
        element.className = 'modal rs-dialog' + (isReopen ? '' : ' fade');
        element.addEventListener('shown.bs.modal', (event) => {
            if (isReopen) {
                let modal = bootstrap.Modal.getInstance(event.target);
                element.classList.add('fade');
                modal._backdrop.classList.add('fade');
            }
            element.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
        });

        element.addEventListener('shown.bs.modal', onOpen);
        element.addEventListener('hide.bs.modal', () => {
            this.modal = null
        });
        element.addEventListener('hidden.bs.modal', (event) => {
            let modal = bootstrap.Modal.getInstance(event.target);
            modal.dispose();
            event.target.remove();
        });

        element.innerHTML = html;

        this.modal = new bootstrap.Modal(element, options);
        this.modal.show();
    }

    /**
     * Закрывает модальное окно
     */
    close() {
        if (this.modal) {
            this.modal.hide();
        }
    }

    /**
     * Возвращает true, если модальное окно сейчас открыто
     *
     * @returns boolean
     */
    isOpen() {
        return !!this.modal;
    }

    /**
     * Отображает лоадер внутри диалогового окна
     */
    showLoader() {
        if (this.modal) {
            this.loader = document.createElement('div');
            this.loader.classList.add('rs-loader');
            this.modal._element.querySelector('.modal-content').append(this.loader);
        }
    }

    /**
     * Скрывает лоадер внутри диалогового окна
     */
    hideLoader() {
        if (this.loader) {
            this.loader.remove();
        }
    }
};