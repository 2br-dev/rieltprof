{if !$mod_validate}
    <div class="error-list no-bottom-border">
        <p>
            {t}Продолжение установки невозможно, у загруженного модуля имеются следующие ошибки:{/t}
        </p>
        <ul class="p-0">
            {foreach from=$mod_errors item=item name=err}
            <li>
                <div class="field"><span class="module-title">{$smarty.foreach.err.iteration}</span><i class="cor"></i></div>
                <div class="text">{$item}</div>
            </li>
            {/foreach}
        </ul>
    </div>
{else}
    {if $mod_info.already_exists}
    <div class="notice notice-warning">
        {t}Такой модуль уже присутствует в системе. Продолжение установки приведет к обновлению модуля{/t}
    </div>
    {/if}

    <h3>{t}Информация о модуле:{/t}</h3>
    <table class="table">
        <tr>
            <td class="ctitle"><span>{t}Название модуля:{/t}</span></td>
            <td>{$mod_info.info.name}</td>
        </tr>
        <tr>
            <td class="ctitle"><span>{t}Описание модуля:{/t}</span></td>
            <td>{$mod_info.info.description}</td>
        </tr>
        <tr>
            <td class="ctitle"><span>Автор:</span></td>
            <td>{$mod_info.info.author}</td>
        </tr>
        <tr>
            <td class="ctitle"><span>Версия модуля:</span></td>
            <td>{$mod_info.info.version}</td>
        </tr>
        {if $mod_info.already_exists}
        <tr>
            <td class="ctitle"><span>Текущая версия модуля:</span></td>
            <td>{$mod_info.current_version}</td>
        </tr>
        {/if}
        <tr>
            <td colspan="2" class="last"></td>
        </tr>
    </table>

    <form method="POST" class="crud-form">
        {if !$mod_info.already_exists && $mod_info.can_insert_demo_data}
        <h3>{t}Параметры установки{/t}</h3>
        <input type="checkbox" name="insertDemoData" value="1" id="install_demo">&nbsp;
        <label for="install_demo">{t}Установить демонстрационные данные{/t}</label>
        {/if}
    </form>

    {if $mod_info.changelog}
        <h3>{t}История изменений:{/t}</h3>
        <textarea readonly="readonly" class="w-100" rows="16">{$mod_info.changelog|escape}</textarea>
    {/if}
{/if}