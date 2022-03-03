<div class="notice-box">
    {t}С помощью данных настроек можно контролировать поля, которые будут отображены пользователю при регистрации, а также поля, которые могут быть использованы в качестве логина при авторизации.{/t}
</div>
<table class="rs-table">
    <thead>
        <tr>
            <th>
                {t}Поле{/t}
            </th>
            <th>
                {t}Показывать{/t}
                <a style="color: #9E9E9E;" class="help-icon" data-placement="right" title="{t}Поля будут отображаться в формах, где предусмотрена регистрация пользователя{/t}">?</a>
            </th>
            <th>
                {t}Требовать{/t}
                <a style="color: #9E9E9E;" class="help-icon" data-placement="right" title="{t}Обязательные поля всегда включены для отображения{/t}">?</a>
            </th>
            <th>
                {t}Разрешить авторизацию{/t}
                <a style="color: #9E9E9E;" class="help-icon" data-placement="right" title="{t}Задает, какие поля можно указывать в качестве логина{/t}">?</a>
            </th>
        </tr>
    </thead>
    <tbody class="property-container">
        {$require_fields=$elem->getAuthRequireFields()}
        {$login_fields=$elem->getAuthLoginFields()}

        {foreach $elem->getAuthVisibleFields() as $field}
            <tr class="property-item">
                <td>
                    {$elem->getUserFieldName($field)}
                </td>
                <td>
                    <input type="checkbox" name="visible_fields[]" value="{$field}" {if $elem.visible_fields && in_array($field, $elem.visible_fields)}checked{/if}>
                </td>
                <td>
                    {if in_array($field, $require_fields)}
                        <input type="checkbox" name="require_fields[]" value="{$field}" {if $elem.require_fields && in_array($field, $elem.require_fields)}checked{/if}>
                    {/if}
                </td>
                <td class="auth-field">
                    {if in_array($field, $login_fields)}
                        <input type="checkbox" name="login_fields[]" value="{$field}" {if $elem.login_fields && in_array($field, $elem.login_fields)}checked{/if}>
                    {/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
<script>
    $('[name="require_fields[]"]').on('change', function () {
        var linkedInput = $('[name="visible_fields[]"][value="' + $(this).val() + '"]');
        if ($(this).prop('checked')) {
            linkedInput.prop('checked', true).attr('disabled', 'disabled');
        } else {
            linkedInput.removeAttr('disabled')
        }
    }).trigger('change')
</script>
<hr>
<table class="otable">
    <tr>
        <td class="otitle">{$elem.__user_one_fio_field->getDescription()}</td>
        <td>{include file=$elem.__user_one_fio_field->getRenderTemplate() field=$elem.__user_one_fio_field}</td>
    </tr>
</table>
