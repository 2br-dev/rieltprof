<div id="meconcomitant">
    <table class="otable">                                              
        <tr class="editrow">
            <td class="ochk" width="20">
                <input id="me-product-concomitant" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__concomitant_arr->getName()}" {if in_array($elem.__concomitant_arr->getName(), $param.doedit)}checked{/if}></td>
            <td class="otitle"><label for="me-product-concomitant">{t}Изменить сопутствующие товары{/t}</label></td>
            <td>
                <div class="multi_edit_rightcol coveron">
                    <div class="cover"></div>
                    {$elem->getProductsDialogConcomitant()->getHtml()}
                    <div class="multi-prop-del-button">
                        <input type="checkbox" value="0" name="concomitant_arr[product][0]"> - <label for="delAllProperty">{t}удалить все сопутствующие товары?{/t}</label>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>