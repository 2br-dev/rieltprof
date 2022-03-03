{addjs file="fileinput/fileinput.min.js" basepath="common"}

<div class="fileinput fileinput-{if $field->get() != ''}exists{else}new{/if}" data-provides="fileinput">
    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width:270px; height:270px; line-height:270px">
        {if $field->get() != ''}
            {if $elem->isImageFile()}
                <img src="{$elem->getImageUrl(270, 270)}">
            {else}
                <a href="{$field->getLink()}" target="_blank">{$field->getFileName()}</a>
            {/if}
        {/if}
    </div>

    <div>
        <span class="btn btn-default btn-file">
            <span class="fileinput-new">{t}Выберите файл{/t}</span>
            <span class="fileinput-exists">{t}Изменить{/t}</span>
            <input type="file" name="{$field->getFormName()}">
            <input type="hidden" value="0" name="del_{$field->getName()}" class="remove">
        </span>

        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">{t}Удалить{/t}</a>
    </div>
</div>