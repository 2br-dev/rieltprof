<input type="checkbox" name="encumbrance" value="1" title="{t}Ограничение банка{/t}" id="encumbrance" {if $elem.encumbrance}checked{/if}>
</td>
</tr>
<tr class="encumbrance_notice">
    <td class="otitle"><p>{t}Банк, Сумма{/t}</p></td>
    <td>{include file=$elem.__encumbrance_notice->getOriginalTemplate() field=$elem.__encumbrance_notice}</td>
</tr>

<script>
    $(function() {
        $('#encumbrance').change(function() {
            // var context = $(this).closest('td');
            if ($(this).is(':checked')) {
                $('.encumbrance_notice').css('display', 'table-row');
            } else {
                $('[name="encumbrance_notice"]').val('');
                $('.encumbrance_notice').css('display', 'none');
            }
        }).change();
    });
</script>
