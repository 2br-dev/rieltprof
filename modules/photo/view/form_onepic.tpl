{foreach from=$photo_list item=photo key=key}
    <li class="photo-one" data-id="{$photo.id}">
        <div class="chk"><input type="checkbox" name="photos[]" value="{$photo.id}"></div>
        <div class="image">
            <a href="{adminUrl mod_controller="photo-blockphotos" do=false pdo="delphoto" photos=[{$photo.id}]}" class="delete confirm-delete" title="{t}удалить фото{/t}"></a>
            <a href="{$photo->getUrl(800, 600, 'xy')}" rel="lightbox-tour" class="bigview"><img src="{$photo->getUrl(140, 146, 'cxy')}"></a>
        </div>
        <div class="title">
            <div class="short" title="{t}Нажмите, чтобы редактировать{/t}">{$photo.title|escape}</div>
            <div class="more">...</div>
            <textarea class="edit_title">{$photo.title}</textarea>
        </div>
        <div class="move">
            <a class="rotate ccw" title="{t}Повернуть против часовой стрелки{/t}" href="{adminUrl mod_controller="photo-blockphotos" do=false pdo="rotate" photoid=$photo.id direction="ccw"}"></a>            
            <div class="handle"></div>
            <a class="rotate cw" title="{t}Повернуть по часовой стрелке{/t}" href="{adminUrl mod_controller="photo-blockphotos" do=false pdo="rotate" photoid=$photo.id direction="cw"}"></a>            
        </div>
    </li>
{/foreach}