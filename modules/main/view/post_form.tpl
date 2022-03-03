{* Выполняет POST запрос *}
<form method="POST" action="{$url}">
    {foreach $post_params as $key => $value}
    <input type="hidden" name="{$key}" value="{$value}">
    {/foreach}

    <input type="submit" value="{t}Продолжить{/t}" id="sub">
</form>
<script type="text/javascript">
    document.getElementById('sub').style.display = 'none';
    document.forms[0].submit();
</script>