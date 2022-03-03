{foreach $field->getList() as $key => $item}
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
        //Скрываем поля, которые не связаны с выбранным типом оформления заказа
        $('[name="{$field->getFormName()}"]').change(function() {
            var orderType = $(this).val();
            $('[data-order-type]').each(function() {
                $(this).closest('tr').toggle(
                    $(this).data('order-type').split(';').indexOf(orderType) > -1
                );
            });
        }).change();
    });
</script>