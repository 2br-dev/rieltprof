<span class="form-inline">
    <div class="input-group">
        <input type="text" name="{$field->getFormName()}" value="{$field->get()}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} datetime="datetime"/>
        <span class="input-group-addon"><i class="zmdi zmdi-calendar-note"></i></span>
    </div>
</span>