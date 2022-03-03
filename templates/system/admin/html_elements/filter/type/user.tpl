{addjs file="jquery.rs.objectselect.js" basepath="common"}

<span class="form-inline">
    <div class="input-group">
        <input type="text" data-name="{$fitem->getName()}" {if $fitem->getValue()>0} value="{$fitem->getTextValue()}"{/if} {$fitem->getAttrString()} data-request-url="{$fitem->getRequestUrl()}" placeholder="{t}Email, фамилия, организация{/t}">
        {if $fitem->getValue()>0}<input type="hidden" name="{$fitem->getName()}" value="{$fitem->getValue()}">{/if}

        <span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
    </div>
</span>