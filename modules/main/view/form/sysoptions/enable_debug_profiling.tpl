{include file=$elem.__ENABLE_DEBUG_PROFILING->getOriginalTemplate() field=$elem.__ENABLE_DEBUG_PROFILING}
<script>
    $(function() {
        $('[name="ENABLE_DEBUG_PROFILING"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name="LOG_QUERY_STACK_TRACE_LEVEL"]').each(function() {
                $(this).closest('tr').toggle(enable);
            });
        }).change();
    });
</script>