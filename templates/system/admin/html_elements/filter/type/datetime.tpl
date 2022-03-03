{addjs file="jquery.datetimeaddon/jquery.datetimeaddon.min.js"}
<span class="form-inline">
    <div class="input-group">
        <input type="text" name="{$fitem->getName()}" value="{$fitem->getValue()}" {$fitem->getAttrString()} datetime="datetime">
        <span class="input-group-addon"><i class="zmdi zmdi-calendar-note"></i></span>
    </div>
</span>