/**
 * Компонент инициализирует форму подписки на новости
 */
new class EmailSubscribe extends RsJsCore.classes.component
{
    executeScript(HTML) {
        var head = document.getElementsByTagName("head")[0];
        var scr;

        var tmp = document.createElement('div');
        tmp.innerHTML = HTML;
        var scrajx = tmp.getElementsByTagName('script');

        for(var i in scrajx) {
            scr = document.createElement("script");
            scr.text = scrajx[i].text;
            head.appendChild(scr);
            head.removeChild(scr);
        }
    }

    initAjaxForm() {
        this.utils.on('submit', '.rs-mailing', (event) => {
            event.preventDefault();
            let form = event.rsTarget;
            let mailingBlock = event.rsTarget.closest('.rs-mailing-block');

            let formData = new FormData(event.target);
            this.utils.fetchJSON(form.action, {
                method:'POST',
                body:formData
            }).then((response) => {
                if (response.html) {
                    let parent = mailingBlock.parentNode;
                    mailingBlock.insertAdjacentHTML("afterend", response.html);
                    mailingBlock.remove();
                    this.executeScript(response.html);
                    parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                }
            });

        });
    }

    onDocumentReady() {
        this.initAjaxForm();
    }
};