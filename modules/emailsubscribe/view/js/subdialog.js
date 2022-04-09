/**
* Отображает диалог подписки на новости
*/
new class SubscribeWindow extends RsJsCore.classes.component {

    /**
     * Выполняет скрипт для защиты от роботов
     *
     * @param HTML
     */
    executeScript(HTML) {
        var head = document.getElementsByTagName("head")[0];
        var scr;

        var tmp = document.createElement('div');
        tmp.innerHTML = HTML;
        var scrajx = tmp.getElementsByTagName('script');

        for(var i in scrajx) {
            scr = document.createElement("script");
            scr.type = "text/javascript";
            scr.text = scrajx[i].text;
            head.appendChild(scr);
            head.removeChild(scr);
        }
    }

    /**
     * Открывает диалог подписки на новости
     */
    openSubscribeDialog() {
        this.plugins.openDialog.show({
            url: global.emailsubscribe_dialog_url,
            callback: (response, element) => {
                this.executeScript(response.html);
            }
        })
    }

    onDocumentReady() {
        if (global.emailsubscribe_dialog_open_delay){ //Если опция настроена и включена
            setTimeout(() => {this.openSubscribeDialog()}, global.emailsubscribe_dialog_open_delay * 1000);
        }
    }
};