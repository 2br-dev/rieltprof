<div class="all" style="float: right;">
    <a title="{t}Нажмите, чтобы включить все привелегии модуля{/t}"  style="text-decoration:underline" onclick="$(this).nextAll('input:checkbox').prop('checked', true).trigger('change')" >Максимум</a>
    {$all = 7}
    {for $num = 0 to $all}
    <input type="checkbox" class="check_all" value="{$all - $num}" id="full_module">
    {/for}
    <a title="{t}Нажмите, чтобы отключить привилегии модуля{/t}"  style="text-decoration:underline" onclick="$(this).prevAll('input:checkbox').prop('checked', false).trigger('change')">Нет прав</a>
</div>