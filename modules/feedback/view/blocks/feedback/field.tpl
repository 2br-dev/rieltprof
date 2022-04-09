{if $request}
    {if $field.show_type=='list'}
        {$postValue = $request->request($field.alias,'')}
    {else}
        {$postValue = $request->request($field.alias,'string')}
    {/if}
{/if}

{if $field.show_type=='string' || $field.show_type=='email'}
    {$field->setCustomClass('form-control')}
    <input type="text" placeholder="{$field.hint}" name="{$field.alias}" {if $field.length>0}maxlength="{$field.length}"{/if}
           value="{if $postValue}{$postValue}{else}{$field->getDefault()}{/if}" {$field->getAttrLine($attr)}/>
{elseif $field.show_type=='list'}
    {$valList=$field->getArrayValuesFromString()}

    {if $valList}
        {if $field.show_list_as == 'radio'}
            {foreach from=$valList item=val key=k}
                {$field->setCustomClass('form-check-input')}
                <div class="form-check">
                    <input placeholder="{$field.hint}" id="vlr_{$key}_{$k}" {if $postValue==$val}checked="checked"{/if}
                            {$field->getAttrLine($attr)} type="radio" name="{$field.alias}"
                           value="{$val}"/>
                    <label for="vlr_{$key}_{$k}" class="form-check-label">{$val}</label>
                </div>
            {/foreach}
        {elseif $field.show_list_as == 'checkbox'}
            {$field->setCustomClass('form-check-input')}
            {foreach from=$valList item=val key=k}
                <div class="form-check">

                    <input placeholder="{$field.hint}" id="vlr_{$key}_{$k}"
                           {if is_array($postValue) && in_array($val, $postValue)}checked="checked"{/if}
                            {$field->getAttrLine($attr)}
                           type="checkbox" name="{$field.alias}[]" value="{$val}"/>
                    <label for="vlr_{$key}_{$k}" class="form-check-label">{$val}</label>
                </div>
            {/foreach}
        {else}
            {$field->setCustomClass('form-select')}
            <select name="{$field.alias}" {$field->getAttrLine($attr)}>
                {foreach from=$valList item=val}
                    <option value="{$val}" {if $postValue==$val}selected="selected"{/if}>{$val}</option>
                {/foreach}
            </select>
        {/if}
    {else}
        {t}Значения списка не заданы{/t}
    {/if}

{elseif $field.show_type=='yesno'}
    {$field->setCustomClass('form-select')}
    <select name="{$field.alias}" {$field->getAttrLine($attr)}>
        <option value="{t}Да{/t}" {if $postValue==t('Да')}selected="selected"{/if}>{t}Да{/t}</option>
        <option value="{t}Нет{/t}" {if $postValue==t('Нет')}selected="selected"{/if}>{t}Нет{/t}</option>
    </select>
{elseif $field.show_type=='text'}
    {$field->setCustomClass('form-control')}
    <textarea name="{$field.alias}" {$field->getAttrLine($attr)}>{if $postValue}{$postValue}{else}{$field->getDefault()}{/if}</textarea>
{elseif $field.show_type=='file'}
    {$field->setCustomClass('form-control')}
    <input placeholder="{$field.hint}" type="file" name="{$field.alias}" {$field->getAttrLine($attr)}/>
{elseif $field.show_type=='captcha'}
    <div class="captcha">
        {$captcha->getView($field.alias, "form_{$field.form_id}")}
    </div>
{/if}