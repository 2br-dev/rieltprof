<div class="crm-user-block">
    {include file=$field->getOriginalTemplate()}
    <div class="crm-selected-user-id">
        {include file=$elem.__implementer_user_id->getOriginalTemplate() field=$elem.__implementer_user_id}
    </div>
    <script>
        $(function() {
            $('select[name="implementer_user_type"]').change(function() {
                $(this)
                    .closest('.crm-user-block')
                    .find('.crm-selected-user-id')
                    .toggle( $(this).val() == 'custom_user' );
            });
        });
    </script>
</div>