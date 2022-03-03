<input type="radio" name="type" value="manual" id="type_manual" {if $elem.type=='manual'}checked{/if}>
<label for="type_manual">{t}Задается вручную{/t}</label>         
{if $elem->mayBecomeAuto()}
    <br>
    <input type="radio" name="type" value="auto" id="type_auto" {if $elem.type=='auto'}checked{/if}>
    <label for="type_auto">{t}Вычисляется автоматически{/t}</label>
    <div style="padding:10px 0 0 25px" id="auto-input-block">
        {include file=$elem.__val_znak->getRenderTemplate() field=$elem.__val_znak}
        {include file=$elem.__val->getRenderTemplate() field=$elem.__val}
        {include file=$elem.__val_type->getRenderTemplate() field=$elem.__val_type}
        {t}от цены{/t}
        {$elem->excludeCostFromDepend($elem.id)}
        {include file=$elem.__depend->getRenderTemplate() field=$elem.__depend}

        <a class="help-icon" title="{$elem.__type->getHint()}">?</a>
    </div>
{/if}
<script>
$(function() {
    onChangeType = function() {
        if ( $("#type_auto:checked").length >0) {
            $('#auto-input-block input, #auto-input-block select').removeAttr('disabled');
        } else {
            $('#auto-input-block input, #auto-input-block select').attr('disabled', 'disabled');
        }
    }
    $("[name='type']").click(onChangeType);
    onChangeType();
});
</script>