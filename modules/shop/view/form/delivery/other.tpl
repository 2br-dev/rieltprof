{include file=$elem.__class->getOriginalTemplate() field=$elem.__class}
<script type="text/javascript">
    $(function() { 
        var updateTypeForm = function() {
            var type = $('select[name="class"]').val();
            $.ajaxQuery({
                url: '{$router->getAdminUrl("getDeliveryTypeForm")}',
                data: { type: type },
                success: function(response) {
                    $('#delivery-type-form').html(response.html);
                    $('body').trigger('new-content');
                }
            })
        }
        $('select[name="class"]').change(function() {
            updateTypeForm();
        });
    });
</script>
</td></tr>
<tbody id="delivery-type-form">
    {if $type_object = $elem->getTypeObject()}
        {include file="%shop%/form/delivery/type_form.tpl"}
    {/if}
</tbody>
<tr><td>