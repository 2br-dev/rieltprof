{$provider_test = $field->provider_object->getEventTestObject()}

<script type="text/javascript">
    $(function() {
        $('select[name="provider"]').change(function() {
            var provider = $(this).val();

            $.ajaxQuery({
                url: '{$router->getAdminUrl("getTestProviderForm")}',
                data: { provider: provider },
                success: function(response) {
                    $('#provider-test-form').html(response.html).trigger('new-content');
                }
            })
        });
    });
</script>

<div class="c-gray">{t}Заполнив форму ниже и нажав на кнопку Выполнить, вы можете эмитировать входящее событие от телефонии.{/t}</div></td></tr>
<tbody id="provider-test-form">
{if $provider_test}
    {include file="%crm%/telephony/tel_test_form.tpl"}
{/if}
</tbody>
<tr><td>