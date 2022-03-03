<div class="import-csv">
    <form class="crud-form" method="POST" action="{adminUrl schema=$schema referer=$referer}" enctype="multipart/form-data" data-dialog-options='{ "width":"500", "height":"300" }'>
        <table class="otable">
            <tr>
                <td class="otitle">{t}CSV Файл{/t}</td>
                <td>{include file="%system%/admin/fileinput.tpl" form_name="csvfile"}</td>
            </tr>
        </table>
    </form>
</div>