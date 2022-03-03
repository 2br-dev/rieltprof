{addjs file="fileinput/fileinput.min.js" basepath="common"}

<div class="deleteSpecDir">
    <label><input type="checkbox" name="simage[delete_all_photos]" value="1"> {t}Удалить все фото у товаров{/t}</label>
</div>

<div class="fileinput fileinput-new" data-provides="fileinput">
    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
    <div>
        <span class="btn btn-default btn-file">
            <span class="fileinput-new">{t}Выберите файл{/t}</span>
            <span class="fileinput-exists">{t}Изменить{/t}</span>
            <input type="file" name="simagefile">
        </span>

        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">{t}Удалить{/t}</a>
    </div>
</div>

<input type="hidden" name="simage[use]" value="1">