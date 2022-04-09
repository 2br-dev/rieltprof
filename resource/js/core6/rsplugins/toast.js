/**
 * Плагин позволяет отображать тосты (всплывающие сообщения) из JavaScript в удобной форме
 * Зависит от bootstrap.Toast
 */
new class Toast extends RsJsCore.classes.plugin {

    constructor()
    {
        super();
        let defaults = {
            containerClassName: "toast-container position-fixed bottom-0 end-0 p-3"
        };
        this.settings = {...defaults, ...this.getExtendsSettings()};
    }
    /**
     * Возвращает объект элемента, в который будут добавлены тосты
     *
     * @returns {HTMLDivElement}
     * @private
     */
    _getContainer()
    {
        if (!this.container) {
            //Подготавливаем контейнер для тостов
            this.container = document.createElement('div');
            this.container.className = this.settings.containerClassName;
            document.body.appendChild(this.container);
        }
        return this.container;
    }

    /**
     * Подготавливает HTML тоста
     *
     * @param title
     * @param message
     * @param options
     * @returns {HTMLDivElement}
     * @private
     */
    _makeToast(title, message, options) {
        let toast = document.createElement('div');
        toast.className = 'toast ' + (options.className ? options.className : '');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `<div class="toast-header">
                            <strong class="me-auto">${title}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>                              
                          </div>
                          <div class="toast-body">
                            ${message}
                          </div>`;
        return toast;
    }

    /**
     * Отображает тост
     *
     * @param title Заголовок сообщения
     * @param message Текст сообщения
     * @param options Параметры отображения тоста
     * @returns {Toast|Toast}
     */
    show(title, message, options) {
        let defaults = {
            className: '',
            animation: true,
            autohide: true,
            delay: 10000
        };
        let settings = {...defaults, ...options};

        let toastElement = this._makeToast(title, message, settings);
        this._getContainer().appendChild(toastElement);

        let toastInstance = new bootstrap.Toast(toastElement, settings);
        toastInstance.show();

        return toastInstance;
    }

};