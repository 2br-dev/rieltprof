{addjs file="jquery.rs.objectselect.js?v=1" basepath="common"}

<span class="form-inline">
    <div class="input-group">
        <input type="text" data-name="{$field->getFormName()}" class="object-select" {if !empty($field->get())} value="{$field->getPublicTitle()}"{/if} {$field->getAttr()} data-request-url="{$field->getRequestUrl()}">
        {if $field->get()>0}<input type="hidden" name="{$field->getFormName()}" value="{$field->get()}">{/if}
        <span class="input-group-addon"><i class="zmdi zmdi-{$field->getIconClass()}"></i></span>
    </div>
</span>