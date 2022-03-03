<div class="formbox">
    <form method="POST" action="{$router->getAdminUrl('CreateLangFilesDialog', [])}" enctype="multipart/form-data" class="tab-content crud-form">
        <input type="submit" value="" style="display:none"/>
        <div class="tab-pane active" id="catalog-product-tab0" role="tabpanel">
            <table class="otable">
                <tr>
                    <td class="otitle">
                        {t}ID языка{/t}
                    </td>
                    <td>
                        <input name="lang" placeholder="en" value="en">
                    </td>
                </tr>
                <tr>
                    <td class="otitle">
                        {t}Модуль{/t}
                    </td>
                    <td>
                        <select name="module">
                            {foreach $modules as $key => $module_name}
                                <option value="{$key}">{$module_name}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>