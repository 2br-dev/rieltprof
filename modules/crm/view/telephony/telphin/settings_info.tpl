<div data-refresh-event-gate-url="{adminUrl do="refreshEventUrlsTelephony" provider=$provider->getId() mod_controller="crm-tools"}">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td>{t}Тип{/t}</td>
                    <td>{t}Метод{/t}</td>
                    <td>{t}URL{/t}</td>
                </tr>
            </thead>
            <tbody>
            {foreach $provider->getAllowEventTypes() as $type => $method}
                <tr>
                    <td>{$type}</td>
                    <td>{$method}</td>
                    <td>{$provider->getEventGateUrl($type)}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>

    {t}Перейдите к списку сотрудников в <a href="https://teleo.telphin.ru/staff/" target="_blank">личном кабинете Телфин</a>.
        Укажите в настройках каждого сотрудника следующие URL на вкладке события. Или воспользуйтесь кнопкой автоматической установки.
        При изменении произвольного секретного ключа на вкладке Телефония или списка добавочных, необходимо нажать на кнопку заново.{/t}
    <br><a id="set-event-url" data-url="{adminUrl do="setEventUrl" mod_controller="crm-telphinctrl" provider=$provider->getId()}" class="btn btn-default m-t-10">{t}Установить URL для событий пользователям добавочного{/t}</a>

    <script>
        $(function() {
            $('#set-event-url').click(function() {

                var formData = $(this).closest('form').serializeArray();
                $.ajaxQuery({
                    method: 'POST',
                    url: $(this).data('url'),
                    data: formData
                });
            });
        });

    </script>
</div>