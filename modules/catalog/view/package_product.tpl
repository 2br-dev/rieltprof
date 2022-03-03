<table class="package-container">
    <tbody class="overable">
        {foreach from=$elem->parsePackage() item=package}
        <tr class="package-item">
            <td class="sort-td"><div class="sort"></div></td>
            <td class="title-td">
                <input type="hidden" value="{$package.title}" name="other[package][{$package.title}][title]" class="h-title">
                <input type="hidden" value="{$package.type}" name="other[package][{$package.title}][type]" class="h-type">
                {$package.title}
            </td>
            <td>{t}Тип:{/t}{if $package.type == 'list'}{t}Список{/t}{else}{t}Строка{/t}{/if}</td>
            <td>
                {if !empty($package.vals_parsed)}
                <table class="vtable">
                    <tbody>
                        {foreach from=$package.vals_parsed item=val}
                        <tr>
                            <td>
                            <input type="hidden" value="{$val.inline|escape}" name="other[package][{$package.title}][vals][]" class="h-val">
                            {$val.title}({$val.code})</td>
                            <td class="c2">{$val.znak}{$val.value} {$val.type}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>        
                {/if}
            </td>
            <td class="item-tools">
                <a class="edit"></a>
                <a class="delete">{t}удалить{/t}</a>                            
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>            