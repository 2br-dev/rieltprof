<div class="formbox" >
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
        <input type="hidden" name="step" value="1">
        <input type="submit" value="" style="display:none">
        <div class="notabs">
            <table class="otable">
                <tr>
                    <td class="otitle">{t}XML файл с настройками{/t}<a class="help-icon" title="{t}Выдается в личном кабинете АТОЛ{/t}">?</a>
                    </td>
                    <td>
                        {include file="%system%/admin/fileinput.tpl" form_name="file"}
                    </td>
                </tr>

            </table>
        </div>
    </form>
</div>