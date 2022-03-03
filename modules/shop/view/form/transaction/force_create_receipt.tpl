{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        $('[name="force_create_receipt"]').change(function() {
            $('[name="receipt_payment_subject"]').closest('tr').toggle( $(this).is(':checked') );
        }).change();
    });
</script>