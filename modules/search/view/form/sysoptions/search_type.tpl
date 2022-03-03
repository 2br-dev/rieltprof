{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        $('[name="search_type"]').change(function() {
            let enable = $(this).val() == 'likeplus';
            $('[name="search_type_likeplus_ignore_symbols"]').each(function() {
                $(this).closest('tr').toggle(enable);
            });
        }).change();
    });
</script>