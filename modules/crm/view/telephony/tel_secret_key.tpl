{include file=$field->getOriginalTemplate() field=$field}
<script>
    $(function() {

        $('input[name="tel_secret_key"]').change(function() {
            var value = $(this).val();

            $('[data-refresh-event-gate-url]').each(function() {
                var infoDiv = $(this);

                $.ajaxQuery({
                    method:'POST',
                    data: {
                        secret_key: value
                    },
                    url: $(this).data('refreshEventGateUrl'),
                    success: function(response) {
                        infoDiv.replaceWith(response.html);
                    }
                });
            });

        });
    });
</script>