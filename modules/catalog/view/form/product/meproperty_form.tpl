<div class="prop-insert-div">
</div>

<div class="property-form clone-it" style="display:none">
    <span class="field-error" data-field="title"></span>
    <input type="hidden" class="p-siteid"/>
    <input type="hidden" class="p-type"/>
    <input type="hidden" class="p-public"/>
    <input type="hidden" class="p-xmlid"/>
    <table width="100%" class="property-table">
            <tr class="p-proplist-block">
                <td class="key">{t}Выберите характеристику{/t}</td>
                <td>
                    <select class="p-proplist">
                       <option>{t}Идет загрузка...{/t}</option>
                    </select>
                    <span class="ploading"><img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif">{t}идет загрузка...{/t}</span>
                </td>
            </tr>                            
            <tr class="p-row-list-delit" style="display:none;">
                <td class="key">{t}Удалить?{/t}</td>
                <td>
                    <span>
                        <input type="checkbox" class="p-check-list-delit" value="1"/>
                    </span><br>
                    <span class="fieldhelp">{t}Удалить эту характеристику у товаров{/t}</span>
                </td>
            </tr>    
            <tr class="p-row-list-savelink" style="display:none;">
                <td class="key">{t}Сохранить связи?{/t}</td>
                <td>
                    <span>
                        <input type="checkbox" class="p-check-list-save-link" value="1"/>
                    </span><br>
                    <span class="fieldhelp">{t}Сохранить связь с ранее установленными значениями{/t}</span>
                </td>
            </tr>          
            <tr class="p-value-block" style="display:none;">
                <td class="key">{t}Значение{/t}</td>
                <td>
                    <span class="p-val-block">
                        <input type="text" class="p-val">
                    </span>
                </td>
            </tr>  
            <tr class="p-row-list-values" style="display:none;">
                <td class="key">{t}Список значений{/t}</td>
                <td>
                    <span class="p-list-check">
                        
                    </span><br>
                </td>
            </tr> 
    </table>
    <div class="oh">
        <a class="close-property">{t}Убрать строку{/t} <i class="zmdi zmdi-long-arrow-up"></i></a>
    </div>
</div>

<div class="multi-prop-del-button">
    <input type="checkbox" value="1" name="_property_[0]"/ id="delAllProperty"> - <label for="delAllProperty">{t}удалить все характеристики выбранных товаров?{/t}</label>
</div>