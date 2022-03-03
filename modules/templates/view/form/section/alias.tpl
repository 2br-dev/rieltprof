<select name="alias">
    {foreach from=$elem.__alias->getList() key=key item=val}
    <option value="{$key}" {if  $elem.alias == $key || ($key == '_other' && !in_array($elem.alias, array_keys($elem.__alias->getList())))}selected{/if}>{$val}</option>
    {/foreach}
</select>

<input type="text" name="alias" value="{$elem.alias}" {if $elem.alias != '_other' && ($elem.alias == '' || in_array($elem.alias, array_keys($elem.__alias->getList())))}disabled style="display:none"{/if}>
<script>
$(function() {
    var change = function() {
        var verdict = $('select[name="alias"]').val() != '_other';
        $('input[name="alias"]').prop('disabled', verdict).toggle(!verdict);
    }
    $('select[name="alias"]').change(change).change();
});
</script>