/**
 * Компонент обеспечивает закрытие "всплывающего блока оповещения о политике использования cookie" на страницах
 * Зависит от плагина cookie.js
 */
new class CookiePolicy extends RsJsCore.classes.component
{
    onDocumentReady() {
        const cookiesPolicy = document.querySelector('.cookies-policy');
        const cookiesPolicyBtn = cookiesPolicy && cookiesPolicy.querySelector('.btn');

        if (cookiesPolicy) {
            cookiesPolicyBtn.addEventListener('click',  () => {
                cookiesPolicy.classList.remove('cookies-policy_active');
                this.plugins.cookie.setCookie('cookiesPolicy', '1');
            });
        }
    }
};