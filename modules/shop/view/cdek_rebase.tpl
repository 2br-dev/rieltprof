<div class="formbox">
    <form method="POST" action="{$router->getAdminUrl('RebaseCdekFile', [])}" enctype="multipart/form-data" class="tab-content crud-form">
        <input type="submit" value="" style="display:none"/>
        <div class="tab-pane active" id="catalog-product-tab0" role="tabpanel">
            <table class="otable">
                <tr>
                    <span class="text-warning">Актуализация некоторых стран может занять больше 1 минуты!</span>
                </tr>
                <tr>
                    <td class="otitle">
                        {t}Страна{/t}
                    </td>
                    <td>
                        <select name="country">
                            {foreach $countries as $key => $country_name}
                                <option value="{$key}">{$country_name}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
