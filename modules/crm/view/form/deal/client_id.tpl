<div id="crm-client-guest" class="crm-client-tab{if $elem.client_type != 'guest'} hidden{/if}">
    {include file=$elem.__client_name->getOriginalTemplate() field=$elem.__client_name}
</div>

<div id="crm-client-user" class="crm-client-tab{if $elem.client_type != 'user'} hidden{/if}">
    {include file=$field->getOriginalTemplate()}
</div>

<script>
    $('input[name="client_type"]').change(function() {
        $('.crm-client-tab').addClass('hidden');
        $('#crm-client-'+$(this).val()).removeClass('hidden');
    });
</script>