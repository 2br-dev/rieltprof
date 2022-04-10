<input type="checkbox" name="exclusive" value="1" title="{t}Эксклюзив{/t}" id="exclusive" {if $elem.exclusive}checked{/if}>
</td>
</tr>
<tr class="advertise">
    <td class="otitle"><p>{t}От себя рекламирую в интернете{/t}</p></td>
    <td>{include file=$elem.__advertise->getOriginalTemplate() field=$elem.__advertise}</td>
</tr>

<script>
    $(function() {
        $('body #exclusive').change(function() {
            if ($(this).is(':checked')) {
                $('.advertise').css('display', 'none');
            } else {
                $('.advertise').css('display', 'table-row');
            }
        }).change();
        $('body .advertise input').change(function(){
            if($(this).is(':checked')){
                // $('[name="mortgage"]').val(0);
                $('body #exclusive').closest('tr').css('display', 'none');
            }else {
                $('body #exclusive').closest('tr').css('display', 'table-row');
            }
        }).change();
    });
</script>
