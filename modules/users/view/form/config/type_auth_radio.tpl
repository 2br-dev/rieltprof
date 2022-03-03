{foreach from=$field->getList() item=item key=key}
    <label class="radio-item" style="display: flex; align-items: flex-start; margin-bottom: 16px;">
        <input class="m-r-10" name="{$field->getFormName()}" type="radio" value="{$key}" {if in_array($key, (array)$field->get())}checked="checked"{/if} {$field->getAttr()}>
        <div style="width: 100%;">
            {$item.title}
            <br>
            <div class="m-t-10 c-gray">
                {$item.description}
            </div>
        </div>
    </label>
{/foreach}

<script>
    $(function() {
        $('[name="{$field->getFormName()}"]').on('change', function (e) {
            $('[2auth], [phoneauth]').parents('tr').hide();

            if ($(this).val() == 1) {
                $('[2auth]').parents('tr').show();
            } else if ($(this).val() == 2) {
                $('[phoneauth]').parents('tr').show();
            }
        }).filter(':checked').trigger('change');
    });
</script>