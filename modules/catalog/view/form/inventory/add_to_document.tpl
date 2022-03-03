<span>{t amount=$amount}Выбрано %amount [plural:%amount:элемент|элементa|элементов]{/t}</span>
<form method="post" action="{adminUrl}" class="crud-form">
    {foreach $ids as $id}
        <input type='hidden' name='ids[]' value='{$id}'>
    {/foreach}
    <input type='hidden' name='document_type' value='{$document_type}'>
    <div>
        {include file=$form_object.__exist->getRenderTemplate() field=$form_object.__exist}
    </div>
    <div>
        {include file=$form_object.__document_id->getRenderTemplate() field=$form_object.__document_id}
    </div>
</form>
