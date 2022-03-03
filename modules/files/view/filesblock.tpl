{addjs file="fileupload/jquery.iframe-transport.js" basepath="common"}
{addjs file="fileupload/jquery.fileupload.js" basepath="common"}
{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="%files%/files.js"}
{addcss file="%files%/files.css "}
<div class="files-block" data-urls='{ "fileUpload": "{adminUrl files_do="Upload" mod_controller="files-block-files" link_type=$link_type link_id=$link_id}",
                                      "fileDelete": "{adminUrl files_do="Delete" mod_controller="files-block-files" link_type=$link_type link_id=$link_id}",
                                      "fileEdit": "{adminUrl files_do="Edit" mod_controller="files-block-files" link_type=$link_type link_id=$link_id}",
                                      "fileChangeAccess": "{adminUrl files_do="changeAccess" mod_controller="files-block-files" link_type=$link_type link_id=$link_id}" }'>
    <table class="upload-block">
        <tr>
            <td class="dragzone-block">
                <div class="dragzone">
                    {t size=$max_upload_size|format_filesize}{t}Чтобы начать загрузку, перетащите сюда файлы. Максимальный размер файлов для загрузки -{/t} %size{/t}
                </div>
            </td>
            <td>
                <span class="add">{t}добавить файлы{/t}
                    <input type="file" class="fileinput" multiple="" name="files[]">
                </span>
            </td>
        </tr>
    </table>
    <div class="files-container{if !$files} hidden{/if}">
        <br>
        <table data-sort-request="{adminUrl files_do="Move" mod_controller="files-block-files" link_type=$link_type link_id=$link_id}" class="rs-table editable-table files-list virtual-form">
            <thead>
                <tr>
                    <th style="width:26px" class="chk">
                        <div class="chkhead-block">
                            <input type="checkbox" class="chk_head select-page" data-name="files[]" alt="">                        
                        </div>
                    </th>
                    <th width="20" class="drag"><span class="sortable sortdot asc"><span></span></span></th>                        
                    <th class="title">{t}Имя файла{/t}</th>
                    <th class="description"><span class="hidden-xs">{t}Описание{/t}</span></th>
                    <th class="size"><span class="hidden-xs">{t}Размер{/t}</span></th>
                    <th class="access">{t}Доступ{/t}</th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody> 
                {foreach $files as $file}
                    {include file="%files%/one_file.tpl" linked_file=$file}
                {/foreach}
            </tbody>
        </table>
        <div class="group-toolbar">
            <span class="checked-offers">{t alias="отмеченные файлы"}Отмеченные<br> файлы{/t}</span> <a class="btn btn-danger delete">{t}Удалить{/t}</a>
        </div>
    </div>
</div>