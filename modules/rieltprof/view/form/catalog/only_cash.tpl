<input type="checkbox" name="only_cash" value="1" title="{t}Только наличка{/t}" id="setban" {if $elem.only_cash}checked{/if}>
</td>
</tr>
<tr class="ban-reason">
    <td class="otitle"><p>{t}Ипотеку рассматриваем{/t}</p></td>
    <td>{include file=$elem.__mortgage->getOriginalTemplate() field=$elem.__mortgage}</td>
</tr>

<script>
    $(function() {
        $('#setban').change(function() {
            if ($(this).is(':checked')) {
                $('.ban-reason').css('display', 'none');
            } else {
                $('.ban-reason').css('display', 'table-row');
            }
        }).change();
        $('.ban-reason input').change(function(){
            if($(this).is(':checked')){
                // $('[name="mortgage"]').val(0);
                $('#setban').closest('tr').css('display', 'none');
            }else {
                $('#setban').closest('tr').css('display', 'table-row');
            }
        }).change();
    });
</script>
