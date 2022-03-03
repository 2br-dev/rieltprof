<div class="formbox" data-dialog-options='{ "width":"700px" }'>
    <p class="inform-block">{t alias="Используйте инструмент клонирования для создания собственных тем.."}Используйте инструмент клонирования для создания собственных тем оформления на основе других.
    Созданные таким образом темы оформления не изменяются во время обновления системы.{/t}</p>
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
        <div class="notabs">
            <table class="otable no-td-width" width="100%">
            <tr>
                <td class="otitle" style="width:150px">{t}Исходная тема{/t}</td>
                <td><select name="source_theme">
                    {foreach from=$root_sections.themes key=key item=item}
                        <option value="{$key}" {if $elem.source_theme == $key}selected{/if}>{$item.title}</option>
                    {/foreach}
                </select></td>
            </tr>
            <tr>
                <td class="otitle">{t}Новый идентификатор (Англ. яз){/t}</td>
                <td><input type="text" name="new_name" size="30" maxlength="25" value="{$elem.new_name}">
                    {$errors=$clone_api->getFormErrors('new_name')}
                    <div class="field-error top-corner" data-field="new_name">{if $errors}<span class="text"><i class="cor"></i>{$errors}</span>{/if}</div>
                </td>
            </tr>
            <tr>
                <td class="otitle">{t}Новое название темы{/t}</td>
                <td><input type="text" name="new_title" size="50" maxlength="50" value="{$elem.new_name}">
                    {$errors=$clone_api->getFormErrors('new_title')}
                    <div class="field-error top-corner" data-field="new_title">{if $errors}<span class="text"><i class="cor"></i>{$errors}</span>{/if}</div>
                </td>
            </tr>
            <tr>
                <td class="otitle">{t}Автор новой темы{/t}</td>
                <td><input type="text" name="new_author" size="50" maxlength="50" value="{$elem.new_author}">
                    {$errors=$clone_api->getFormErrors('new_author')}
                    <div class="field-error top-corner" data-field="new_author">{if $errors}<span class="text"><i class="cor"></i>{$errors}</span>{/if}</div>
                </td>
            </tr>
            <tr>
                <td class="otitle">{t}Описание новой темы{/t}</td>
                <td><textarea cols="50" rows="5" name="new_descr">{$elem.new_descr}</textarea>
                    {$errors=$clone_api->getFormErrors('new_descr')}
                    <div class="field-error top-corner" data-field="new_descr">{if $errors}<span class="text"><i class="cor"></i>{$errors}</span>{/if}</div>
                </td>
            </tr>            
            <tr>
                <td class="otitle">{t}Переключиться на новую тему оформления{/t}</td>
                <td><input type="checkbox" name="set_theme" {if $elem.set_theme}checked{/if}></td>
            </tr>
            </table>
        </div>
    </form>
</div>