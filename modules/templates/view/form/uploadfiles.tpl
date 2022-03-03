{addcss file="%templates%/uploadfiles.css"}
{addjs file="fileupload/jquery.fileupload.js"}
{addjs file="fileupload/jquery.iframe-transport.js"}
{addjs file="%templates%/jquery.uploadtemplatefiles.js"}
<div id="upload-files" data-upload-url="{$router->getAdminUrl('uploadFile', ['path' => $path])}">
    <div class="notice-box upload-files-notice">
        {t}Папка для загрузки:{/t} <strong>{$path}</strong><br>
        {t}Для загрузки допускаются файлы со следующими расширениями:{/t}
        {implode(',', $allow_ext)}
    </div>
    <form class="upload-files-dnd crud-form" enctype="multipart/form-data" method="POST">
        <input type="file" name="files[]" multiple class="inputUploadFile" style="display:none">
        {t alias="Перетащите файлы сюда..."}Перетащите сюда файлы или <a class="selectUploadFile">выберите файлы на диске</a>{/t}
    </form>

    <table class="upload-files-table hidden">
        <thead>
            <tr>
                <th>{t}Файл{/t}</th>
                <th>{t}Размер{/t}</th>
                <th>{t}Статус{/t}</th>
                <th><a class="cancel-all">{t}отменить все{/t}</a></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    $.allReady(function() {
        $('#upload-files').uploadTemplateFiles();
    });
</script>