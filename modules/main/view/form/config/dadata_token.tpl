{include file=$elem.__dadata_token->getOriginalTemplate() field=$elem.__dadata_token}
<script>
    $(function() {
        var token_tr = $('input[name="dadata_token"]').closest('tr');
        $('select[name="geo_ip_service"]').change(function() {
            token_tr.toggle( $(this).val() == 'dadata' );
        }).change();
    });
</script>