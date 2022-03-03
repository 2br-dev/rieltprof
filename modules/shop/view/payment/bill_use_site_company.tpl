{include file=$elem.__use_site_company->getOriginalTemplate() field=$elem.__use_site_company}
<script type="text/javascript">
$(function() {
    var useChange = function() {
        $('input[name^="data[firm_"]').prop('disabled', $('input[name="data[use_site_company]"]').prop('checked') );
    }
    $('input[name="data[use_site_company]"]').change(useChange);
    useChange();
});
</script>