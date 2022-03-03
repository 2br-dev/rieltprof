{addjs file="fileinput/fileinput.min.js" basepath="common"}

{if $field}
    {* Форма загрузки нового файла и отображения текущего. field - \RS\Orm\Type\Abstract *}
    <div class="fileinput fileinput-{if $field->get() != ''}exists{else}new{/if}" data-provides="fileinput">
        <span class="btn btn-default btn-file">
            <span class="fileinput-new">{t}Выберите файл{/t}</span>
            <span class="fileinput-exists">{t}Изменить{/t}</span>
            <input type="file" name="{$field->getFormName()}">
            <input type="hidden" value="0" name="del_{$field->getName()}" class="remove">
        </span>
        <span class="fileinput-filename">
            {if $field->get() != ''}<a href="{$field->getLink()}" target="_blank">{$field->getFileName()}</a>{/if}
        </span>

        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
    </div>
{else}
    {* Форма загрузки нового файла *}
    <div class="fileinput fileinput-new" data-provides="fileinput">
        <span class="btn btn-default btn-file">
            <span class="fileinput-new">{t}Выберите файл{/t}</span>
            <span class="fileinput-exists">{t}Изменить{/t}</span>
            <input type="file" name="{$form_name}">
        </span>
        <span class="fileinput-filename"></span>
        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
    </div>
{/if}