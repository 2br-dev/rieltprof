<div class="archiveResult" data-dialog-options='{ "width":"500", "height":"450" }'>
        <br>
        <p>{$title}</p>
        {if $step != 5}<p>{t}Шаг:{/t} {$step + 1}</p>{/if}
        {if $step > -1 && $step != 5}
            <p>{t}Получение документов{/t}</p>
        {/if}
        {if $step > 0 && $step != 5}
            <p>{t}Количество документов:{/t} {count($params.docs_id)}</p>
            <p>{t}Перемещение документов{/t}</p>
        {/if}
        {if $step > 1 && $step != 5}
            <p>{t}Очистка старых данных{/t}</p>
        {/if}
        {if $step > 2 && $step != 5}
            <p>{t}Обновление базы данных{/t}</p>
        {/if}
        {if $step > 3 && $step != 5}
            <p>{t}Обновление архивных документов{/t}</p>
        {/if}
        {if $step == 5}
            <p>{t}Успешно завершено{/t}</p>
        {/if}

    {if $step != 5}
        <script>
            $.allReady(function() {
                $.ajaxQuery({
                    url: '{$router->getAdminUrl("ProcessArchive",['mode' => $mode] ,"catalog-inventoryctrl")}',
                    data: { 'step': {json_encode($step + 1)}, 'date' : '{$date}', 'mode' : '{$mode}'},
                    type: 'POST',
                    success: function(response) {
                        $('.dialog-window').children().remove();
                        $('.dialog-window')
                                .append(response.html)
                                .trigger('new-content')
                                .trigger('initContent')
                                .trigger('contentSizeChanged');
                    }
                });
            });
        </script>
    {/if}

</div>


