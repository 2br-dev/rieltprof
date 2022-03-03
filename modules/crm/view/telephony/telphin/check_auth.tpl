{include file=$field->getOriginalTemplate()}
<br><a class="m-t-10 btn btn-default"
   id="telphin-check-auth"
   data-url="{adminUrl do=checkAuth mod_controller="crm-telphinctrl" provider=$field->provider->getId()}">{t}Проверить авторизацию{/t}</a>

<script>
    $(function() {
        $('#telphin-check-auth').click(function() {

            var appId = $('[name="telphin_app_id"]').val();
            var appSecret = $('[name="telphin_app_secret"]').val();

            $.ajaxQuery({
                method: 'POST',
                url: $(this).data('url'),
                data: {
                    telphin_app_id: appId,
                    telphin_secret_key:appSecret
                }
            });
        });
    })

</script>