<div class="formbox" data-dialog-options='{ "width":"500", "height":"400" }'>
        <div class="notice notice-danger">
            {t alias="Перед загрузкой XML файла.."}Перед загрузкой XML файла со схемой блоков, информация по существующим блокам, секциям, страницам будет удалена.
            Чтобы иметь возможность восстановить текущее состояние структуры блоков, рекомендуется сделать экспорт данных.{/t}
        </div>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <div class="notabs">
                <table class="otable">
                                                                <tr>
                    <td class="otitle">{t}XML файл{/t}</td>
                    <td>{include file="%system%/admin/fileinput.tpl" form_name="file"}</td>
                </tr>
                                                                                </table>
            </div>
        </form>
    </div>