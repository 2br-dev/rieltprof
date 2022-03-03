{addjs file="jquery.rs.objectselect.js" basepath="common"}

{$value = $fitem->getValue()}
<span class="form-inline">
    <select name="{$fitem->getName()}[provider]" {$fitem->getAttrString()}>
        {html_options options=$fitem->getList() selected=$value.provider}
    </select>
</span>

<span class="form-inline">
    <div class="input-group">
        <input type="text" data-name="{$fitem->getName()}[user_id]" {if $fitem->getValue()>0} value="{$fitem->getUserFio()}"{/if} {$fitem->getAttrString()} data-request-url="{$fitem->getRequestUrl()}" placeholder="{t}Email, фамилия, организация{/t}">
        {if $fitem->getValue()>0}<input type="hidden" name="{$fitem->getName()}[user_id]" value="{$value.user_id}">{/if}

        <span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
    </div>
</span>