{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addjs file="tmpl/tmpl.min.js" basepath="common"}
{addjs file="jquery.rs.userfields.js" basepath="common"}

<div class="userfields-container" data-key="{$manager->getField()}">
    {if $before_phrase}
        <div class="notice-box">{$before_phrase}</div>
    {/if}
    {$key=$manager->getField()}
    {$type_list=['string' => t('Строка'), 'list' => t('Список'), 'bool' => t('Да/Нет')]}

    <div class="table-mobile-wrapper">
        <table class="rs-table">
        <thead>
            <tr>
                <th class="chk"><div class="chkhead-block">
                        <input type="checkbox" data-name="chk[]" class="chk_head select-page" alt="">
                    </div>
                </th>
                <th width="20"></th>
                <th>{t}Название{/t}</th>
                <th>{t}Тип{/t}</th>
                <th>{t}Макс. кол-во символов{/t}</th>
                <th>{t}Обязательное{/t}</th>
                <th>{t}Значение по умолчанию{/t}</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="property-container">
            {foreach from=$manager->getStructure() item=item}
            <tr class="property-item">
                <td class="chk">
                    <input type="checkbox" value="{$item.alias}" name="chk[]">
                    <input value="{$item.alias}" name="{$key}[{$item.alias}][alias]" class="h-alias" type="hidden">
                    <input value="{$item.maxlength}" name="{$key}[{$item.alias}][maxlength]" class="h-maxlength" type="hidden">
                    <input value="{$item.necessary}" name="{$key}[{$item.alias}][necessary]" class="h-necessary" type="hidden">
                    <input value="{$item.title}" name="{$key}[{$item.alias}][title]" class="h-title" type="hidden">
                    <input value="{$item.type}" name="{$key}[{$item.alias}][type]" class="h-type" type="hidden">
                    <input value="{$item.values}" name="{$key}[{$item.alias}][values]" class="h-values" type="hidden">
                    <input value="{$item.val}" name="{$key}[{$item.alias}][val]" class="h-val" type="hidden">
                </td>
                <td class="dh p-drag-handle">
                    <a data-sortid="165" class="sort p-dndsort"></a>
                </td>
                <td>
                    {$item.title}
                </td>
                <td>{$type_list[$item.type]}</td>
                <td>{$item.maxlength}</td>
                <td>{if !empty($item.necessary)}{t}Да{/t}{else}{t}Нет{/t}{/if}</td>
                <td>{$item.val}</td>
                <td>
                    <div class="tools">
                        <a class="zmdi zmdi-edit edit"></a>
                    </div>
                </td>
            </tr>
            {/foreach}        
        </tbody>
        <tbody>
            <tr class="norecord" {if $manager->getStructure()}style="display:none"{/if}>
                <td align="center" colspan="8"> {t}Нет элементов{/t}</td>
            </tr>
        </tbody>
    </table>
    </div>

    <div class="rs-under-tools">
        <a class="item add va-m-c">
            <i class="zmdi zmdi-plus f-19"></i>&nbsp;
            <span>{t}Добавить поле{/t}</span>
        </a>
        <a class="item del va-m-c c-red">
            <i class="zmdi zmdi-delete f-19"></i>&nbsp;
            <span>{t}Удалить{/t}<span>
        </a>
    </div>

    <div id="userfield-dialog" style="display:none">
        <table width="99%" class="otable">
            <tr>
                <td colspan="2" class="p-result"></td>
            </tr>
            <tr class="p-proplist-block">
                <td class="otitle">{t}Идентификатор{/t}
                <div class="fieldhelp">{t}допустимы только английские буквы, символ "подчеркивание"<br>Например: "years"{/t}
                </div>
                </td>
                <td><input type="text" class="p-alias"></td>
            </tr>
            <tr>
                <td width="220" class="otitle">{t}Название поля{/t}
                <div class="fieldhelp">{t}Например: "возраст"{/t}</div>
                </td>
                <td><input type="text" class="p-title"></td>
            </tr>
            <tr>
                <td class="otitle">{t}Тип{/t}</td>
                <td><select name="p-type" class="p-type">
                    <option value="string">{t}Строка{/t}</option>
                    <option value="text">{t}Текст{/t}</option>
                    <option value="list">{t}Список{/t}</option>
                    <option value="bool">{t}Да/Нет{/t}</option>
                </select></td>
            </tr>
            <tr class="p-values-block" style="display:none">
                <td class="otitle">{t}Возможные значения{/t}
                <div class="fieldhelp">{t}Укажите через запятую значения для списка<br>Например: "10 лет, 20 лет, 30 лет"{/t}</div>
                </td>
                <td><input type="text" class="p-values"></td>
            </tr>            
            <tr>
                <td class="otitle">{t}Значение по умолчанию{/t}
                <div class="fieldhelp">{t}Будет присвоено товарам по умолчанию{/t}</div>
                </td>
                <td><input type="text" class="p-val"></td>
            </tr>        
            <tr>
                <td width="220" class="otitle">{t}Максимальное число символов{/t}
                <div class="fieldhelp">{t}Например: "255"{/t}</div>
                </td>
                <td><input type="text" class="p-maxlength"></td>
            </tr>
            <tr>
                <td width="220" class="otitle">{t}Обязательное{/t}</td>
                <td><input type="checkbox" class="p-necessary" value="1"></td>
            </tr>                        
        </table>
    </div>
    
    {strip}{literal}    
    <script type="text/x-tmpl" id="userfield-line">
        <tr class="property-item">
            <td class="chk">
                <input type="checkbox" value="{%=o.alias%}" name="chk[]">
                <input value="{%=o.alias%}" name="{%=o.key%}[{%=o.alias%}][alias]" class="h-alias" type="hidden">
                <input value="{%=o.maxlength%}" name="{%=o.key%}[{%=o.alias%}][maxlength]" class="h-maxlength" type="hidden">
                <input value="{%=o.necessary%}" name="{%=o.key%}[{%=o.alias%}][necessary]" class="h-necessary" type="hidden" {% if (o.necessary){ %}checked{% } %}>
                <input value="{%=o.title%}" name="{%=o.key%}[{%=o.alias%}][title]" class="h-title" type="hidden">
                <input value="{%=o.type%}" name="{%=o.key%}[{%=o.alias%}][type]" class="h-type" type="hidden">
                <input value="{%=o.values%}" name="{%=o.key%}[{%=o.alias%}][values]" class="h-values" type="hidden">
                <input value="{%=o.val%}" name="{%=o.key%}[{%=o.alias%}][val]" class="h-val" type="hidden">
            </td>
            <td class="drag-handle">
                <a data-sortid="{%=o.alias%}" class="sort dndsort"></a>
            </td>
            <td>
                {%=o.title%}
            </td>
            <td>{%=o.type_str%}</td>
            <td>{%=o.maxlength%}</td>
            <td>{%=o.necessary_str%}</td>
            <td>{%=o.val_str%}</td>
            <td>
                <div class="tools">
                    <a class="zmdi zmdi-edit edit"></a>
                </div>
            </td>
        </tr>    
    </script>
    {/literal}{/strip}
</div>