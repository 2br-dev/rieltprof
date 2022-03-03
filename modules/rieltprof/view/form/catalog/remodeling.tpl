<input type="checkbox" name="remodeling" value="1" title="Перепланировка" id="remodeling" {if $elem.remodeling}checked{/if}>
</td>
</tr>
<tr class="remodeling_legalized">
    <td class="otitle"><p>{t}Перепланировка узаконена?{/t}</p></td>
    <td>{include file=$elem.__remodeling_legalized->getOriginalTemplate() field=$elem.__remodeling_legalized}</td>
</tr>

<script>
    $(function() {
        $('body #remodeling').change(function() {
            // var context = $(this).closest('td');
            if ($(this).is(':checked')) {
                $('.remodeling_legalized').css({
                    'display': 'table-row'
                });
            } else {
                $('.remodeling_legalized').css({
                    'display': 'none'
                });
            }
        }).change();
    });
</script>
