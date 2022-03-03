{addcss file="{$mod_css}photoblock.css?v=2" basepath="root"}

{addcss file="common/lightgallery/css/lightgallery.min.css" basepath="common"}
{addjs file="lightgallery/lightgallery-all.min.js" basepath="common"}

{addjs file="fileupload/jquery.iframe-transport.js" basepath="common"}
{addjs file="fileupload/jquery.fileupload.js" basepath="common"}
{addjs file="{$mod_js}photo.js" basepath="root"}

{if $param.linkid == 0}
    <div class="cant_adding">
        {t}Добавление фото возможно только в режиме редактирования.{/t}
    </div>
{else}
    <div class="photo_block" method="POST" enctype="multipart/form-data" action="{adminUrl mod_controller="photo-blockphotos" pdo="addphoto" linkid=$param.linkid type=$param.type}">
        <input type="hidden" name="redirect" value="{urlmake}"/>
        <table class="upload-block productPhotos {if empty($photo_list_html)}no-photos{/if}">
            <tr>
                <td class="dragzone-block"><div class="dragzone">{t}Чтобы начать загрузку, перетащите сюда фотографии{/t}</div></td>
                <td><button class="apply-offers-list add-offers-link" type="button">{t}назначить{/t}</button></td>
                <td><button class="delete-list" type="button" formaction="{adminUrl mod_controller="photo-blockphotos" pdo="delphoto"}">{t}удалить{/t}</button></td>
                <td><span class="check-all">{t}выбрать все{/t}</span></td>
                <td><span class="add">{t}добавить фото{/t}<input type="file" name="files[]" multiple class="fileinput"></span></td>
            </tr>
        </table>
        
        <ul class="photo-list photo-list-product overable" data-sort-url="{adminUrl mod_controller="photo-blockphotos" pdo="movephoto" do=false}" data-edit-url="{adminUrl mod_controller="photo-blockphotos" do=false pdo="editphoto"}">
            {$photo_list_html}
        </ul>
        <div class="clear"></div>
    </div>
{/if}