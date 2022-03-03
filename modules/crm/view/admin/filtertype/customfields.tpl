{$values = $fitem->getValue()}
{foreach $fitem->getUserFieldsManager()->getStructure() as $key => $field}
    {if $field.type == 'string' || $field.type == 'text'}
        <div class="form-group">
            <label class="standartkey">{$field.title}</label><br>
            <input type="text" name="{$fitem->getName()}[{$key}]" value="{$values[$key]}">
        </div>

    {elseif $field.type == 'list'}
        <div class="form-group">
            <label class="standartkey">{$field.title}</label><br>
            <select name="{$fitem->getName()}[{$key}]">
                <option value="">{t}- Не выбрано -{/t}</option>
                {foreach $fitem->getUserFieldsManager()->parseValueList($field.values) as $item}
                    <option value="{$item}" {if $values[$key] == $item}selected{/if}>{$item}</option>
                {/foreach}
            </select>
        </div>

    {elseif $field.type == 'bool'}
        <div class="form-group">
            <label class="standartkey">{$field.title}</label><br>
            <select name="{$fitem->getName()}[{$key}]">
                <option value="" {if $values[$key] == ""}selected{/if}>{t}- Не выбрано -{/t}</option>
                <option value="1" {if $values[$key] == 1}selected{/if}>{t}Да{/t}</option>
            </select>
        </div>
    {/if}
{/foreach}