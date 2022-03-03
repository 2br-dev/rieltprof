{if (!$is_empty_tmp)}
<div class="mod-exists notice notice-warning p-10">
    {t}Во временной папке есть файлы.{/t} <a href="{adminUrl do=addStep2}">{t}Подробнее...{/t}</a>
</div>
{/if}
<div class="formbox">
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":"500px", "height":"350" }'>

        {include file="%system%/admin/fileinput.tpl" form_name="module"}

    </form>
</div>