{include file=$elem.__allow_api_methods->getOriginalTemplate() field=$elem.__allow_api_methods}

<script type="text/javascript">
$(function() {
    $('[name="allow_api_methods[]"][value="all"]').change(function() {
        if ($(this).is(':checked')) {
            $('[name="allow_api_methods[]"]:not([value="all"])').prop('checked', false).prop('disabled', true);
        } else {
            $('[name="allow_api_methods[]"]:not([value="all"])').prop('disabled', false);
        }
    }).change();
});
</script>