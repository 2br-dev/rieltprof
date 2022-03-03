{include file=$elem.__is_clearfix_after->getOriginalTemplate() field=$elem.__is_clearfix_after}
<span id="clearfix-css" {if !$elem.is_clearfix_after}style="display:none"{/if}>CSS класс {include file=$elem.__clearfix_after_css->getOriginalTemplate() field=$elem.__clearfix_after_css}</span>
<script>
    $.allReady(function() {    
        //Управляет формами clearfix
        $('input[name="is_clearfix_after"]').change(function() {
            $('#clearfix-css').toggle( $(this).is(':checked') );
        })
    });
</script>