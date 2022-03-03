{include file=$elem.__smtp_is_use->getOriginalTemplate() field=$elem.__smtp_is_use}
<script>
    $(function() {       
        $('[name="smtp_is_use"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name="smtp_host"], [name="smtp_port"], [name="smtp_secure"], [name="smtp_auth"], [name="smtp_username"], [name="smtp_password"]').each(function() {
                $(this).closest('tr').toggle(enable);
            });
        }).change();
        
        $('[name="smtp_auth"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name="smtp_username"], [name="smtp_password"]').prop('disabled', !enable);
        }).change();
    });
</script>