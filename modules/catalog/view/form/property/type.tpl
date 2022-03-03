<select name="type" {if $elem.id>0}data-old-value="{$elem.type}"{/if} id="property-type">
    {foreach $elem->getAllowTypeData() as $key => $data}
    <option value="{$key}" {if $elem.type == $key}selected{/if} data-is-list="{$data.is_list|string_format:"%d"}">{$data.title}</option>
    {/foreach}
</select>
<div class="inform-block hidden" style="margin-top:10px" id="property-changed-type">
    {t}Тип характеристики изменен. Сохраните изменения и продолжите <br> редактирование характеристики. После сохранения изменений будет<br> произведена конвертация значений, привязанных к данной характеристике и товарам.{/t}
</div>
<script>
    $(function() {
        $('#property-type[data-old-value]').change(function() {
            var changed = $(this).data('oldValue') != $(this).val();
            $('#property-changed-type').toggleClass('hidden', !changed);
            
            if ($(this).val()=='int'){
                $('[name="int_hide_inputs"]').closest('tr').show();
            } else {
                $('[name="int_hide_inputs"]').closest('tr').hide();
            }
        });
        
        $('#property-type[data-old-value]').change();
    });
</script>