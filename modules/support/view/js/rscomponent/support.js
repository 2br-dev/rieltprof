/**
 * Обеспечивает работоспособность раздела Личный кабинет -> Поддержка
 */
new class Support extends RsJsCore.classes.component {

    constructor(settings)
    {
        super();
        let defaults = {
            topicId: '.rs-support-topic-id',
            topicName: '.rs-support-topic-name',
            topicRemove: '.rs-topic-delete',
            hiddenClass: 'd-none'
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    initTopicSelect() {
        let topicId = document.querySelector(this.settings.topicId);
        topicId && topicId.addEventListener('change', (element) => {

            let newTopicName = document.querySelector(this.settings.topicName);
            element.target.value == '0' ? newTopicName.classList.remove(this.settings.hiddenClass) :
                newTopicName.classList.add(this.settings.hiddenClass);
        });

    }

    initTopicDelete() {
        this.utils.on('click', this.settings.topicRemove, (event) => {
            event.preventDefault();
            if (!confirm(lang.t('Вы действительно хотите удалить переписку по теме?'))) return false;
            let button = event.rsTarget;
            let topic = button.closest('[data-id]');
            topic.style.opacity = 0.5;
            this.utils.fetchJSON(button.dataset.removeUrl)
                .then(response => {
                    if (response.success) {
                        location.reload();
                    }
                });
        });
    }

    onDocumentReady() {
        this.initTopicSelect();
        this.initTopicDelete();
    }
};