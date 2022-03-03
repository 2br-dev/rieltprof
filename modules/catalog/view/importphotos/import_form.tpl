<div class="formbox" data-dialog-options='{ "width":"700px" }'>
    <div class="inform-block">
        {t alias="Импорт фотографий. Описание"}<p>Zip-архив должен содержать изображения товаров в формате jpg, gif или png. 
        Имя каждого файла изображения должно точно соответствовать указанному ниже свойству товара</p>
        
        <p>Если к одному товару нужно прикрепить несколько изображений, то необходимо использовать следующее именование файлов:
        <p><i><свойство товара>[<символ-разделитель><номер изображения>].<расширение файла></i><br>
        Например: tovar.jpg, tovar.01.jpg, tovar.02.jpg</p>
        
        <p>Если одному изображению будет соответствовать несколько товаров, то изображение будет добавлено к нескольким товарам.</p>
        <p>Изображение с одним и тем же именем файла в случае повторной загрузки архива не будет добавлено дважды к одному товару</p>
        
        <p>Не рекомендуется использовать кириллические имена файлов внутри архивов. Как обеспечить связку изображений по свойству товара, 
        имеющему кириллические символы читайте в <a href="http://readyscript.ru/manual/catalog_zip_import_photos.html" target="_blank"><u>полной документации</u></a>.</p>
        <p><strong>Максимальный размер загружаемого файла согласно настройкам сервера: {$max_file_size}</strong></p>{/t}
    </div>
    <br>
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
        <input type="hidden" name="nextstep" value="{if $this_controller->api->issetUnpackedFiles()}3{else}2{/if}">
        <input type="submit" value="" style="display:none">
        <div class="notabs">
            <table class="otable">
                <tr>
                    {if $this_controller->api->issetUnpackedFiles()}
                    <td colspan="2" class="otitle">
                        {t}<strong>Во временной папке имеются распакованные изображения.</strong> Вы можете импортировать их или
                        <a href="{adminUrl do="cleanTmp"}" class="crud-add crud-replace-dialog"><u>удалить временные файлы и загрузить новый Zip-архив</u></a>{/t}
                    </td>
                    {else}
                    <td class="otitle">{t}Zip архив с изображениями{/t}</td>
                    <td><input type="file" name="zipfile"></td>
                    {/if}
                </tr>
                <tr>
                    <td class="otitle">{t}Свойство товара, которому соответствует имена файлов-изображений в архиве{/t}</td>
                    <td><select name="field">
                        {foreach $compare_fields as $key => $field}
                        <option value="{$key}">{$field}</option>
                        {/foreach}
                    </select></td>
                </tr>                
                <tr>
                    <td class="otitle">{t}Символ-разделитель(по умолчанию - точка){/t}</td>
                    <td><input type="text" name="separator" value="." size="2" maxlength="1"></td>
                </tr>
            </table>
        </div>
    </form>
</div>
