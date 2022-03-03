<p>{t}Подождите идёт перенаправление...{/t}</p>
<form id="printFormCdek" action="{$api_url}orders_print.php" method="POST">
    <textarea name="xml_request" style="display:none">{$xml}</textarea>
    <input type="submit" value="{t}Отправить{/t}" style="display:none"/>
</form>
<script type="text/javascript">
    $("#printFormCdek").submit();
    setTimeout('$(".ui-dialog-titlebar-close").click()',3000);
</script>