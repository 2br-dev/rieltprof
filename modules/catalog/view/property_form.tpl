<div class="property-form" style="display:none">
    <table width="100%" class="property-table">
            <tr class="p-proplist-block">
                <td class="key">{t}Выберите характеристику{/t}</td>
                <td><select class="p-proplist">
                        <option value="new">{t}Новая характеристика{/t}</option>
                    </select>
                    <span class="ploading"><img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif">{t}идет загрузка...{/t}</span>
                    <br>
                    <span class="fieldhelp">{t}Не рекомендуется создавать новую характеристику, если подобная уже имеется{/t}</span>
                </td>
            </tr>
            <tr class="p-group-block">
                <td class="key">{t}Группа{/t}</td>
                <td><select class="p-parent_id">
                        <option value="0">{t}Без группы{/t}</option>
                    </select><span class="makenew"> {t}или создать новую{/t} </span><input type="text" class="p-new-group">
                    
                    <br>
                    <span class="fieldhelp">{t}Тематическая группа характеристики{/t}</span>
                </td>
            </tr>
            <tr class="p-title-block">
                <td class="key">{t}Название{/t}</td>
                <td><input type="text" class="p-title"><div class="field-error top-corner" data-field="title"></div><br>
                    <span class="fieldhelp">{t}Будет отражаться в описани товара. Например: "диаметр соеденительной втулки"{/t}</span>
                </td>
            </tr>
            <tr class="p-type-block">
                <td class="key">{t}Тип{/t}</td>
                <td><select class="p-type">
                    {foreach $value_types as $key => $type}
                        <option value="{$key}" data-is-list="{$type.is_list|string_format:"%d"}">{$type.title}</option>
                    {/foreach}
                </select></td>
            </tr>
            <tr>
                <td class="key">{t}Единица измерения{/t}</td>
                <td><input type="text" class="p-unit" style="width:100px"><br>
                    <span class="fieldhelp">{t}Будет отображаться после значения (до 50 знаков){/t}</span>
                </td>
            </tr>
            <tr class="p-value-block">
                <td class="key">{t}Значение{/t}</td>
                <td>
                    <span class="p-val-block">
                        <input type="text" class="p-val">
                    </span>
                </td>
            </tr>
            <tr class="p-new-value-block">
                <td class="key">{t}Добавить значение{/t}</td>
                <td>
                    <div class="p-list-actions">
                        <input type="text" class="p-new-value" placeholder="{t}новое значение{/t}">
                        <a class="p-add-new-value">{t}добавить{/t}</a>
                        <br><span class="fieldhelp">{t}Полное редактирование всех свойств значений доступно в разделе {/t}<a href="{$router->getAdminUrl(false, [], 'catalog-propctrl')}" target="_blank">{t}Характеристики{/t}</a></span>
                    </div>
                </td>
            </tr>
            
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-success add disabled" data-add-text="{t}Добавить{/t}" data-edit-text="{t}Сохранить{/t}">{t}Добавить{/t}</a>
                    <a class="close">{t}свернуть{/t}</a>
                </td>
            </tr>
        </table>
</div>
<div class="some-property-form" style="display:none">
    <table width="100%" class="property-table some-property-table">
        <tr>
            <td class="key">{t}Выберите характеристики{/t} 
            <div class="fieldhelp">{t}Удерживая CTRL можно выбрать несколько характеристик{/t}</div></td>
            <td><select class="some-props" size="20" style="width:300px; height:250px;" multiple="multiple" disabled="disabled">
                <option>{t}Идет загрузка...{/t}</option>
            </select></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a class="btn btn-success add-some disabled">{t}Добавить{/t}</a>
                <a class="close">{t}свернуть{/t}</a>
            </td>
        </tr>
    </table>
</div>