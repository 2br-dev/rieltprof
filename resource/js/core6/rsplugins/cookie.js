new class Cookie extends RsJsCore.classes.plugin {
    /**
     * Устанавливает значение в cookie
     *
     * @param name
     * @param value
     * @param options
     */
    setCookie(name, value, options = {}) {
        options = {
            path: '/',
            expires: new Date( Date.now() + 750 * 24 * 60 * 60 * 1000),
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }
        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }
        document.cookie = updatedCookie;
    }

    /**
     * Возвращает значение из cookie
     * @param name
     * @returns {any}
     */
    getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
};