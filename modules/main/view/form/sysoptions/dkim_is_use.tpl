{include file=$elem.__dkim_is_use->getOriginalTemplate() field=$elem.__dkim_is_use}
<script>
    $(function() {
        $('[name="dkim_is_use"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name*="dkim_"]:not("[name=dkim_is_use]")').each(function() {
                $(this).closest('tr').toggle(enable);
            });
        }).change();
    });
</script>