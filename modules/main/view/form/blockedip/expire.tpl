<input type="checkbox" id="use-block-expire" {if $elem.expire}checked{/if}>
<span id="block-expire">{include file=$elem.__expire->getOriginalTemplate() field=$elem.__expire}</span>
<script>
    $(function() {
        $('#use-block-expire').change(function() {
            var context = $(this).parent();
            $('#block-expire', context).toggle($(this).is(':checked'));
            if (!$(this).is(':checked')) {
                $('input[name="expire"]', context).val('');
            }
        }).change();
    });
</script>