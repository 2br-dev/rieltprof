{* редактор ключ => значение *}
{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="jquery.rs.keyvaleditor.js" basepath="common"}
<div class="keyval-container" data-var="{$field_name}">
    <table class="keyvalTable{if empty($arr)} hidden{/if}">
        <thead>
            <tr>
                <th width="20"></th>
                <th class="kv-head-key">{t}Параметр{/t}</th>
                <th class="kv-head-val">{t}Значение{/t}</th>
                <th width="20"></th>
            </tr>
        </thead>
        <tbody>
            {if is_array($arr)}
            {foreach $arr as $prop_key => $prop_val}
            <tr>
                <td class="kv-sort">
                    <div class="ksort">
                        <i class="zmdi zmdi-unfold-more"></i>
                    </div>
                </td>
                <td class="kv-key"><input type="text" name="{$field_name}[key][]" value="{$prop_key}"></td>
                <td class="kv-val"><input type="text" name="{$field_name}[val][]" value="{$prop_val}"></td>
                <td class="kv-del"><a class="remove zmdi zmdi-delete"></a></td>
            </tr>
            {/foreach}
            {/if}
        </tbody>
    </table>
    <a class="btn btn-default add-pair va-m-c">
        <i class="zmdi zmdi-plus"></i>
        <span>{$add_button_text|default:"{t}Добавить{/t}"}</span>
    </a>
</div>