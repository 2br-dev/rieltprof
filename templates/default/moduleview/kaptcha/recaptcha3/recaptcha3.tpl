<input type="hidden" class="recaptcha-v3" id="{$unique_id}"
       name="{$name}"
       data-context="{$context}"
       {$attributes}>

<script>
    $(function() {
        $('#{$unique_id}').reCaptchaV3();
    });
</script>