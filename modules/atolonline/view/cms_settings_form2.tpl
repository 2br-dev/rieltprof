<div class="formbox" >
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
        <input type="hidden" name="step" value="2">
        <input type="submit" value="" style="display:none">
        <div class="notabs">
            <table class="otable">
                <tr>
                    <td class="otitle">{t}Выберите идентификатор настроек{/t}
                    </td>
                    <td>
                        <select name="shop">
                            {foreach $shops as $key => $shop}
                                <option value="{$key}">{$shop}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>