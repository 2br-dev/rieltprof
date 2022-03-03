<input type="hidden" name="tax_ids" value="{$elem.tax_ids}">
    <input type="checkbox" value="all" class="tax_items" id="tax_all" {if $elem.tax_ids == 'all'}checked{/if}> 
    <label for="tax_all">{t}Все налоги{/t}</label><br>
{assign var=checked_taxes value=explode(",", $elem.tax_ids)}
{foreach from=$field->getList() key=key item=tax}
    <input type="checkbox" value="{$key}" class="tax_items tax_items_other" id="tax_{$key}" {if in_array($key, $checked_taxes)}checked{/if}> 
    <label for="tax_{$key}">{$tax}</label><br>
{/foreach}

<script>
$(function() {
    var checkTax = function() {
        if (this.checked) {
            $('.tax_items_other').prop('checked', false).prop('disabled', true);
        } else {
            $('.tax_items_other').prop('disabled', false);
        }
    }
    $('#tax_all').change(checkTax).change();    
    
    
    $('.tax_items').change(function() {
        var value = [];
        $('.tax_items:checked').each(function() {
            value.push($(this).val());
        });
        $('input[name="tax_ids"]').val(value.join(','));
    });
})
</script>